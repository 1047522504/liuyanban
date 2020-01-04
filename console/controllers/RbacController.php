<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

class RbacController extends Controller
{
    public function actionInit()
    {


        $auth = new DbManager();

        // 添加 "createMessage" 权限
        $createMessage = $auth->createPermission('createMessage');
        $createMessage->description = '添加留言';
        $auth->add($createMessage);

        // 添加 "updateMessage" 权限
        $updateMessage = $auth->createPermission('updateMessage');
        $updateMessage->description = '修改留言';
        $auth->add($updateMessage);

        // 添加 "deleteMessage" 权限
        $deleteMessage = $auth->createPermission('deleteMessage');
        $deleteMessage->description = '删除留言';
        $auth->add($deleteMessage);



        // 添加 "messageAdmin" 角色并赋予 "updateMessage" "deleteMessage" 权限
        $messageAdmin = $auth->createRole('messageAdmin');
        $messageAdmin->description = "留言管理员";
        $auth->add($messageAdmin);
        $auth->addChild($messageAdmin, $updateMessage);
        $auth->addChild($messageAdmin, $deleteMessage);
        $auth->addChild($messageAdmin, $createMessage);


        // 添加 "messageAdminDelete" 角色并赋予 "deleteMessage" 权限
        $messageAdminDelete = $auth->createRole('messageAdminDelete');
        $messageAdminDelete->description = "留言操作员";
        $auth->add($messageAdminDelete);
        $auth->addChild($messageAdminDelete, $deleteMessage);


        // 添加 "admin" 角色并赋予 "updatePost"
        // 和 "author" 权限
        $admin = $auth->createRole('admin');
        $admin->description="系统管理员";
        $auth->add($admin);
        $auth->addChild($admin, $messageAdmin);
        $auth->addChild($admin, $messageAdminDelete);

        // 为用户指派角色。其中 1 和 2 是由 IdentityInterface::getId() 返回的id
        // 通常在你的 User 模型中实现这个函数。

        $auth->assign($admin, 1);
        $auth->assign($messageAdmin, 2);
        $auth->assign($messageAdminDelete, 3);

        echo "finish";
    }
}