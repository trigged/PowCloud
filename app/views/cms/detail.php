<?php echo $header; ?>

    <div class="currentVersion">
        <fieldset>
            <legend>当前版本</legend>
        </fieldset>
        <table class="table table-bordered table-striped">
            <colgroup>
                <col class="span1">
                <col class="span7">
            </colgroup>
            <thead>
            <tr>
                <th>属性</th>
                <th>属性值</th>
            </tr>
            </thead>
            <tbody>
            <?php //foreach (): ?>
            <tr>
                <td>
                    数据
                </td>
                <td><pre><?php print_r($currentVersion->toArray()); ?></pre></td>
            </tr>
            <?php //endforeach; ?>
            </tbody>
        </table>
    </div>

<!-- record begin -->
<?php echo $record; ?>
<!-- record end -->

<?php echo $footer;?>