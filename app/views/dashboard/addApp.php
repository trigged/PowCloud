<?php echo $header; ?>
    <div style="padding:0 15px;">
        <div class="">
            <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
            <form data-status="0" id="addAppForm" class="form-horizontal"
                  action="<?php echo URL::action('DashBoardController@storeApp') ?>" method="post">
                <fieldset>
                    <legend>添加应用</legend>
                    <div class="form-group">
                        <label for="name" class="control-label col-sm-3">应用名称*:</label>

                        <div class="controls col-sm-3">
                            <input name="name" class="form-control" value="" type="text" placeholder="应用名称" id="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="app_id" class="control-label col-sm-3">信息&nbsp;:</label>

                        <div class="controls col-sm-3">
                            <input name="info" class="form-control" value="" type="text" placeholder="信息" id="info">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="app_id" class="control-label col-sm-3">数据库服务&nbsp;:</label>
                        <input type="checkbox" name="database" value="system" id="checkbox_system" checked>系统
                        <input type="checkbox" name="database" value="self" id="checkbox_self">自有
                    </div>
                    <div class="form-group base_info">

                        <div class="note note-warning">
                            <h4 class="block">自有数据库服务</h4>

                            <p>
                                您正在使用自有数据库服务,请确保一下几点
                            </p>

                            <p>
                                - 目前只支持 mysql 服务,并且请确保有数据库的创建,修改,和删除权限,用户打开远程访问
                            </p>

                            <p>
                                - 自有数据库服务的用户我们不再保障 <code>数据</code>的安全和稳定(因为不在我们的控制体系内,但是服务的安全和稳定我们可以保障) 所以请您做好备份和维护
                            </p>

                            <p>
                                <a href="http://doc.powapi.com/" target="_blank"> 相关文档地址</a>.
                            </p>

                        </div>

                        <div class="from-group">
                            <label for="app_id" class="control-label col-sm-3">服务器地址&nbsp;:</label>

                            <div class="controls col-sm-3">
                                <input name="info" class="form-control" value="" type="text" placeholder="主机地址"
                                       id="info">
                            </div>
                        </div>


                        <div class="from-group">
                            <label for="app_id" class="control-label col-sm-3">数据库用户名&nbsp;:</label>

                            <div class="controls col-sm-3">
                                <input name="info" class="form-control" value="" type="text" placeholder="请确保拥有权限"
                                       id="info">
                            </div>
                        </div>

                        <div class="from-group">
                            <label for="app_id" class="control-label col-sm-3">数据库密码&nbsp;:</label>

                            <div class="controls col-sm-3">
                                <input name="info" class="form-control" value="" type="text" placeholder="主机地址"
                                       id="info">
                            </div>
                        </div>
                    </div>
                    <button id="loading-btn" class="btn btn-info" onclick="()">测试连接</button>


                    <div style="margin-left:330px;">
                        <button id="JS_Sub" class="btn btn-primary">提交</button>
                        <a href="javascript:void (0)" class="btn  btn-warning" onclick="history.back();">取消</a>
                    </div>
                </fieldset>
            </form>
            <?php echo \Utils\FormBuilderHelper::staticEnd('addAppForm',
                array( //表单规则
                    'name' => array('required' => true),
                ),
                URL::action('DashBoardController@storeApp'),
                'POST'
            );//注册表单JS
            ?>
        </div>
    </div>
    <script>
        $('#loading-btn').click(function () {
            var btn = $(this);
            btn.button('检测中');
            $.ajax({
                type: "post",
                data: {},
                url: "<?php echo URL::action('DashBoardController@testDB'); ?>"
            }).done(function () {
                    btn.button('reset');
                });
        });

        $(function () {
            $("#checkbox_system").change(function () {
                console.log("you click checkbox_system");
                $("#checkbox_self").prop("checked", !this.checked);
                $(".base_info").prop("hidden", this.checked);
                if (this.checked) {

                }
            });
            $("#checkbox_self").change(function () {
                $("#checkbox_system").prop("checked", !this.checked);
                $(".base_info").prop("hidden", !this.checked);
                if (this.checked) {

                }
            });


        });
    </script>
<?php echo $footer; ?>