<style>


    .appLink:hover {
        background-color: #2accab;
        outline: none;
    }

</style>

<?php echo $header; ?>
<!--left menu begin-->
<div class="span2">
    <?php if ($apps): ?>
        <ul class="nav nav-list bs-docs-sidenav affix">
            <?php foreach ($apps as $app): ?>
                <li class=""><a href="#appItem-<?php echo $app->id; ?>"><i
                            class="icon-chevron-right"></i> <?php echo $app->name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<!--right menu end-->
<div class="span10">
    <div class="row-fluid" style="margin-bottom: 10px;">
        <div class="span-12">
            <div class="pull-right">
                <?php
                if ($enable != 'false') {
                    echo '<a href="' . URL::action('DashBoardController@addApp') . '" class="btn btn-small btn-primary"type="button">添加应用</a>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php if ($apps):foreach ($apps as $app): ?>
        <section id="appItem-<?php echo $app->id; ?>">
            <!--app item begin-->
            <div class="row-fluid item">

                <div class="span6 item item-team">
                    <a style="text-decoration: none; color: #34495e"
                       href="<?php echo URL::action('CmsController@index', array('app_id' => $app->id)) ?>">
                        <div class="appTitle appLink">
                            <h4>
                                <?php echo $app->name; ?>
                            </h4>
                        </div>
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
                </div>


                <div class="span6 item item-member">
                    <div class="appTitle appUserTitle">
                        <h4>项目成员</h4>
                    </div>
                    <div class="item-member-body">
                        <?php if ($app->appUser): ?>
                            <ul class="member">
                                <?php foreach ($app->appUser as $user): ?>
                                    <?php $user = User::find($user->user_id);
                                    if ($user && $user->exists): ?>
                                        <li>
                                            <a title="<?php echo $user->name; ?>" class="handle-members"
                                               data-toggle="modal" data-target="#myModal"
                                               data-header-title="移除成员" data-footer-title="移除"
                                               data-app-id="<?php echo $app->id; ?>"
                                               data-user-id="<?php echo $user->id; ?>"
                                               type="button" href="#" class="show-member">
                                                <span class="JAvatar"><?php echo md5($user->id); ?></span>
                                            </a>
                                            <a href="javascript:;" class="member-name"
                                               title="<?php echo $user->name; ?>"><?php echo $user->name; ?></a>
                                        </li>
                                    <?php endif; endforeach; ?>
                                <?php if ($app->user_id == Auth::user()->id || (int)$appIds[$app->id] === Limit::ROLE_SUPER): ?>
                                    <li>
                                        <a class="btn btn-primary handle-members plus-members"
                                           style="padding-top: 12px; padding-bottom: 12px;" data-toggle="modal"
                                           data-target="#myModal"
                                           data-header-title="增加成员" data-footer-title="增加"
                                           data-app-id="<?php echo $app->id; ?>"
                                           data-user-id="" type="button">
                                            <span class="fui-plus"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if ($app->Author === Auth::user()->name): ?>
                <div class="row-fluid" style="margin-bottom: 10px;">
                    <div class="span-12">
                        <div class="pull-right">
                            <a href="<?php echo URL::action('DashBoardController@editApp', array('app_id' => $app->id)) ?>"
                               class="btn btn-small" type="button">修改应用</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!--app item end-->
        </section>
    <?php endforeach;endif; ?>


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">增加成员</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer" style="text-align:center">
                    <button type="button" class="btn btn-default btn-member" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary btn-member btn-member-handle default-member"></button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('.item-member-body').hover(function () {
            //0 2px 0 #fff,0 -2px 0 #f2f2ea
//          $('.item-member-body').style.backgroundColor = ' #f2f2ea';
            $('.item-member-body').css('backgroundColor', '#000000');
        });


        $(function () {
            $('.handle-members').click(function () {
                app_id = $(this).attr('data-app-id');
                user_id = $(this).attr('data-user-id');

                $('.btn-member-handle').prop('disabled', false);
                selector = $(this).parent().nextAll().andSelf();
                selector.each(function () {
                    if ($(this).children().hasClass('plus-members')) {
                        $('.btn-member-handle').prop('disabled', false);
                        if (!$('.btn-member-handle').hasClass('btn-primary')) {
                            $('.btn-member-handle').addClass('btn-primary')
                        }
                    } else {
                        $('.btn-member-handle').removeClass('btn-primary').prop('disabled', true);
                    }
                });

                if (user_id == <?php echo Auth::user()->id; ?>) {
                    $('.btn-member-handle').prop('disabled', true);
                    $('.btn-member-handle').removeClass('btn-primary')
                }


                $('.modal-title').text($(this).attr('data-header-title'));
                $('.modal-footer .btn-member-handle').text($(this).attr('data-footer-title')).removeClass('default-member').addClass('store-member');
                $.get("<?php echo URL::action('DashBoardController@addMember') ?>", {'app_id': app_id, 'user_id': user_id}).done(function (data) {


                    $('.modal-body').html(data.addMembers);
                    $('.store-member').off('click');
                    $('.store-member').click(function (event) {
                        user_ids = $("#user_ids").val();
                        post_param = ['<?php echo URL::action("DashBoardController@storeMember")?>', {'user_ids': user_ids, 'app_id': app_id}];

                        if (user_ids === undefined) {
                            post_param = $.post('<?php echo URL::action("DashBoardController@delete")?>', {'user_id': user_id, 'app_id': app_id});
                        } else {
                            post_param = $.post('<?php echo URL::action("DashBoardController@storeMember")?>', {'user_ids': user_ids, 'app_id': app_id});
                        }

                        post_param.done(function (data) {
                            location.reload();
                        }).error(function () {
                                alert('something wrong!');
                            });
                    });
                }).error(function () {
                        alert('something wrong!');
                    });
            });
        });

        $('.item-member-body').hover(function () {
            $('.item-member-body').css('backgroundColor', '#000000');
        });
    </script>
<?php echo $footer; ?>