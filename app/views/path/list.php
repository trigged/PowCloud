<?php echo $header; ?>
    <link href="<?php echo URL::asset('css/zTreeStyle.css'); ?>" rel="stylesheet">
    <script src="<?php echo URL::asset('js/jquery.ztree.all-3.5.min.js'); ?>"></script>
    <SCRIPT type="text/javascript">
        <!--
        var setting = {
            view: {
                selectedMulti: false
            },
            edit: {
                enable: true,
                showRemoveBtn: false,
                showRenameBtn: false
            },
            data: {
                keep: {
                    parent: true
                    //leaf:true
                },
                simpleData: {
                    enable: true
                }
            },
            callback: {
                beforeDrag: beforeDrag,
                beforeRemove: beforeRemove,
                beforeRename: beforeRename,
                onRemove: onRemove,
                onClick: onClick
            }
        };

        var zNodes = <?php echo $pathTree;?>;
        function beforeDrag(treeId, treeNodes) {
            return false;
        }
        function beforeRemove(treeId, treeNode) {
            return confirm("确认删除 节点 -- " + treeNode.name + " 吗？");
        }
        function onRemove(e, treeId, treeNode) {
            return confirm("确认删除 节点 -- " + treeNode.name + " 吗？");
        }
        function beforeRename(treeId, treeNode, newName) {
            if (newName.length == 0) {
                alert("节点名称不能为空.");
                var zTree = $.fn.zTree.getZTreeObj("pathList");
                setTimeout(function () {
                    zTree.editName(treeNode)
                }, 10);
                return false;
            }
            return true;
        }

        function onClick(e, treeId, treeNode) {
            $.ajax({
                url: treeNode.infoUrl,
                type: 'GET',
                success: function (re) {
                    $('.info').html(re);
                }
            });
        }
        function add(e) {
            var zTree = $.fn.zTree.getZTreeObj("pathList"),
                isParent = e.data.isParent,
                nodes = zTree.getSelectedNodes(),
                treeNode = nodes[0];
            if (treeNode) {
                $.ajax({
                    url: '<?php echo URL::action('PathController@store'); ?>',
                    type: 'POST',
                    data: $('.child_form').serialize() + '&parent=' + treeNode.name,
                    success: function (re) {
                        re = $.parseJSON(re);
                        if (re.error) {
                            alert(re.error);
                            return false;
                        }
                        if (re) {
                            if (treeNode) {
                                treeNode = zTree.addNodes(treeNode, {id: re.id, pId: treeNode.id, isParent: isParent, name: re.name, infoUrl: re.infoUrl});
                            }
                            $('#JS_PathChild').parent().parent().remove();
                            alert('创建成功');
                        } else
                            alert('创建路径失败');
                    }
                });
            }
        }
        ;
        function edit() {
            var zTree = $.fn.zTree.getZTreeObj("pathList"),
                nodes = zTree.getSelectedNodes(),
                treeNode = nodes[0];
            if (nodes.length == 0) {
                alert("请先选择一个节点");
                return;
            }
            zTree.editName(treeNode);
        }
        ;
        function remove(e) {
            if (confirm("确认删除 节点  吗？")) {
                var zTree = $.fn.zTree.getZTreeObj("pathList"),
                    nodes = zTree.getSelectedNodes(),
                    treeNode = nodes[0];
                if (nodes.length == 0) {
                    alert("请先选择一个节点");
                    return;
                }
                $.ajax({
                    url: $(e.target).attr('data-url'),
                    type: 'DELETE',
                    data: $('.child_form').serialize() + '&parent=' + treeNode.name,
                    success: function (re) {
                        re = $.parseJSON(re);
                        if (re) {
                            var callbackFlag = $("#callbackTrigger").attr("checked");
                            zTree.removeNode(treeNode, callbackFlag);
                            alert('删除成功');
                        } else
                            alert('删除失败');
                    }
                });
            }
        }
        ;
        function clearChildren(e) {
            var zTree = $.fn.zTree.getZTreeObj("pathList"),
                nodes = zTree.getSelectedNodes(),
                treeNode = nodes[0];
            if (nodes.length == 0 || !nodes[0].isParent) {
                alert("请先选择一个父节点");
                return;
            }
            zTree.removeChildNodes(treeNode);
        }
        ;

        $(document).ready(function () {
            $.fn.zTree.init($("#pathList"), setting, zNodes);
            $("#addParent").bind("click", {isParent: true}, add);
            $("body").on("click", "#addLeaf", {isParent: false}, add);
            $("#edit").bind("click", edit);
            $("body").on("click", "#remove", remove);
            $("#clearChildren").bind("click", clearChildren);
        });
        //-->
    </SCRIPT>

    <div class="col-md-3">
        <fieldset>
            <legend>API 列表</legend>
            <ul id="pathList" class="ztree pathList"></ul>
        </fieldset>
    </div>
    <div class="col-md-8">
        <fieldset>
            <legend>API信息</legend>
            <blockquote>
                <div class="info">
                    <p>点击左边节点查看相应路径信息</p>
                    <small>点击相应结点进行 <cite title="Source Title">添删改查操作</cite></small>
                </div>
            </blockquote>
        </fieldset>
    </div>
<?php echo $footer; ?>