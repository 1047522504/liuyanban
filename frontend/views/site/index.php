<?php

$this->title = '我的留言板';
?>
<link rel="stylesheet" href="/frontend/web/css/style.css?t=<?php echo time();?>">
<div class="bodywrap">
    <div class="headernav">
        <img src="/frontend/web/img/banner.jpg" alt="">
        <div>
            <h2>留言版</h2>
            <p></p>
        </div>
    </div>

    <div class="msgwrap">
        <div class="listwrap">

            <div class="msglist">
                <h3>关于这个留言<em><div class="zan" data-id="{$v.user_id}" data-msg="{$v.msg_id}"  id="{$v.msg_id}">

                            <img src="/frontend/web/img/dian.jpg" class="zan_img" style="width: 20px;height: 20px;" >
                            <span class="zan_num">(<span class="num">5</span>)</span>

                        </div>
                    </em>


                </h3>
                <p>我的留言</p>

                <p class="replyc"><span> 2019-16-15 15:15：15回复：</span>回复内容</p>

                <p>
                    <a href="javascript:;" class="reply" data-title="回复留言" data-url="{$config.siteurl}/?a=reply&id={$val['id']}">回复</a>
                    <a href="javascript:;" class="reply edit" data-title="修改留言" data-url="{$config.siteurl}/?a=edit&id={$val['id']}">修改</a>
                    <a href="javascript:;" data-msg="确定删除该留言吗？" class="ajaxLink del" data-url="index.php?a=delete&id={$val.id}">删除</a>
                </p>
            </div>

            <div class="msglist">
                <h3>关于这个留言<em><div class="zan" data-id="{$v.user_id}" data-msg="{$v.msg_id}"  id="{$v.msg_id}">

                            <img src="/frontend/web/img/dian.jpg" class="zan_img" style="width: 20px;height: 20px;" >
                            <span class="zan_num">(<span class="num">5</span>)</span>

                        </div>
                    </em>


                </h3>



                <p>我的留言</p>

                <p class="replyc"><span> 2019-16-15 15:15：15回复：</span>回复内容</p>

                <p>
                    <a href="javascript:;" class="reply" data-title="回复留言" data-url="{$config.siteurl}/?a=reply&id={$val['id']}">回复</a>
                    <a href="javascript:;" class="reply edit" data-title="修改留言" data-url="{$config.siteurl}/?a=edit&id={$val['id']}">修改</a>
                    <a href="javascript:;" data-msg="确定删除该留言吗？" class="ajaxLink del" data-url="index.php?a=delete&id={$val.id}">删除</a>
                </p>
            </div>

        </div>
        <div class="msgform">
            <form action="" method="post" class="ajaxForm">
                <p><input type="text" name="title" id="title" placeholder="留言主题"/></p>
                <p><textarea name="content" id="content" cols="30" rows="10" placeholder="留言内容"></textarea></p>
                <p><button id="subBtn">提交</button></p>
            </form>
        </div>

    </div>

    <script type="text/javascript" src="/frontend/web/js/jquery.min.js"></script>
    <script type="text/javascript" src="/frontend/web/js/jquery.form.min.js"></script>
    <script type="text/javascript" src=/frontend/web/js/common.js?t=<?php echo time();?>"></script>