<?php echo $header; ?>
    <div class="currentVersion">
        <fieldset>
            <legend>当前版本</legend>
        </fieldset>
        <table class="table table-bordered table-striped">
            <colgroup>
                <col class="col-md-1">
                <col class="col-md-7">
            </colgroup>
            <thead>
            <tr>
                <th>属性</th>
                <th>属性值</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    模型名称
                </td>
                <td><?php echo $currentVersion->table_name; ?></td>
            </tr>
            <tr>
                <td>
                    插件名称
                </td>
                <td><?php echo $currentVersion->name; ?></td>
            </tr>
            <tr>
                <td>
                    位置
                </td>
                <td>
                    <?php echo $currentVersion->action; ?>
                </td>
            </tr>
            <tr>
                <td>
                    代码
                </td>
                <td>
                    <pre><?php echo htmlspecialchars($currentVersion->code); ?></pre>
                </td>
            </tr>
            <tr>
                <td>
                    状态
                </td>
                <td><?php echo $currentVersion->status ? '<span class="label label-info">启用</span>' : '<span class="label label-info">禁用</span>'; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php echo $record; ?>
<?php echo $footer; ?>