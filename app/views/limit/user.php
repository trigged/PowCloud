<?php echo $header; ?>
<?php
if (!$isAdmin) {
    echo "<style>
        .limit {
            display:none;
        }
    </style>";
}
?>
    <div class="mt20"></div>
    <form data-status="0" id="group_form" class="form-horizontal" method="get">
        <div class="col-md-5">
            <input type="search" name="username" class="form-control" placeholder="搜索用户"
                   style="display: inline-block;">
        </div>
        <button id="JS_Sub" class="btn btn-primary" return="false">搜索</button>
    </form>

    <div class="data-list" style="">
        <ul class="nav nav-tabs" style="margin-top: 10px;">
            <?php foreach ($groupsArray as $groupId => $groupName): ?>
                <li class="<?php echo ($group == $groupId) ? 'active' : ''; ?>"><a
                        href="<?php echo URL::action('LimitController@user', array('group' => $groupId)); ?>"
                        group-id="<?php echo $groupId ?>"><?php echo $groupName; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <div class="dataTablesList">
            <table id="" class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>用户名</th>
                    <th>所在用户组</th>
                    <th>邮箱</th>
                    <th>上次登录时间</th>
                    <th class="limit">操作</th>
                </tr>
                </thead><?php if ($limits): ?>
                    <tbody>
                    <?php foreach ($limits as $limit): ?>
                        <tr>
                            <td><?php echo $limit->id; ?></td>
                            <td><a href="#"><span class="label label-info"><?php echo $limit->name; ?></span></a></td>
                            <td><?php echo(isset($limit->id) && $limit->id ? Group::find(ATURelationModel::where('app_id', Session::get('app_id'))->where('user_id', $limit->id)->first()->group_id)->groupName : ''); ?></td>
                            <td><?php echo $limit->mail; ?></td>
                            <td><?php echo $limit->getDisplayModifyTime(); ?></td>
                            <td class="operation limit">
                                <a href="<?php echo URL::action('LimitController@handleUser', array('limit' => $limit->id)); ?>"><i
                                        class="glyphicon glyphicon-edit"></i></a>
                                <a class="JS_limitOp"
                                   data-url="<?php echo URL::action('LimitController@cancelAdmin', array('limit' => $limit->id)) ?>"
                                   href="<?php echo URL::action('LimitController@cancelAdmin', array('limit' => $limit->id)) ?>"
                                   data-target="<?php echo $limit->id; ?>" href="javascript:void(0)"><i title="取消管理员"
                                                                                                        class="glyphicon glyphicon-ban-circle"></i>
                                </a>
                                <a href="<?php echo URL::action('LimitController@setAdmin', array('limit' => $limit->id)) ?>"><i
                                        class="glyphicon glyphicon-user"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>

        <?php echo (isset($_GET['username'])?'':$limits?$limits->appends(array('group'=>$group))->links():'') ?>
    </div>


<?php echo $footer; ?>