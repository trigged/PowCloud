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

        </div>


    <?php else: ?>

        <div class="note note-warning">
            <h4 class="block"> <?php get_greeting(); ?></h4>

            <p>:( 很遗憾目前你还看不到任何数据,你可以联系管理员为你开通权限</p>
        </div>
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
                                  target="_blank">PowCloud 后台操作指南</a></p>

    <p>&nbsp;&nbsp;&nbsp;<code>若登录异常,请及时修改域密码并且联系管理员</code></p>
    </div>

    <div class="clearfix">
        <div class="col-md-3" style="position: relative;">
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


        <!--        <div class="col-md-3" style="position: relative;">-->
        <!--            <div class="dashboard-stat purple">-->
        <!--                <div class="visual">-->
        <!--                    <i class="fa fa-globe"></i>-->
        <!--                </div>-->
        <!--                <div class="details">-->
        <!--                    <div class="number"></div>-->
        <!--                    <div class="desc">先占位</div>-->
        <!--                </div>-->
        <!--                <a class="more" href="#" style="height: 10px">-->
        <!--                    <i class="m-glyphicon glyphicon-swapright m-glyphicon glyphicon-white"></i>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="col-md-3" style="position: relative;">-->
        <!--            <div class="dashboard-stat yellow">-->
        <!--                <div class="visual">-->
        <!--                    <i class="fa fa-bar-chart-o"></i>-->
        <!--                </div>-->
        <!--                <div class="details">-->
        <!--                    <div class="number"></div>-->
        <!--                    <div class="desc">先占位</div>-->
        <!--                </div>-->
        <!--                <a class="more" href="#" style="height: 10px">-->
        <!--                    <i class="m-glyphicon glyphicon-swapright m-glyphicon glyphicon-white"></i>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
    </div>
    <div class="echarts" id="main"
         style=" height:300px; width:90%%; border-top:1px solid #999;border-top:1px solid #999; padding:30px 0 0 0">
    </div>
     <form action="" method="post">
         <p>
             <select class="echarts-select">
                 <option>2</option>
                 <option>3</option>
                 <option>category</option>
             </select>
         </p>

         <label>index:</label>
         <input type="text" class="sel_in1" value="1"/>
         <label>update:</label>
         <input type="text" class="sel_in2" value="2"/>
         <label>create:</label>
         <input type="text" class="sel_in3" value="3"/>
         <label>delete:</label>
         <input type="text" class="sel_in4" value="4"/>
         <input type="button" class="sel_btn btn" value="刷新"/>
     </form>

    <script src="http://echarts.baidu.com/build/dist/echarts.js"></script>
    <script type="text/javascript">
        var echarts_select =  $(".echarts-select").val();
        var sel_in1 =  $(".sel_in1").val();
        var sel_in2 =  $(".sel_in2").val();
        var sel_in3 =  $(".sel_in3").val();
        var sel_in4 =  $(".sel_in4").val();
        var date_x = <?php echo json_encode($apiData_x); ?>;
        var date = <?php echo json_encode($apiData); ?>;
        console.log("date_x :", date_x);
        console.log("date :", date);
        require.config({
            paths: {
                echarts: 'http://echarts.baidu.com/build/dist'
            }
        });
        require(
            [
                'echarts',
                'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
            ],
            function (ec) {
                // 基于准备好的dom，初始化echarts图表
                var myChart = ec.init(document.getElementById('main'));


                var option = {

                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['index', 'update', 'create', 'delete']
                    },
                    toolbox: {
                        show: true,
                        feature: {
                            mark: {show: false},
                            dataView: {show: false, readOnly: false},
                            magicType: {show: false, type: ['line']},
                            restore: {show: false},
                            saveAsImage: {show: false}
                        }
                    },
                    calculable: true,
                    xAxis: [
                        {
                            type: 'category',
                            boundaryGap: false,
                            axisLabel : {
                                show:true,
                                interval: 'auto',    // {number}
                                formatter: '星期{value}'
                            },
                            data : [
                                '1','2','3','4','5','6'
                            ]
                        }
                    ],

                    yAxis: [
                        {
                            type: 'value'
                        }
                    ],
                    series: [
                        {
                            name: 'index',
                            type: 'line',
                            smooth: true,
                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                            data: [11,20,30,40,50,20]
                        }
                        {
                            name: 'update',
                            type: 'line',
                            smooth: true,
                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                            data: [21, 182, 434, 791, 390, 30]
                        },
                        {
                            name: 'create',
                            type: 'line',
                            smooth: true,
                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                            data: [31, 1132, 601, 234, 120, 90]
                        },
                        {
                            name: 'delete',
                            type: 'line',
                            smooth: true,
                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                            data: [41, 1100, 401, 634, 520, 10]
                        }
                    ]

                };

                // 为echarts对象加载数据
                    myChart.setOption(option);
            }
        );
    </script>
<?php
}
echo $footer; ?>
