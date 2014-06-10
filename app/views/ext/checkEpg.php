<?php
if (empty($video_check_state)) :
    echo "暂无数据同步任务,你可以点击刷新 手动开始任务"; elseif ($video_check_state == 'start') :
    echo "任务正在检测中,请耐心等待"; elseif ($video_check_state == 'ok') :
    echo "数据已经是最新的了"; elseif ($video_check_state == 'end') :

    echo "<thead><tr style='color:#5ea351'><td>数据ID</td><td>表名</td><td>标题</td><td>信息</td></tr></thead><tbody>";

    foreach ($modelsName as $modelName) :
        $videos_check = \Operator\ReadApi::getVideoCheckData($modelName);
        $model = SchemaBuilder::where('table_name', '=', $modelName)->first();
        foreach ($videos_check as $video_id):
            $value = $video_id ? explode(':', \Operator\ReadApi::getVideoStateCheckInfo($video_id, $modelName)) : '';
            if (count($value) !== 3) {
                continue;
            }
            $id = $value[0];
            $title = $value[1];
            $info = $value [2];
            ?>
            <tr onclick="location.href='<?php echo URL::action('CmsController@edit', array('table' => $model->id, 'cms' => $id)) ?>'" ><td><?php echo $id ?></td><td><?php echo $model->table_alias; ?></td><td><?php echo $title; ?></td><td><?php echo trim($info, ',') ?></td></tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>

<?php elseif ($video_check_state):
    if ($video_progress) {
        if ($video_check_count) {
            echo sprintf('<div class="progress  progress-warning  progress-striped"><div class="bar" style="width: %s;"></div></div>', $video_progress);
        } else {
            echo sprintf('<div class="progress  progress-success  progress-striped"><div class="bar" style="width: %s;"></div></div>', $video_progress);
        }
    }
    echo "数据正在处理中,当前进度: " . $video_check_state;
endif; ?>