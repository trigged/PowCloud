/**
 * CMS Core Script
 * Created by troyfan on 14-3-19.
 */

var CMS =  function(){
    var debug = false;

    var registerGlobalAjaxErrorEvent = function(){
        $(document).ajaxError(function(event, jqxhr, settings, exception){
            console.log(jqxhr.statusText);
            toastr.error(jqxhr.statusText);
        });
    };

    var messageTipInit=function(){
        if(typeof toastr == 'object'){
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        }
    };

    return {
        init:function(){
            messageTipInit();
            registerGlobalAjaxErrorEvent();
        }
    };
}();


