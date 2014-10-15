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
        <p>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://doc.powapi.com/"
                                      target="_blank">CMS后台操作指南</a></p>

        <p>&nbsp;&nbsp;&nbsp;<code>若登录异常,请及时修改域密码并且联系管理员</code></p>
    </div>

    <div class="row">
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





<?php
}
echo $footer; ?>
