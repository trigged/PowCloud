<div class="JAjaxInput" style="display: none">
    <table class="table">
        <thead>
        <tr>
            <th>绑定对象</th>
            <th>数组源或链接</th>
            <th>字段映射关系(本地字段,远程字段)</th>
        </tr>
        </thead>
        <tbody id="ajaxInput">
        <tr class="JDisableDelete">
            <td rowspan="1" id="ajaxInputRowspan">
                <input type="text" value="" name="default_value[target]">
                &nbsp;&nbsp;<a style="cursor:pointer;" data-target="ajaxInput" data-type="tr" class="JAjaxInput"
                               data-tableindex="2" href="javascript:;"><i class="glyphicon glyphicon-plus"></i></a>
            </td>
            <td>
                <input type="text" value="" name="default_value[data][1][data]">
                &nbsp;&nbsp;<a style="cursor:pointer;" data-target="ajaxInput" data-type="td" class="JAjaxInput"
                               data-mapindex="2" data-tableindex="1" href="javascript:;"><i
                        class="glyphicon glyphicon-plus"></i></a>
                <a style="cursor:pointer;" data-type="delete-tr" class="JAjaxInput" href="javascript:;"><i
                        class="glyphicon glyphicon-remove"></i></a>
            </td>
            <td>
                <div style="margin-bottom: 5px;">
                    <input type="text" value="" class="form-control" name="default_value[data][1][map][1][localField]">&nbsp;&nbsp;&nbsp;
                    <input type="text" value="" class="form-control" name="default_value[data][1][map][1][remoteField]">
                    <a style="cursor:pointer;" data-type="delete-td" class="JAjaxInput" href="javascript:;"><i
                            class="glyphicon glyphicon-remove"></i></a>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="JNormalInput" style="display: none">
    <input type="text" id="default_value" placeholder="默认值" value="" class="form-control" name="default_value">
</div>
<?php
$tableIndex = 2;
if ($field !== null && is_array($field->default_value)) {
    $tableIndex = !empty($field->default_value['map']) ? count($field->default_value['map']) : 2;
}
?>
<script>
    $(function () {
        var tableIndex = <?php echo $tableIndex; ?>;
        $('.JAjaxInput').on('click', function () {
            var type = $(this).attr('data-type');
            var target = $(this).attr('data-target');
            var trTpl = '<tr><td><input type="text" value="" name="default_value[data][{tableIndex}][data]">&nbsp;&nbsp;<a style="cursor:pointer;" data-mapIndex="2" data-tableIndex="{data-tableIndex}" data-type="td" class="JAjaxInput" href="javascript:;"><i class="glyphicon glyphicon-plus"></i></a>&nbsp;&nbsp;<a href="javascript:;"  class="JAjaxInput" data-type="delete-tr" style="cursor:pointer;"><i class="glyphicon glyphicon-remove"></i></a></td><td><div style="margin-bottom: 5px;"><input type="text" value="" class="form-control" name="default_value[data][{tableIndex}][map][{mapIndex}][localField]">&nbsp;&nbsp;&nbsp;<input type="text" value="" class="form-control" name="default_value[data][{tableIndex}][map][{mapIndex}][remoteField]">&nbsp;&nbsp;<a href="javascript:;"  class="JAjaxInput" data-type="delete-td" style="cursor:pointer;"><i class="glyphicon glyphicon-remove"></i></a></div></td></tr>';
            var tdTpl = '<div style="margin-bottom: 5px;"><input type="text" value="" class="form-control" name="default_value[data][{tableIndex}][map][{mapIndex}][localField]">&nbsp;&nbsp;&nbsp;<input type="text" value="" class="form-control" name="default_value[data][{tableIndex}][map][{mapIndex}][remoteField]">&nbsp;&nbsp;<a href="javascript:;"  class="JAjaxInput" data-type="delete-td" style="cursor:pointer;"><i class="glyphicon glyphicon-remove"></i></a></div>';

            if (type == 'tr') {
                var rowspan = parseInt($(this).parent().attr('rowspan'));
                trTpl = trTpl.replace(/{tableIndex}/g, tableIndex).replace(/{mapIndex}/g, '1').replace(/{data-tableIndex}/g, tableIndex);
                $('#' + target).append(trTpl);
                $(this).parent().attr('rowspan', rowspan + 1);
                tableIndex++;
            } else if (type == 'td') {
                var mapIndex = parseInt($(this).attr('data-mapIndex'));
                $(this).parent().next().append(tdTpl.replace(/{mapIndex}/g, mapIndex).replace(/{tableIndex}/g, $(this).attr('data-tableIndex')));
                $(this).attr('data-mapIndex', mapIndex + 1);
            } else if (type == "delete-tr") {
                var rowspan = parseInt($('#ajaxInputRowspan').attr('rowspan'));
                if (rowspan > 1) {
                    $('#ajaxInputRowspan').attr('rowspan', rowspan - 1);
                    if (!$(this).parent().parent().hasClass('JDisableDelete'))
                        $(this).parent().parent().remove();
                } else {
                    alert('至少保留一个');
                }
            } else if (type == "delete-td") {

                if (parseInt($(this).parent().prev().length) > 0 || parseInt($(this).parent().next().length) > 0)
                    $(this).parent().remove();
                else {
                    var rowspan = parseInt($('#ajaxInputRowspan').attr('rowspan'));
                    if (rowspan > 1) {
                        if (!$(this).parent().parent().parent().hasClass('JDisableDelete')) {
                            $('#ajaxInputRowspan').attr('rowspan', rowspan - 1);
                            $(this).parent().parent().parent().remove();
                        } else {
                            alert('这行不能删除');
                        }
                    } else {
                        alert('至少保留一个');
                    }
                }
            }
        });

        $('.JFieldType').change(function () {
            var defaultValueHtml = $('.JDefaultValue').html();
            if ($(this).val() == 'ajaxInput') {
                $('.JNormalInput').html(defaultValueHtml);
                $('.JDefaultValue').html($('.JAjaxInput').html());
            } else {
                $('.JAjaxInput').html(defaultValueHtml);
                $('.JDefaultValue').html($('.JNormalInput').html());
            }
        });
    })
</script>