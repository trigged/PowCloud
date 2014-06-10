<script src="<?php echo URL::asset('js/ace/src-min/ace.js'); ?>" type="text/javascript" charset="utf-8" ></script>
<script src="<?php echo URL::asset('js/ace/src-min/theme-textmate.js'); ?>" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("code_edit_plugin");
    editor.session.setMode("ace/mode/php");
    editor.setTheme("ace/theme/textmate");
    editor.renderer.setShowPrintMargin(false);
    editor.on("change",function(){
        $('textarea[name="code"]').val(editor.session.getValue());
    });
    editor.setFontSize(16);
    editor.commands.addCommand({
        name: 'F11 Full Screen',
        bindKey: 'F11',
        exec: function(editor) {
            container = document.getElementById(editor.container.id);
            if(container.className.indexOf('fullScreen')!=-1){
                className = container.className.replace('fullScreen','');
                container.className = className;
            }
            else
                container.className += ' fullScreen' ;

            body = document.getElementsByTagName('body');
            if(body[0].className.indexOf('fullScreen')!=-1){
                className = body[0].className.replace('fullScreen','');
                body[0].className = className;
            }
            else
                body[0].className += ' fullScreen' ;
            console.log(body[0].className);
            editor.resize()
        }
    });

</script>