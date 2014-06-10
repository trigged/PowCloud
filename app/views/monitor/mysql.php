<?php echo $header; ?>
<div class="tablesInfo">
    <table class="table table-striped">
        <thead>
        <tr>
            <td>Name</td>
            <td>Rows</td>
            <td>Avg_row_len</td>
            <td>Data_lenth</td>
            <td>Max_data_len</td>
            <td>Auto_inc</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tablesInfo as $tableInfo): ?>
        <tr>
            <td><?php echo $tableInfo->Name; ?></td>
            <td><?php echo $tableInfo->Rows; ?></td>
            <td><?php echo $tableInfo->Avg_row_length; ?></td>
            <td><?php echo $tableInfo->Data_length; ?></td>
            <td><?php echo $tableInfo->Max_data_length; ?></td>
            <td><?php echo $tableInfo->Auto_increment; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<?php echo $footer; ?>