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
use yii\helpers\Html;
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

        $pageNum = Yii::$app->request->get('pageNum');

        $pageNum = empty($pageNum)? 1 : $pageNum;
        //总记录条数
        $count = $this->getCount();
        //每页显示条数
        $pageSize = 5;
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

    //留言操作
    public function actionAdd()
    {
        $session_id = Yii::$app->session->get('__id');
        //首先判断是否登录
        if (!empty($session_id)) {
            $model = new Message();
            $title = Yii::$app->request->post('title');
            $message = Yii::$app->request->post('message');
            $title = isset($title) ? trim($title) : "";
            $message = isset($message) ? trim($message) : "";

            $message = Html::encode($message);
            $message = addslashes($message);
            $message = $model->getWords($message);

            $title = Html::encode($title);
            $title = addslashes($title);
            $title = $model->getWords($title);

            if (strpos($message, '****') == false && strpos($title, '****') == false) {

                $model->title = $title;
                $model->content = $message;
                $model->user_id = $session_id;
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
            } else {
                $arr = array('code' => 3, 'msg' => '你的留言或者标题存在敏感字符');
                exit(json_encode($arr));
            }


        } else {
            $arr = array('code' => 2, 'message' => '请登录以后在留言！');
            exit(json_encode($arr));
        }
    }

    public function getMessageList($pid = 0, $pageNum = 1, $pageSize = 3)
    {


        $sql = "SELECT * FROM message WHERE pid='$pid' GROUP BY content ORDER BY like_num DESC,msg_id DESC LIMIT " . (($pageNum - 1) * $pageSize) . "," . $pageSize;

        $message = Yii::$app->db->createCommand($sql)->queryAll();


        $userId = array();
        $da = array();

        foreach ($message as &$v){

            $userId[] = $v['user_id'];
        }

        $user = User::find()->select(['id','username'])->where(['in','id',$userId])->asArray()->all();

        foreach ($user as &$v) {
            $da['username'] = $v;

        }

        foreach ($message as &$v) {
            $v['username'] = $da['username']['username'];
        }


        $result = [];
        foreach ($message as $key => $value) {

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




    //获取总条数
    public function getCount()
    {

        return Message::find()->where(['pid' => 0])->groupBy(['content'])->count();
    }

    //redis点赞
    public function actionRedis()
    {

        $session_id = Yii::$app->session->get('__id');

        if (!empty($session_id)){

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $redis = Yii::$app->redis;
            $msg_id = Yii::$app->request->post('msg_id');
            $msg_id = !empty($msg_id) ? intval($msg_id) : 0;
            $bool = $redis->exists($msg_id . 'log');

            $data = json_encode(['user_id' => $session_id, 'msg_id' => $msg_id, 'created' => time()], true);

            if (!$bool) {

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

                $data = $redis->rpop($msg_id . 'log');

                $arr[] = json_decode($data, JSON_UNESCAPED_UNICODE);

                $redis->lpush($msg_id . 'log', $data);
                $transaction = Yii::$app->db->beginTransaction(\yii\db\Transaction::SERIALIZABLE);
                try {

                    $sql = "SELECT like_num FROM message WHERE msg_id=:msg_id FOR UPDATE";
                    $info = Yii::$app->db->createCommand($sql)->bindValue(':msg_id', $msg_id)->queryOne();
                    //现在点赞的人数 +1 因 进行一次请求
                    $likes = $info['like_num'] + 1;
                    /*点赞量入库*/
                    $sql = "UPDATE message SET like_num=like_num+1 WHERE msg_id=:msg_id";

                    Yii::$app->db->createCommand($sql)->bindValue(':msg_id', $msg_id)->execute();
                    //此时数据库 与缓存是一致的
                    $redis->set($msg_id . 'like_num', $likes);

                    /*log入库*/
                    $bool = Yii::$app->db->createCommand()->batchInsert(LikeIp::tableName(), ['user_id', 'msg_id', 'created_at'], $arr)->execute();
                    //var_dump($bool);die;
                    if ($bool == 0) {
                        throw new Exception('点赞日志入库失败');
                    }
                    $transaction->commit();


                    $array = array('code' => 1, 'error' => '点赞成功');
                    exit(json_encode($array));

                } catch (\PDOException $exception) {

                    $transaction->rollBack();
                    return '点赞失败' . $exception->getMessage();
                }

            } else {

                $likes = $redis->get($msg_id . 'like_num');

                if (empty($likes)) {
                    $likes = $this->redis_likes($msg_id);
                }

                $array = array('code' => 0, 'success' => '你已经点过赞了');
                exit(json_encode($array));
            }

        }else{
            $arr = array('code' => 2, 'message' => '请登录以后再点赞！');
            exit(json_encode($arr));
        }



    }


    public function redis_likes($msg_id)
    {

        $likes = Message::find()->where(['msg_id' => $msg_id])->select('like_num')->asArray()->one()['like_num'];
        return $likes;
    }


}
