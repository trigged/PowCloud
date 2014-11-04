<?php echo $header;
//var_dump($miss);
?>

    <div class="tablesInfo">
        <table class="table table-striped">
            <thead>
            <tr>
                <td>别名</td>
                <td>缓存情况</td>
                <td>状态</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $item): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['value']; ?></td>
                    <td><?php if ($item['state']) {
                            echo '<span class="label label-success">正常</span>';
                        } else {
                            echo '<span class="label label-warning">缓存丢失</span>';
                        }?></td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php echo $footer; ?>