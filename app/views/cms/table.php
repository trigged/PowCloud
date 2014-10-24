<?php echo $header; ?>
    <fieldset class="table_list_title">
        <legend><?php echo $table->table_alias; ?></legend>
    </fieldset>
    <div id="JS_button_hook" class="" style="">
        <?php if (isset($options[$table->id]) && $options[$table->id]['edit'] == 2): ?>
            <a href="<?php echo URL::action('CmsController@create', array('id' => $table->id)) ?>"
               class="btn btn-primary" type="button">添加数据</a>
        <?php endif ?>
        <?php echo Event::fire('cms.hook', array($table->table_name, Hook::CMS_TABLE_HEAD_BUTTON), true); ?>
    </div>
    <div class="data-list" style="">
        <ul class="nav nav-tabs" style="margin-top: 10px;">
            <li class="<?php echo !$status ? 'active' : ''; ?>">
                <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id)) ?>">正常数据</a>
            </li>
            <li class="<?php echo $status === 'timing' ? 'active' : ''; ?>">
                <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id, 'status' => 'timing')) ?>">
                    定时任务
                </a>
            </li>
            <li class="<?php echo $status === 'waiting' ? 'active' : ''; ?>">
                <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id, 'status' => 'waiting')) ?>">
                    待发布
                </a>
            </li>
            <?php if ($roles === 3): ?>
                <li class="<?php echo $status === 'deleted' ? 'active' : ''; ?>">
                    <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id, 'status' => 'deleted')) ?>">
                        已删除数据
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <?php if ($dataList && $dataList->count() > 0): ?>
        <div class="dataTablesList table-responsive">
            <!-- CM_TABLE_HEADER HOOK begin -->
            <?php echo Event::fire('cms.hook', array($table->table_name, Hook::CMS_TABLE_HEADER, array('pageSize' => $pageSize, 'table' => $table, 'status' => $status)), true); ?>
            <!-- CM_TABLE_HEADER HOOK end -->
            <table id="<?php echo $table->table_name ?>_table"
                   class="table table-hover <?php if (empty($status) && (empty($table_options['list.table.rank']) || $table_options['list.table.rank'] == 1 || empty($_GET['keyword']))): ?> sortable<?php endif; ?>">
                <thead>
                <tr>
                    <?php if (empty($table_options) || (!empty($table_options['list.table.id_display']) &&
                            $table_options['list.table.id_display'] != 1)
                    ): ?>
                        <td>#</td>
                    <?php endif; ?>
                    <?php if (!empty($table_options['list.table.id_display']) && $table_options['list.table.id_display'] == 1): ?>
                        <td><?php echo str_replace('列表', '', $table->table_alias); ?>ID</td>
                    <?php endif; ?>
                    <?php foreach ($forms as $form): if ($form->field && $form->isVisible && !in_array($form->field, $foreign)): ?>
                        <td style="display:<?php echo($form->isVisible || $form->isVisible == '' ? '' : 'none') ?>"><?php echo $form->label ?></td>
                    <?php endif;endforeach; ?>
                    <?php if ($status === 'timing'): ?>
                        <td>发布时间</td>
                        <td>状态</td>
                    <?php endif; ?>

<!--                    --><?php //if ($roles === 3): ?>
<!--                        <td>缓存</td>-->
<!--                    --><?php //endif; ?>
                    <?php if (!empty($table_options['list.table.modifier']) && $table_options['list.table.modifier'] == 1): ?>
                        <td>修改人</td>
                    <?php endif; ?>
                    <td>更新时间</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody data-table="<?php echo $table->table_name ?>_table"
                       data-rank="<?php echo URL::action('ExtController@rank'); ?>">
                <?php $index = 1;
                foreach ($dataList as $data):
                    $class = "";
                    $state = null;
                    // glyphicon glyphicon-time
                    if (isset($data->timing_state)) {
                        $timing_state = $data->timing_state;
                        if ($timing_state === \Operator\RedisKey::HAS_PUB_ONLINE) {
                            $state = '定时上线';
                        } elseif ($timing_state === \Operator\RedisKey::HAS_PUB_OFFLINE) {
                            $class = 'error';
                            $state = '定时下线';
                        } elseif ($timing_state === \Operator\RedisKey::HAS_PUB_FIRST) {
                            $state = '定时顶置';
                        } elseif ($data->timing_state == \Operator\RedisKey::READY_LINE) {
                            $state = '待发布';
                        } elseif ($data->hasNormalData()) {
                            $state = '上线中';
                        }
                    }
                    if (!$class && !$data->hasChildData()) {
                        $class = 'warning';
                    }
                    ?>
                    <tr class="<?php echo $class; ?>" data-id="<?php echo $data->id; ?>"
                        data-rank="<?php echo $data->rank ?>"
                        id="<?php echo $table->table_name; ?>_data_<?php echo $data->id; ?>">

                        <?php if (empty($table_options) ||
                            (!empty($table_options['list.table.id_display']) && $table_options['list.table.id_display'] != 1)
                        ): ?>
                            <td class="index"><?php echo $index++; //$data->id ?></td>
                        <?php endif; ?>

                        <?php if (!empty($table_options['list.table.id_display']) && $table_options['list.table.id_display'] == 1): ?>
                            <td class="index"><?php echo $data->id ?></td>
                        <?php endif; ?>

                        <?php foreach ($forms as $form):if ($form->field && $form->isVisible && !in_array($form->field, $foreign)): ?>
                            <td style="display:<?php echo($form->isVisible || $form->isVisible == '' ? '' : 'none') ?>">
                                <?php echo call_user_func_array(array('\Utils\DataColumnHelper', $form->type), array('', $form->dataType, $data->{$form->field}, $form->default_value)); ?>
                            </td>
                        <?php endif;endforeach; ?>

                        <?php if ($status === 'timing'): ?>
                            <td><?php echo $data->timing_time; ?></td>
                            <td><?php echo $state; ?></td>
                        <?php endif; ?>

<!--                        --><?php //if ($roles === 3): ?>
<!--                            <td>-->
<!--                                --><?php //echo \Operator\ReadApi::getTableObject($table->table_name, $data->id, false) ? '<span class="label label-success">有</span>' : '<span class="label label-warning">无</span>' ?>
<!--                            </td>-->
<!--                        --><?php //endif; ?>
                        <?php if (!empty($table_options['list.table.modifier']) && $table_options['list.table.modifier'] == 1): ?>
                            <td><?php echo $data->user_name; ?></td>
                        <?php endif; ?>
                        <td><?php echo $data->updated_at; ?></td>
                        <td>
                            <?php if ($roles === 3 && $status === "deleted" && (int)date('Y', strtotime($data->deleted_at)) > 0): ?>
                                <a class="JS_tableDataOp" title="恢复数据" data-method="POST" href="javascript:void (0)"
                                   data-url="<?php echo URL::action('CmsController@restore', array('table' => $table->id, 'id' => $data->id)); ?>">
                                    <i class="glyphicon glyphicon-repeat"></i>
                                </a>

                            <?php else: ?>
                                <a title="编辑"
                                   href="<?php echo URL::action('CmsController@edit', array('cms' => $data->id, 'table' => $table->id)) ?>"><i
                                        class="glyphicon glyphicon-edit"></i></a>

                                <?php if ($status !== 'timing' && isset($data->timing_state) && $data->timing_state === \Operator\RedisKey::READY_LINE): ?>
                                    <a title="上线" class="JS_tableDataOp" data-method="POST" href="javascript:void (0)"
                                       data-url="<?php echo URL::action('CmsController@online', array('table' => $table->id, 'id' => $data->id)); ?>"><i
                                            class="glyphicon glyphicon-hand-up"></i></a>


                                <?php elseif (isset($data->timing_state) && $data->timing_state !== \Operator\RedisKey::READY_LINE): ?>
                                    <a title="下线" class="JS_tableDataOp" data-method="POST" href="javascript:void (0)"
                                       data-url="<?php echo URL::action('CmsController@offline', array('table' => $table->id, 'id' => $data->id)); ?>"><i
                                            class="glyphicon glyphicon-hand-down"></i></a>

                                <?php endif; ?>
                                <?php if (empty($status) && (empty($table_options['list.table.rank']) || $table_options['list.table.rank'] == 1 || empty($_GET['keyword']))): ?>
                                    <?php echo \Utils\DataColumnHelper::rank('rank', $data->id, $table->table_name . '_data_' . $data->id, $data); ?>
                                <?php endif; ?>

                                <?php if ($roles === 3): ?>
                                    <a title="查看历史版本"
                                       href="<?php echo URL::action('CmsController@detail', array('table' => $table->id, 'id' => $data->id)); ?>"><i
                                            class="glyphicon glyphicon-file"></i></a>
                                <?php endif; ?>
                                <?php if (isset($options[$table->id]) && $options[$table->id]['edit'] == 2): ?>
                                    <a title="更新缓存" href="javascript:void (0)" class="JS_tableDataOp" data-method="post"
                                       data-url="<?php echo URL::action('CmsController@cacheRefresh', array('table' => $table->table_name, 'target' => $data->id)); ?>"><i
                                            class="glyphicon glyphicon-refresh"></i></a>

                                    <a title="删除" class="JS_tableDataOp" data-method="DELETE" href="javascript:void (0)"
                                       data-url="<?php echo URL::action('CmsController@destroy', array('cms' => $data->id, 'table' => $table->id)); ?>"><i
                                            class="glyphicon glyphicon-remove"></i></a>

                                <?php endif ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo $dataList ? $dataList->appends(array_filter(array(
                'id'      => $table->id,
                'status'  => $status,
                'keyword' => empty($_GET['keyword']) ? '' : $_GET['keyword'],
                'field'   => empty($_GET['field']) ? '' : $_GET['field'],
            )))->links() : ''; ?>
            <?php else: ?>
                <?php echo empty($_GET['keyword']) ? '暂无数据' : '无搜索结果'; ?>
            <?php
            endif;
            ?>
        </div>
        <?php echo Event::fire('cms.hook', array($table->table_name, Hook::CMS_TABLE_FOOTER_BUTTON), true); ?>
    </div>
    <script src="<?php echo URL::asset('js/module/table.js'); ?>"></script>
    <script>
        $(function () {
            table.init('.sortable tbody tr', '<tr class="sortable-holder" style="height: 50px;"></tr>');
        });
    </script>
<?php echo $footer; ?>