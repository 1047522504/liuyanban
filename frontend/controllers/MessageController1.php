<?php

namespace frontend\controllers;


use common\models\LikeIp;
use common\models\Message;
use common\models\MessageTest;
use common\models\Reply;
use common\models\User;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * Site controller
 */
class MessageController extends Controller
{
    public $enableCsrfValidation = false;
    public $num = 1;

    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {


        //总记录条数
        $count = $this->getCount();

        //每页显示条数
        $pageSize = 5;

        $pageNum = empty($_GET["pageNum"]) ? 1 : $_GET["pageNum"];
        //总页数
        $endPage = ceil($count / $pageSize);

        $result = $this->getMessageList(0, $pageNum, $pageSize);
        //上一页
        $pageStart = $pageNum == 1 ? 1 : $pageNum - 1;
        //下一页
        $pageEnd = $pageNum + 1;

        return $this->render('index.twig', [

            'data' => $result,
            'endPage' => $endPage,
            'pageStart' => $pageStart,
            'pageEnd' => $pageEnd


        ]);


    }




    //删除评论
    public function actionDelete()
    {

        $user_id = $_POST['user_id'];
        $msg_id = $_POST['msg_id'];

        if ($user_id == $_SESSION['__id']) {
            $model = Message::findOne($msg_id);
            $res = $model->delete();
            if ($res) {
                $arr = array('code' => 0, 'success' => '删除成功');
                json_encode($arr);
            }
        } else {
            $arr = array('code' => 1, 'error' => '你只能删除自己留言');
            json_encode($arr);
        }


    }

    //点赞
    public function actionLike()
    {

        if ($_SESSION['__id']) {

            $cookie_user_id = $_SESSION['__id'];
            $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;

            $msg_id = !empty($_POST['msg_id']) ? intval($_POST['msg_id']) : 0;
            //判断是否点过赞

            $query = LikeIp::find();
            $data = $query->where(['msg_id' => $msg_id, 'user_id' => $cookie_user_id])->all();


            if (!empty($data)) {
                $arr = array('code' => 0, 'success' => '你已经点过赞了');
                exit(json_encode($arr));
            } else {

                $like_ip = new LikeIp();
                $like_ip->user_id = $cookie_user_id;
                $like_ip->msg_id = $msg_id;
                $like_ip->created_at = time();
                $like_ip->save();

                $message = Message::find()->where(['msg_id' => $msg_id])->one();
                $message->like_num += 1;
                $message->created_at = time();
                $message->save();


                $arr = array('code' => 1, 'error' => '点赞成功');
                exit(json_encode($arr));

            }
        } else {
            $arr = array('code' => 3, 'msg' => '请登录以后在点赞！');
            exit(json_encode($arr));
        }

    }

    //留言操作
    public function actionAdd()
    {

        //首先判断是否登录
        if ($_SESSION['__id']) {
            $model = new Message();
            $user_id = $_SESSION['__id'];
            $title = isset($_POST['title']) ? trim($_POST['title']) : "";
            $message = isset($_POST['message']) ? trim($_POST['message']) : "";
            $message = str_replace("_", "\_", $message);
            $message = str_replace("%", "\%", $message);
            $message = strip_tags($message);
            $message = htmlspecialchars($message);
            $message = addslashes($message);
            $message = $model->getWords($message);


            $title = str_replace("_", "\_", $title);
            $title = str_replace("%", "\%", $title);
            $title = strip_tags($title);
            $title = htmlspecialchars($title);
            $title = addslashes($title);
            $title = $model->getWords($title);

            if(!strpos($message,'****') && !strpos($title,'****')){

                $model->title = $title;
                $model->content = $message;
                $model->user_id = $user_id;
                $model->created_at = time();
                $model->like_num = 0;
                $res = $model->save();

                if ($res) {
                    $arr = array('code' => 0, 'success' => '留言成功');

                    exit(json_encode($arr));

                } else {
                    $arr = array('code' => 1, 'error' => '留言失败');
                    exit(json_encode($arr));
                }
            }else{
                $arr = array('code' => 3, 'msg' => '你的留言或者标题存在敏感字符');
                exit(json_encode($arr));
            }



        } else {

            $arr = array('code' => 2, 'msg' => '请登录以后在留言！');
            exit(json_encode($arr));
        }
    }

    public function getMessageList($pid = 0, $pageNum = 1, $pageSize = 3)
    {

        //$message_list = Message::find()->innerJoinWith('user')->where(['pid'=>$pid])->orderBy(['msg_id'=>SORT_DESC])->limit(($pageNum - 1) * $pageSize,$pageSize)->asArray()->all();
        $sql = "SELECT * FROM message AS m INNER JOIN user AS u ON m.user_id=u.id WHERE m.pid='$pid' ORDER BY like_num DESC,msg_id DESC LIMIT " . (($pageNum - 1) * $pageSize) . "," . $pageSize;
        $message_list = Message::findBySql($sql)->asArray()->all();

        $result = [];
        foreach ($message_list as $key => $value) {

            $data = [];
            $data['msg_id'] = $value['msg_id'];
            $data['user_id'] = $value['user_id'];
            $data['pid'] = $value['pid'];
            $data['title'] = $value['title'];
            $data['content'] = $value['content'];
            $data['username'] = $value['username'];
            $data['like_num'] = $value['like_num'];
            $data['created_at'] = $value['created_at'];
            $data['child'] = $this->getMessageList($value['msg_id']);
            $result[] = $data;
        }

        return $result;
    }


    public function getCount()
    {

        return Message::find()->where(['pid' => 0])->count();
    }


    public function actionRedis(){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $redis = Yii::$app->redis;
        $msg_id = !empty($_POST['msg_id']) ? intval($_POST['msg_id']) : 0;
        $cookie_user_id = $_SESSION['__id'];
        $bool=$redis->exists($msg_id.'log');
        $max_length=3;
        $time=1;
        $data=json_encode(['user_id'=>$cookie_user_id,'msg_id'=>$msg_id,'created'=>time()],true);

        if(!$bool){

            //$length=$redis->llen($msg_id.'log');


                $redis->lpush($msg_id . 'log', $data);

                //插入成功
                if (!$redis->exists($msg_id . 'like_num')) {
                    //执行sql查询content likes
                    $likes = $this->redis_likes($msg_id);
                    //进行点赞了,likes+1
                    $redis->set($msg_id . 'like_num', $likes + 1);
                } else {
                    $redis->incr($msg_id . 'like_num');
                }
                $likes = $redis->get($msg_id . 'like_num');


                //事务,log content 入库
                $arr = [];
                for ($i=0;$i<$max_length;$i++){
                    $data=$redis->rpop($msg_id.'log');
                    $arr=json_decode($data,JSON_UNESCAPED_UNICODE);
                }

                $redis->lpush($msg_id.'log',$data);
                $transaction=Yii::$app->db->beginTransaction(\yii\db\Transaction::SERIALIZABLE);
                try{

                    $sql="select like_num from message where msg_id=:msg_id for update";
                    $info=Yii::$app->db->createCommand($sql)->bindValue(':msg_id',$msg_id)->queryOne();
                    //现在点赞的人数 +1 因 进行一次请求
                    $likes=$info['like_num']+$max_length+1;
                    /*点赞量入库*/
                    $sql="update message set like_num=like_num+$max_length WHERE msg_id=:msg_id";

                    Yii::$app->db->createCommand($sql)->bindValue(':msg_id',$msg_id)->execute();
                    //此时数据库 与缓存是一致的
                    $redis->set($msg_id.'like_num',$likes);

                    /*log入库*/
                    $bool=Yii::$app->db->createCommand()->batchInsert(LikeIp::tableName(),['user_id','msg_id','created_at'],$arr)->execute();
                    //var_dump($bool);die;
                    if ($bool==0){
                        throw new Exception('点赞日志入库失败');
                    }
                    $transaction->commit();


                    $array = array('code' => 1, 'error' => '点赞成功');
                    exit(json_encode($array));

                }catch (\PDOException $exception){

                    $transaction->rollBack();
                    return '点赞失败'.$exception->getMessage();
                }



            $redis->set($msg_id.$cookie_user_id,'','ex',$time);


        }else{

            $likes=$redis->get($msg_id.'like_num');

            if (empty($likes)){
                $likes=$this->redis_likes($msg_id);
            }

            $array = array('code' => 0, 'success' => '你已经点过赞了');
            exit(json_encode($array));
        }


        }



    public function redis_likes($msg_id){
        //执行sql查询content likes
        $likes=Message::find()->where(['msg_id'=>$msg_id])->select('like_num')->asArray()->one()['like_num'];
        //进行点赞了,likes+1
        return $likes;
    }


    public function actionTest(){
        $msg_id=65;

        $data=$this->redis_likes($msg_id);
        var_dump($data);
    }





}
