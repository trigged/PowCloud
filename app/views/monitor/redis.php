<?php echo $header; ?>
<div>
    <table class="table table-striped">
        <thead>
        <tr>
            <td>参数</td><td>对应值</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Ping</td><td><?php echo $redisInfo['ping']?></td>
        </tr>
        <tr>
            <td>命中率</td><td><?php echo $redisInfo['keyspace_hits']  == 0 ? 0 : round($redisInfo['keyspace_hits'] * 100 / ($redisInfo['keyspace_hits'] + $redisInfo['keyspace_misses']), 2) . '%'; ?></td>
        </tr>
        <tr>
            <td>命中次数</td><td><?php echo $redisInfo['keyspace_hits']?></td>
        </tr>
        <tr>
            <td>失效次数</td><td><?php echo $redisInfo['keyspace_misses']?></td>
        </tr>
        <tr>
            <td>执行次数</td><td><?php echo $redisInfo['total_commands_processed'];?></td>
        </tr>
        <tr>
            <td>在线天数</td><td><?php echo $redisInfo['uptime_in_days'];?></td>
        </tr>
        <tr>
            <td>内存使用</td><td><?php echo $redisInfo['used_memory_human'];?></td>
        </tr>
        <tr>
            <td>最后写时间</td><td><?php echo date('Y-m-d H:i:s', $redisInfo['last_save_time']); ?></td>
        </tr>
        <?php foreach ($redisInfo as $key => $value) :
            if (preg_match("/^db\d+$/", $key)): ?>
                <tr>
                    <td><?php echo $key; ?></td>
                    <td><?php foreach ($value as $k => $v) {
                            echo $k . '=' . $v . ';';
                        }?></td>
                </tr>
        <?php endif; endforeach; ?>
        </tbody>
    </table>
</div>

<?php echo $footer; ?>