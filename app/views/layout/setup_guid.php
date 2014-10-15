<div class="row demo-samples">
    <div class="col-md-4">
        <div class="todo mrm">
            <div class="todo-search">
                <input class="todo-search-field" value="" placeholder="初始引导"/>
            </div>
            <ul>
                <li class="todo-done" id='li_host'>
                    <a href="/host/create" style="text-decoration: none";/>
                    <div class="todo-icon fui-check-inverted" id='system_host'></div>
                    <div>
                        <h4 class="todo-name">
                            创建一个你所想要的网站
                        </h4>
                        好的开始
                    </div>
                </li>

                <li id='li_path'>
                    <div class="todo-icon fui-location" id='system_path'></div>
                    <a href="/path" style="text-decoration: none";/>
                    <div class="todo-content">
                        <h4 class="todo-name">
                            选择你所想要的访问路径
                        </h4>
                        加油
                    </div>
                </li>

                <li id='li_table'>
                    <a href="/table/create" style="text-decoration: none";/>
                    <div class="todo-icon fui-time" id='system_table'></div>

                    <div class="todo-content">
                        <h4 class="todo-name">
                            创建API返回的结构
                        </h4>
                        马上就要成功了
                    </div>
                </li>

                <li id='li_form'>
                    <a href="/table" style="text-decoration: none";/>
                    <div class="todo-icon fui-time" id='system_form'></div>
                    <div class="todo-content">
                        <h4 class="todo-name">
                            为你的结构创造一个友好的编辑界面吧
                        </h4>
                        激动人心的时刻
                    </div>
                </li>
            </ul>
        </div>
    </div>

</div>

<script>
    host_maker = '/host/create';
    path_maker = '/path';
    table_maker = '/table/create';
    forms_maker = '/table';

    wait_icon = 'todo-icon fui-time';
    current_icon = 'todo-icon fui-location';
    finish_icon = 'todo-icon fui-check-inverted';

    todo_done = 'todo-done';

    todo_none = '';

    current_path = window.location.pathname;

    if (current_path == host_maker) {
        $('#li_host').removeClass();
        $('#li_path').removeClass();
        $('#li_table').removeClass();
        $('#li_form').removeClass();

        $('#system_host').attr('class', current_icon);
        $('#system_path').attr('class', wait_icon);
        $('#system_table').attr('class', wait_icon);
        $('#system_form').attr('class', wait_icon);

    }
    else if (current_path == path_maker) {
        $('#li_host').attr('class', todo_done);
        $('#li_path').removeClass();
        $('#li_table').removeClass();
        $('#li_form').removeClass();

        $('#system_host').attr('class', finish_icon);
        $('#system_path').attr('class', current_icon);
        $('#system_table').attr('class', wait_icon);
        $('#system_form').attr('class', wait_icon);
    }
    else if (current_path == table_maker) {
        $('#li_host').attr('class', todo_done);
        $('#li_path').attr('class', todo_done);
        $('#li_table').removeClass();
        $('#li_form').removeClass();

        $('#system_host').attr('class', finish_icon);
        $('#system_path').attr('class', finish_icon);
        $('#system_table').attr('class', current_icon);
        $('#system_form').attr('class', wait_icon);
    }
    else if (current_path == forms_maker) {
        $('#li_host').attr('class', todo_done);
        $('#li_path').attr('class', todo_done);
        $('#li_table').attr('class', todo_done);
        $('#li_form').removeClass();

        $('#system_host').attr('class', finish_icon);
        $('#system_path').attr('class', finish_icon);
        $('#system_table').attr('class', finish_icon);
        $('#system_form').attr('class', current_icon);
    }

</script>