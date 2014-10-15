<?php echo $header; ?>
    <form method="post" class="form-horizontal">
        <fieldset>
            <legend>输入sql语句</legend>
            <div class="form-group">
                <label class="control-label">选择DB:</label>

                <div class="controls">
                    <?php echo Form::select('connection', array('mysql' => 'x-cms', 'models' => 'cms-models'), $data['connection'], array('id' => 'connection')); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">sql语句:</label>

                <div class="controls">
                    <input type="text" name="sql" placeholder="Type sql command…" value="<?php echo $data['sql'] ?>"
                           class="form-control">
                    <span class="help-block">目前只支持SELECT查询</span>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">EXECUTE</button>
            </div>
        </fieldset>
    </form>
<?php if ($data['sql'] && (!empty($data['return']) || !empty($data['error']['errorMessage']))): ?>
    <fieldset>
        <legend>返回结果</legend>
        <pre>
            <?php if (!empty($data['error']['errorMessage'])): echo $data['error']['errorMessage']; ?>
            <?php elseif (!empty($data['return'])):  dd(addslashes_deep($data['return'])); ?>
            <?php endif; ?>
        </pre>
    </fieldset>
<?php endif; ?>
<?php echo $footer; ?>

<?php
function addslashes_deep($value)
{
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value) ? array_map('addslashes_deep', $value) : e($value);
    }
}

?>