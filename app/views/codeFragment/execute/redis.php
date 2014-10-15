<?php echo $header; ?>
    <form method="post">
        <fieldset>
            <legend>输入redis命令</legend>
            <label class="control-label">命令:</label>

            <div class="controls">
                <input type="text" name="redis_command" placeholder="Type redis command…"
                       value="<?php echo $data['redis_command']; ?>" class="form-control">
            </div>

            <span class="help-block">只支持一般查询如:Hget,hgetall,zscore,zcount.......</span>
            <?php if (!empty($data['error']['errorMessage'])): ?><span
                class="help-block"><?php echo $data['error']['errorMessage'] ?></span><?php endif; ?>
            <button class="btn" type="submit">EXECUTE</button>
        </fieldset>
    </form>
<?php if (isset($data['return'])): ?>
    <fieldset>
        <legend>返回结果</legend>
        <pre>
            <?php is_array($data['return']) ? var_dump($data['return']) : print($data['return']); ?>
        </pre>
    </fieldset>
<?php endif; ?>
<?php echo $footer; ?>