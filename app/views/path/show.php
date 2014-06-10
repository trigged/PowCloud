<div class="hostsList">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>路径</th>
            <th>主机</th>
            <th>父路径</th>
            <th>缓存时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $path->id; ?></td>
                <td><?php echo $path->name; ?></td>
                <td><?php echo $path->host_id;?></td>
                <td><?php echo $path->parent;?></td>
                <td><?php echo $path->expire;?></td>
                <td><?php echo $path->updated_at;?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>