<?php echo $header; ?>
<div class="row">
    <ul class="nav nav-tabs" style="margin-top: 10px;">
        <li class="<?php echo !$status ? 'active' : ''; ?>">
            <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id)) ?>">调用示例</a>
        </li>
        <li class="<?php echo $status === 'timing' ? 'active' : ''; ?>">
            <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id, 'status' => 'timing')) ?>">
                字段
            </a>
        </li>
        <li class="<?php echo $status === 'waiting' ? 'active' : ''; ?>">
            <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id, 'status' => 'waiting')) ?>">
                文档
            </a>
        </li>
        <?php if ($roles === 3): ?>
            <li class="<?php echo $status === 'deleted' ? 'active' : ''; ?>">
                <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id, 'status' => 'deleted')) ?>">
                    示例
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?php echo $footer; ?>

