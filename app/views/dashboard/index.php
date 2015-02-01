<style>
    .appLink:hover {
        background-color: #2accab;
        outline: none;
    }

    .feedback-input {
        color: #3c3c3c;
        font-family: Helvetica, Arial, sans-serif;
        font-weight: 500;
        font-size: 18px;
        border-radius: 0;
        line-height: 22px;
        background-color: #fbfbfb;
        padding: 13px 13px 13px 54px;
        width: 100%;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
        border: 3px solid #3AC1C7;
        margin-top: 20px;
        margin-bottom: 20px;
    }

</style>

<?php echo $header; ?>
<!--left menu begin-->

<div class="col-md-2 sidebar-nav">
    <?php if ($apps): ?>
        <ul class="nav pow_sidebar">
            <?php foreach ($apps as $app): ?>
                <li class=""><a href="<?php echo URL::action('CmsController@index', array('app_id' => $app->id)) ?>"><i
                            class="glyphicon glyphicon-chevron-right"></i> <?php echo $app->name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<!--right menu end-->
<div class="col-md-10" style="padding-bottom:20px">
    <div class="" style="height:30px;"></div>
    <?php if ($enable != 'true'): ?>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <strong>提示!</strong> 请先激活账户,若没有收到邮件可以查阅垃圾箱 或者点用户进入个人中心重新发送邮件
        </div>
    <?php endif; ?>
    <div class="row" style="margin-bottom: 10px;">
        <div class="span-12">
            <div class="pull-right">
                <?php
                if ($enable != 'false') {
                    echo '<a href="' . URL::action('DashBoardController@addApp') . '" class="btn btn-sm btn-primary"type="button">添加应用</a>';

                }   ?>
            </div>
        </div>
    </div>
    <?php if ($apps):foreach ($apps as $app): ?>
        <section id="appItem-<?php echo $app->id; ?>">
            <!--app item begin-->
            <div class="item clearfix">
                <div class="col-md-6">
                    <a class="clearfix pow_item_title " style="margin:10px 0;
                    <?php  if ($app->type == 'self') {
                        echo ' background-color:#397B8F';
                    }?>;"
                       href="<?php echo URL::action('CmsController@index', array('app_id' => $app->id)) ?>">
                        <h4>
                            <?php echo $app->name; ?>
                        </h4>

                    </a>

                    <div class="appInfo  <?php if ($app->user_id !== Auth::user()->id) {
                        echo "noInfo";
                    } ?> ">
                        <?php if ($app->user_id == Auth::user()->id): ?>
                            <blockquote>
                                <p class="lead">APP Info</p>

                                <p>
                                    APP ID :<?php echo $app->id ?>
                                </p>

                                <p>
                                    APP token
                                    :<?php echo \Utils\UseHelper::makeToken($app->id, \Utils\UseHelper::$default_key) ?>
                                </p>
                            </blockquote>
                        <?php endif; ?>
                    </div>

                    <?php if ($app->Author === Auth::user()->name): ?>
                        <a href="<?php echo URL::action('DashBoardController@editApp', array('app_id' => $app->id)) ?>"
                           class="btn btn-primary btn-x" type="button">修改应用</a>
                    <?php endif; ?>
                    <?php if ($app->type == 'self' && $app->init != 1): ?>
                        <button
                            class=" btn btn-info btn-x init-btn" type="button" data-title="<?php echo $app->id ?>">初始化数据
                        </button>
                    <?php endif; ?>
                    <div class="mt20"></div>
                </div>

                <div class="col-md-6">
                    <div class="appTitle appUserTitle">
                        <h4>项目成员</h4>
                    </div>
                    <div class="item-member-body">
                        <?php if ($app->appUser): ?>
                            <ul class="member pow_member clearfix">
                                <?php foreach ($app->appUser as $user): ?>
                                    <?php $user = User::find($user->user_id);
                                    if ($user && $user->exists): ?>
                                        <li class="fl">
                                            <a title="<?php echo $user->name; ?>" class="user_info"

                                               class="show-member">
                                                <!-- <span class="JAvatar"><?php echo md5($user->id); ?></span>-->
                                                <span class="JAvatar pow_user_letter">l</span>
                                            </a>
                                            <a href="javascript:;" class="member-name"
                                               title="<?php echo $user->name; ?>"><?php echo $user->name; ?></a>
                                        </li>
                                    <?php endif; endforeach; ?>
                                <?php if ($app->user_id == Auth::user()->id || (int)$appIds[$app->id] === Limit::ROLE_SUPER): ?>
                                    <li>
                                        <a class="btn btn-primary  handle-members  plus-members"
                                           style="padding-top: 12px; padding-bottom: 12px;" data-toggle="modal"
                                           data-target="#myModal" data-app-id="<?php echo $app->id; ?>">
                                            <span class="fui-plus">+</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mt20"></div>
        </section>
    <?php endforeach;endif; ?>


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">随我闯江湖 title</h4>
                </div>
                <div class="modal-body">
                    <div class="jumbotron">
                        <h2>APP权限设置</h2>
                        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
                        <form class="form-horizontal" method="post" id="invite">
                            <fieldset>
                                <p>
                                    <input class="feedback-input" name="email" id="email_input" placeholder="小伙伴的邮箱地址"/>
                                    <input class="feedback-input" name="app_id" id="app_input" value="" type="hidden"/>
                                    <input class="feedback-input" name="action" id="user_action" value=""
                                           type="hidden"/>
                                </p>
                                <?php echo \Utils\FormBuilderHelper::staticEnd('invite',
                                    array( //表单规则
                                        'email' => array('required' => true),
                                    ),
                                    URL::action('UserMessageController@invite'),
                                    'POST'
                                );//注册表单JS
                                ?>
                                <p>
                                    <button id="JS_Sub" class="btn btn-primary btn-lg"></button>
                                </p>
                            </fieldset>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(function () {
            $('.init-btn').click(function () {
                var btn = $(this);
                var app_id = $(this).attr('data-title');
                btn.button('检测中');
                $.ajax({
                    type: "get",
                    data: {id: app_id},
                    url: "<?php echo URL::action('DashBoardController@initDB'); ?>"
                }).success(function (re) {
                        re = $.parseJSON(re);
                        if (re.status) {
                            var errors = re.data;
                            if (!errors) {
                                errors = re.message
                            }
                            alert(errors);
                        }
                    })
                    .done(function () {
                        btn.hidden();
                    });
            });

            $('.handle-members').click(function () {
                $('#JS_Sub').attr('disabled', false);
                app_id = $(this).attr('data-app-id');
                user_id = $(this).attr('data-user-id');
                $('#app_input').val(app_id);
                $('#user_action').val(<?php echo UserMessage::ACTION_INVITE?>);
                $('#JS_Sub').text("发送邀请");
                $('#email_input').show();
                //change html contnet

            });
            $('.user_info').click(function () {
                $('#JS_Sub').attr('disabled', false);
                app_id = $(this).attr('data-app-id');
                user_id = $(this).attr('data-user-id');
                $('#app_input').val(app_id);
                $('#user_action').val(<?php echo UserMessage::ACTION_REMOVE?>);
                $('#JS_Sub').text("去除权限");
                $('#email_input').hide();
                //change title and html content
            });

        });
    </script>

</div>

<?php echo $footer; ?>