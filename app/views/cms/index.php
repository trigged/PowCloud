<?php echo $header; ?>

<link href="<?php echo URL::asset('css/style.css'); ?>" rel="stylesheet"/>

<style>
    div span.label > a:link {
        color: #ffffff
    }

    div span.label > a:hover {
        color: #1e347b
    }

    span.label-danger {
        background-color: #f89406
    }
</style>

<script>
    $(window).on('beforeunload', function () {
        $('.btn-syncDataForm').text('正在' + $('.btn-syncDataForm').text() + '中')
    })

    $(function () {
        var timer = setInterval(function () {
            handleProcessBar();
        }, 200);

        function handleProcessBar() {
            $.ajax('<?php echo URL::action('ExtController@processBar') ?>')
                .done(function (data) {
                    $('.feeds').html(data.html);
                    $('.check-epg-number').html(data.video_check_count + '条');
                    if (data.video_check_state == 'end' || data.video_check_state == 'ok' || data.video_check_state == '') {
                        $('.check-epg').css('display', 'block');
                        $('.span-refresh').css('display', 'none');
                        clearInterval(timer);
                    } else {
                        $('.check-epg').css('display', 'none');
                        $('.span-refresh').css('display', 'block');
                    }
                })
        }
    });
</script>

<?php

function get_greeting()
{
    $hour = date('H');
    if ($hour >= 18) {
        echo '晚上好~!';
    } elseif ($hour >= 14) {
        echo '下午好~';
    } else if (12 <= $hour && $hour < 14) {
        echo '中午好~';
    } else if (6 <= $hour && $hour < 14) {
        echo '上午好~';
    } else if ($hour < 6) {
        echo '我勒个去,半夜你还用此系统~给跪了';
    }
}

if ($options === false || (isset($options['no_right']) && $options['no_right'])) {

if ($roles === 3) :?>
<div class="note note-warning">
<h4 class="block"> <?php get_greeting(); ?></h4>

<p>:( 很遗憾目前你还看不到任何数据,你可以在权限管理中设置你的具体权限</p>

<?php else:
?>



<div class="note note-warning">
    <h4 class="block"> <?php get_greeting(); ?></h4>

    <p>:( 很遗憾目前你还看不到任何数据,你可以联系管理员为你开通权限</p>

    <?php
    endif;
    } else {
    echo ' <div class="note note-success"> <h4 class="block">';
    get_greeting();
    echo '</h4>';

    ?>
    <?php
    if (isset($user['last_time']) && ($user['last_time'])) {
        echo '<p> &nbsp;&nbsp;&nbsp;&nbsp;你最近一次录时间在:' . $user['last_time'] . '</p>';
    }
    if (isset($user['last_area']) && ($user['last_area'])) {
        echo '<p> &nbsp;&nbsp;&nbsp;&nbsp;你最近一次登录地点在:' . $user['last_area'] . '</p>';
    }
    ?>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://p.demo1.pptv.com/w/pptv_launcher/xcmsguide/"
                                  target="_blank">CMS后台操作指南</a></p>

    <p>&nbsp;&nbsp;&nbsp;<code>若登录异常,请及时修改域密码并且联系管理员</code></p>
</div>

    <div class="row-fluid">
        <div class="span3" style="position: relative;">
            <div class="dashboard-stat blue">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <?php echo $timing_count; ?>条
                    </div>
                    <div class="desc">
                        定时数据
                    </div>
                </div>
                <a class="more" href="#" style="height: 10px">
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
        <div class="span3" style="position: relative;">
            <div class="dashboard-stat green">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="number check-epg-number">
                    </div>
                    <div class="desc">EPG 差异数据</div>
                </div>
                <a class="more" href="#" style="height: 10px">
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
        <div class="span3" style="position: relative;">
            <div class="dashboard-stat purple">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number"></div>
                    <div class="desc">先占位</div>
                </div>
                <a class="more" href="#" style="height: 10px">
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
        <div class="span3" style="position: relative;">
            <div class="dashboard-stat yellow">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number"></div>
                    <div class="desc">先占位</div>
                </div>
                <a class="more" href="#" style="height: 10px">
                    <i class="m-icon-swapright m-icon-white"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <div class="portlet box blue ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-check"></i>
                        定时数据
                    </div>
                    <div class="actions">
                        <div class="btn-group">

                        </div>
                    </div>
                </div>
                <div class="portlet-body" style="overflow: auto; height:200px">
                    <div class="slimScrollDiv" style="">
                        <div class="scroller" style="">
                            <table class="table table-hover timing-data">
                                <thead>
                                <tr style="color:#689af1">
                                    <td>表名</td>
                                    <td>类型</td>
                                    <td>标题</td>
                                    <td>时间</td>
                                </tr>
                                </thead>
                                <?php
                                if (isset($timing_data)) {
                                    foreach ($timing_data as $data) {
                                        if (count($data) === 2) {
                                            $value = \Operator\ReadApi::getTimingInfo($data[0]);
                                            $value = explode('::', $value);
                                            if (count($value) === 4) {
                                                $timing_type = (int)$value[0];
                                                $timing_table = $value[1];
                                                $timing_id = $value[2];
                                                $timing_title = $value[3];
                                                $table_info = \Operator\ReadApi::getTableInfo($timing_table);

                                                $time = $data[1];
                                                ?>
                                                <tbody>
                                                <tr data-url="<?php echo URL::action('CmsController@edit', array('table' => $table_info['id'], 'cms' => $timing_id)) ?>">
                                                    <td><?php echo $table_info['table_alias']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($timing_type === \Operator\RedisKey::HAS_PUB_OFFLINE) {
                                                            echo '定时下线';
                                                        } elseif ($timing_type === \Operator\RedisKey::HAS_PUB_FIRST) {
                                                            echo '定时顶置';
                                                        } elseif ($timing_type === \Operator\RedisKey::HAS_PUB_ONLINE) {
                                                            echo '定时上线';
                                                        }?>
                                                    </td>
                                                    <td><?php echo $timing_title; ?></td>
                                                    <td><?php echo date('Y-m-d H:i:s', $time); ?></td>
                                                </tr>
                                                </tbody>
                                            <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="span6">
            <div class="portlet box green tasks-widget">
                <div class="portlet-title">
                    <div class="caption timing-data">
                        <div class="check-epg">
                            <i class="fa fa-bell-o"></i>
                            EPG 差异数据 &nbsp; &nbsp; &nbsp; &nbsp;
                            <a href="#" data-target="#form" data-toggle="modal" class="synchroData" data-label="同步"
                               data-url="<?php echo URL::action('ExtController@synchroData'); ?>"><span
                                    class="label label-info"
                                    style="font-size: 14px;color:  #000000;background-color: #FFFFFF">自动同步</span></a>
                            &nbsp; &nbsp;
                            <a href="#" data-target="#form" data-toggle="modal" class="refreshTimingCheck"
                               data-label="刷新"
                               data-url="<?php echo URL::action('ExtController@refreshTimingCheck'); ?>">
                                    <span class="refresh label label-info"
                                          style="font-size: 14px;color:  #000000;background-color: #FFFFFF">刷新</span>
                            </a>
                        </div>
                        <div class="span-refresh" style="display:none;min-height:18px">
                            <i class="fa fa-bell-o"></i>
                            正在刷新中 &nbsp; &nbsp; &nbsp; &nbsp;
                        </div>
                    </div>
                    <div class="action"></div>
                </div>
                <div class="portlet-body" style="overflow: auto; height:200px">
                    <div class="slimScrollDiv" style="">
                        <div class="scroller" style="">
                            <table class="table table-hover feeds">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<?php
}
echo $footer; ?>
