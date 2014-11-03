/**
 * table operation module
 * Created by troyfan on 14-3-19.
 */
var table = function ($) {

    //table op for delete
    var tableDataOp = function () {
        $('.JS_tableDataOp').click(function (e) {
            if (confirm('确认要执行操作吗?')) {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: $(this).attr('data-method'),
                    success: function (re) {
                        re = $.parseJSON(re);
                        if (re.status == 'fail') {
                            toastr.error(re.message);
                            return false;
                        }
                        location.reload();
                    }
                });
            }
            e.stopPropagation();
            return false;
        })
    };

    //排序实际操作脚本
    var loadRankFunction = function () {
        $('#JS_rank_append').on('click', function () {
            var button = $(this);
            button.attr('disabled', true);
            var table = button.attr('data-table');
            var url = button.attr('data-url');
            var id = [];
            var ra = [];
            $('table#' + table + ' tr').each(function () {
                var idData = $(this).attr('data-id');
                var rankData = $(this).attr('data-rank')
                if ($(this).attr('data-rank')) {
                    id.push(idData);
                    ra.push(rankData);
                }
            });
            ra.sort(function (a, b) {
                return parseInt(a) < parseInt(b) ? 1 : -1
            });
            $.ajax({
                url: url,
                data: {'id': id, 'rank': ra, 'table': table.replace('_table', '')},
                type: 'POST',
                success: function (re) {
                    var json = $.parseJSON(re);
                    if (json.status == 'success') {
                        button.parent().remove();
                        alert('排序成功');
                    } else {
                        button.attr('disabled', false);
                        alert('排序失败，稍后再试');
                    }
                }
            }).error(function () {
                    button.attr('disabled', false);
                })
                .complete(function () {
                    button.attr('disabled', false);
                });
        });
    };

    //sort b
    var sortable = false;
    var dragging = false;
    var sortTarget, current, iX, iY, tpl;
    var holderAttribute = '.sortable-holder';
    var sort = function (target, holder) {
        sortTarget = $('.index',$(target));
        tpl = holder;
        sortTarget.mousedown(function (e) {
            current = $(this).parent();
            _sortMouseDown(e)
        });
        _sortMouseUp();
    };
    var _sortMouseDown = function (e) {

        iX = e.clientX - current[0].offsetLeft;
        iY = e.clientY - current[0].offsetTop;
        document.onmousemove = function (e) {
            if (current) {
                current.css('cursor', 'move');
                dragging = true;
                _sortMouseMove(e);
            }
        };
    };
    var _sortMouseMove = function (e) {
        if (!current) return false;
        var oY = e.clientY - iY;
        var oX = e.clientX - iX;
        var prevTop = null;
        var nextTop = null;
        if ($(holderAttribute).length <= 0) {
            if (current.prev().length > 0) {
                prevTop = current.prev()[0].offsetTop;
            }
            if (current.next().length > 0) {
                nextTop = current.next()[0].offsetTop;
            }
        } else if ($('.sortable-holder').length > 0) {
            if ($(holderAttribute).prev().length > 0) {
                prevTop = $('.sortable-holder').prev()[0].offsetTop;
            }

            if ($(holderAttribute).next().length > 0) {
                nextTop = $('.sortable-holder').next()[0].offsetTop;
            }
        }

        var cur = $(holderAttribute).length > 0 ? $(holderAttribute) : current;
        if (prevTop != null && prevTop > oY) {
            $(tpl).insertBefore(cur.prev(), false);
            if (cur.attr('class') == 'sortable-holder') {
                cur.remove();
            }
        } else if (nextTop != null && nextTop < oY) {
            $(tpl).insertAfter(cur.next(), false);
            if (cur.attr('class') == 'sortable-holder') {
                cur.remove();
            }
        }

        if (dragging) {
            current.css({"top": oY + "px", "left": oX + "px", "padding": "20px 0", "position": "absolute"});
            return false;
        }
    };
    var _sortMouseUp = function () {
        $(document).mouseup(function (e) {
            if (dragging) {
                current.css({"position": "", "top": '', "left": '', "padding": "0"});
                $(current).insertBefore($(holderAttribute));
                document.onmousemove = null;
                $(holderAttribute).remove();
                current.css('cursor', '');
                sortDone();
            }
            current = '';
            dragging = false;
        })
    };
    //排序完成时执行的操作 可以自定义
    var sortDone = function () {
        if ($('#JS_rank_append').length == 0) {
            var table = current.parent().attr('data-table');
            var url = current.parent().attr('data-rank');
            var update_button = '<button id="JS_rank_append" style="margin-left: 10px;" data-table="' + table + '" data-url="' + url + '" class="btn btn-primary btn-danger"  type="button" >更新排序</i></span></button>';
            var cancel_button = '<a class="btn" style="margin-left: 10px;" href="javascript:location.reload()">取消排序</a>';
            $('#JS_button_hook').append('<div style="display: inline;">' + update_button + cancel_button + '</div>');
        }
        //加载排序操作
    };

    return {
        init: function (sortTarget, holder) {
            //table删除操作
            tableDataOp();
            //初始化排序
            if ($(sortTarget).length > 0) {
                sort(sortTarget, holder);
                loadRankFunction();
            }
        }
    }
}($);