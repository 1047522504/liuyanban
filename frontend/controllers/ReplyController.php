<?php

namespace frontend\controllers;


use common\models\LikeIp;
use common\models\Message;
use common\models\Reply;
use common\models\User;
use Yii;
use yii\console\controllers\HelpController;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * Site controller
 */
class ReplyController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;

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
    //回复留言界面
    public function actionIndex()
    {

        $user_id = Yii::$app->request->get('user_id');
        $msg_id = Yii::$app->request->get('msg_id');

        return $this->render('reply.twig', [
            'user_id' => $user_id,
            'msg_id' => $msg_id
        ]);


    }


    //评论操作
    public function actionAdd()
    {

        $session_id = Yii::$app->session->get('__id');

        if($session_id){

            $reply = new Message();
            $msg_id = Yii::$app->request->post('msg_id');
            $content = Yii::$app->request->post('content');
            $msg_id = !empty($msg_id) ? intval($msg_id) : 0;
            $content = isset($content) ? trim($content) : "";
            $content = Html::encode($content);
            $content = addslashes($content);

            $content = $reply->getWords($content);

            if(strpos($content,'****') == false){

                $reply->user_id = $session_id;
                $reply->pid = $msg_id;
                $reply->title = "留言";
                $reply->content = $content;
                $reply->created_at = time();
                $res = $reply->save();

                if ($res) {
                    $arr = array('code' => 0, 'success' => '回复成功');
                    exit(json_encode($arr));

                } else {
                    $arr = array('code' => 1, 'error' => '回复失败');
                    exit(json_encode($arr));
                }
            }else{
                    $arr = array('code' => 3, 'msg' => '你的留言存在敏感字符');

                    exit(json_encode($arr));
            }


        }else{
            $arr = array('code' => 2, 'msg' => '请先登录以后在回复');
            exit(json_encode($arr));
        }




    }

}
