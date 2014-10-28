var geo = null;
var geoForce = null;
var geoType = null;

$(function () {

  $('input.image-uploader').on('click', function () {
    if ($('#image-uploader-container').size() == 0) {

      $('#image-uploader-cancel-btn').click(function () {
        $('#image-uploader-container').hide();
      });
    }

    $('input[image-uploader=true]').removeAttr('image-uploader');
    $(this).attr('image-uploader', 'true');
    $(this).before($('#image-uploader-container'));
    $('#image-uploader-container').css({'margin-top': this.clientHeight + 'px'});
    $('#image-uploader-container').show();
  });

  /**
   * 上传图片预览模块
   */
    //菜单效果
  $('input.image-uploader').on('mouseenter',function () {
    if ($(this).val()) $(this).after('<img src="' + $(this).val() + '" style=" border:solid 1px #ccc; background-color:#fff; padding:5px;z-index: 9999; position:absolute; width:300px;" />');
    return false;
  }).on('mouseleave', function () {
      if ($(this).val()) $(this).next().remove();
    });
  /**
   * 图片预览模块
   */
  $('img.image-thumb').hover(function () {
    var src = $(this).attr('src');
    if ($(this).attr("data-img")) {
      src = $(this).attr('data-img');
    }
    $(this).after('<img src="' + src + '" style=" border:solid 1px #ccc; background-color:#fff; padding:5px; position:absolute; width:300px;" />');
  }, function () {
    $(this).next().remove();
  });

  $('img.text-thumb').hover(function () {
    var src = $(this).attr('src');
    if ($(this).attr("data-text")) {
      src = $(this).attr('data-text');
    }
    $(this).after('<label style=" border:solid 1px #ccc; background-color:#fff; padding:5px; position:absolute; width:500px;" >' + src + '</label>');
  }, function () {
    $(this).next().remove();
  });

  $('.popover-trigger').popover('hide');
  $('.tooltip-trigger').tooltip('hide');

  $('input[name="geo[data][]"]').on('click', function (e) {
    var ids = [];
    $('input[name="geo[data][]"]:checked').each(function () {
      ids.push($(this).val());
    });
    if ($('#shortcut').length > 0)
      $('#shortcut').val(ids.join(','));
  });

  var request = [];

  //table area
  $('table .area_filter_tr').on('click', function (event) {
    //todo 阻止多次ajax请求
    var tar = $(this).parent().parent();
    var namespace = $(this).attr('data-namespace');
    var parent = $(this).parent();
    var parentHtml = $(this).parent().html();
    var localGeo = geo;
    var localForce = geoForce;
    var localGeoType = geoType;
    var data = $(this).attr('data-value');
    geo = null;//注消全局全量 防止脏数据
    geoForce = null;
    geoType = null;
    //console.log(localGeo);
    if (localGeo !== null) {
      data = {'force[]': localForce, 'data[]': localGeo, 'namespace': namespace, 'type': localGeoType !== null ? 1 : 0};
    } else
      data += '&namespace=' + namespace;

    var len = $('td', $(tar)).length;
    var ajaxFlag = localGeoType !== null ? 0 : $(tar).attr('data-area');
    if (ajaxFlag != 1) {
      $.ajax({
        url: $(this).attr('data-url'),
        data: data,
        type: 'GET',
        success: function (re) {
          if (localGeo !== null && $(tar).next().hasClass('area_list_ajax'))
            $(tar).next().remove();
          if (!$(tar).next().hasClass('area_list_ajax'))
            $(tar).after('<tr class="area_list_ajax"><td colspan="' + len + '">' + re + '</td></tr>')
          $(tar).attr('data-area', '1');
        }
      });
    } else {
      var value = $('input:radio[name="' + namespace + '[geo][type]"]:checked', $(tar).next()).val();
      if (value == 0)
        $(parent).html(parentHtml.replace('有', '无'));
      else if (value > 0)
        $(parent).html(parentHtml.replace('无', '有'));
      $(tar).next().toggle();
    }

    return false;
  });

  //tr dom delete
  $('.tr_remove').on('click', function () {
    var table = $(this).attr('data-table');
    var parent = $(this).parent().parent();
    var tr_count = $('tr', $(parent).parent()).not('.area_list_ajax').length;
    if (tr_count > 1) {
      if ($(parent).next().hasClass('area_list_ajax')) {
        $($(parent).next()).remove();
      }
      $(parent).remove();
    } else {
      alert('最少要保留一个');
    }
    reset_table_index(table);
  });
  //tr add
  $('.tr_add').on('click', function () {

    var parent = $(this).parent().parent();
    var parentCopy = parent.clone();
    var container = parent.parent();
    var tr_last = $('.data:last', $(container));
    var table = $(this).attr('data-table');
    var index = parseInt($('#table-' + table).attr('data-count')) + 1;

    //设置索引
    var area_filter_tr = $(parent).find('.area_filter_tr').length;
    if (area_filter_tr > 0) {
      $('.area_filter_tr', $(parentCopy)).attr('data-namespace', table + '[' + index + ']');//这个可能会存的特列
      $('.area_filter_tr', $(parentCopy)).attr('data-value', '');
      $('.area_filter_tr', $(parentCopy)).parent().html($('.area_filter_tr', $(parentCopy)).parent().html().replace('有', '无'));
    }


    $('input', $(parentCopy)).each(function () {
      var name = this.name.replace(/\w*?\[\d*?\](\[\w*?\])/g, table + '[' + index + ']' + "$1");
      var id = this.id ? this.id.replace(/\w*?_\d*?_(\w*?)/g, table + '_' + index + '_' + "$1") : '';
      this.name = name;
      this.id = id;
      id = name = '';
    });

    $('input', parentCopy).val('');
    $('td:first', $(parentCopy)).html('<span class="data-index">' + index + '</span>');
    $(parentCopy).attr('data-area', '');
    $(container).append(parentCopy);
    $('#table-' + table).attr('data-count', index);
    reset_table_index(table);
  });

  $('.tr_rank').on('click', function () {
    var direction = $(this).attr('data-direction');
    var current = $(this).parent().parent();
    var table = $(this).attr('data-table');

    if (direction == 'up') {
      if ($(current).prev().length > 0) {
        if ($(current).prev().attr('class') == 'area_list_ajax') {
          prev = $(current).prev().prev();
        } else
          prev = $(current).prev();

        next_area = '';
        if ($(current).attr('data-area') == 1)
          next_area = $(current).next();
        $(current).insertBefore(prev);
        $(next_area).insertBefore(prev);
      }
    } else if (direction == 'down') {
      if ($(current).next(".data").length > 0 || $(current).next().next('.data').length > 0) {

        if ($(current).attr('data-area') != 1) {
          if ($(current).next().attr('data-area') != 1)
            next = $(current).next();
          else
            next = $(current).next().next();
        } else {
          if ($(current).next().next().attr('data-area') != 1)
            next = $(current).next().next();
          else
            next = $(current).next().next().next();
        }

        next_area = '';
        if ($(current).attr('data-area') == 1)
          next_area = $(current).next();

        $(next_area).insertAfter(next);
        $(current).insertAfter(next);

      }
    }

    reset_table_index(table)

  });

  /**
   * 用于自动生成表单ajax请求分析 根ID请求EPG
   */
  $('.ajaxBtn_analyse').on('click', function (event) {
    var current = $(this);
    var namespace = $(this).prev().attr('name').replace(/(\w*?\[\d*?\])\[\w*?\]/g, "$1");
    var tdFilter = $('a.area_filter_tr[data-namespace="' + namespace + '"]').parent();

    if (namespace == $(this).prev().attr('name'))//对新增的DOM做索引
      namespace = $(this).attr('data-namespace');

    var target = $(this).prev().val();
    if (!target) {
      alert('请填写要分析的内容');
      return false;
    }

    var dom_target = $(this).attr('data-target');
    var obj = document.getElementById(dom_target);
    if (obj && obj.type == 'select-one') {
      val = $('#' + dom_target + ' option:selected').val();
      if (!val || val == '0') {
        alert('请选择要分析的类型');
        return false;
      }
      dom_target_data = $('#' + dom_target).attr('data-' + val)
    } else
      dom_target_data = $(this).attr('data-data');


    if (dom_target_data) {
      var url = $(this).attr('data-url');
      var table = $(this).attr('data-table');
      var field = $(this).attr('data-field');
      $.ajax({
        url: url,
        data: 'target=' + target + '&table=' + table + '&field=' + field + '&data=' + encodeURIComponent(dom_target_data),
        type: 'POST',
        beforeSend: function () {
          current.attr('disabled', true).html('正在分析.....');
        },
        success: function (re) {
          json = $.parseJSON(re);
          if (json.status == 'success') {
            if (json.data) {
              for (key in json.data) {
                if (key == 'geo') {
                  if (json.data['geo']) {
                    if (json.data['geo'].type) {
                      $('input[name="' + namespace + '[geo][type]"][value=' + json.data['geo'].type + ']').attr('checked', 'checked');
                      $('div[class*="' + namespace + '[geo]"]').show();
                      geo = json.data['geo'].data;
                      geoForce = json.data['geo'].force;
                      geoType = json.data['geo'].type;
                      check_checkbox(namespace + '[geo][data][]', json.data['geo'].data, json.data['geo'].type);
                      resetGeoForce(namespace + '[geo][data][]', namespace + '[geo][force]', json.data['geo'].force, json.data['geo'].type);
                      if (tdFilter.length > 0) {
                        $(tdFilter).html(tdFilter.html().replace('无', '有'));
                        $('.area_filter_tr', tdFilter).trigger('click');
                      }
                    } else {
                      $('input[name="' + namespace + '[geo][type]"][value=0]').attr('checked', 'checked');
                      check_checkbox(namespace + '[geo][data][]', json.data['geo'].data, json.data['geo'].type);
                      resetGeoForce(namespace + '[geo][data][]', namespace + '[geo][force]', json.data['geo'].force, json.data['geo'].type);
                      if (tdFilter.length > 0) {
                        if (tdFilter.html().indexOf('无') == -1) {
                          $(tdFilter).html(tdFilter.html().replace('有', '无'));
                          $('.area_filter_tr', tdFilter).trigger('click');
                        }
                      }
                    }
                  }
                } else
                  $('input[name="' + namespace + '[' + key + ']"]').val(json.data[key]);
              }

            } else
              alert('分析失败')
          } else {
            alert(json.message);
          }
        }
      }).complete(function () {
          current.attr('disabled', false).html('分析');
        });
    } else
      alert('无法分析，请与管理员联系');
  });

  $('.JS_top').on('click', function () {
    var id = $(this).attr('data-id');
    var dataRow = $(this).attr('data-row');
    var table = dataRow.replace('_data_' + id, '');
    $.ajax({
      url: $(this).attr('data-url'),
      data: {'id': id, 'table': table},
      type: 'POST',

      success: function (re) {
        json = $.parseJSON(re);
        if (json.status == 'success') {
          alert('置顶成功');
          window.location.reload();
        } else {
          alert('置顶失败，稍后再试');
        }
      }
    });
  });

  $('.JPageSize').change(function () {
    var url = $('option:selected', $(this)).attr('data-url');
    if (url)
      window.location.href = url;
  });

  //geo force
  $('.JGeoForce').on('click', function (event) {
    var select = $(this).attr('data-select');
    var target = $(this).attr('data-space') + '[force]';
    var foreach = $(this).attr('data-space') + '[data][]';
    if (!select || select == 0) {
      $(this).attr('src', $(this).attr('data-strong-select'));
      $(this).attr('data-select', 1);
      resetGeoForce(foreach, target, false, 1);
      if ($(this).prev().attr('checked'))
        return false;
    } else {
      $(this).attr('src', $(this).attr('data-strong'));
      $(this).attr('data-select', 0);
      resetGeoForce(foreach, target, false, 1);
      return false;
    }
  });

  $('.JGeoCheck').on('click', function () {
    if ($(this).attr('checked') != 'checked') {
      var target = $(this).attr('data-space') + '[force]';
      var foreach = $(this).attr('data-space') + '[data][]';
      $(this).next().attr('src', $(this).next().attr('data-strong'));
      $(this).next().attr('data-select', 0);
      resetGeoForce(foreach, target, false, 1);
    }
  });

  //用于首页提交同步数据

  $('.synchroData, .refreshTimingCheck').click(function () {
    $('.check-epg').css('display', 'none');
    $('.span-refresh').css('display', 'block');
    $.ajax({url: $(this).attr('data-url'), type: 'post'})
      .done(function (data) {
        location.reload();
      }).fail(function () {
        alert('failure');
      });
  });

  $('.scroller table tbody tr').click(function () {
    location.href = $(this).attr('data-url');
  });
});

function uploadCallback(error, data) {
  if (error != null) {
    alert(error);
  }
  else {
    data = decodeURI(data);
    var object = JSON.parse(data);
    $('input[image-uploader=true]').val(object.url);
    $('input[name=photo_width]').val(object.width);
    $('input[name=photo_height]').val(object.height);
    $('#image-uploader-iframe').attr('src', $('#image-uploader-iframe').attr('src'));
  }
  $('#image-uploader-container').hide();
}

function reset_table_index(table) {
  $('#table-' + table + ' .data-index').each(function (index) {
    $(this).html(index + 1);
  });
}

function check_checkbox(name, data, checked) {
  $('input[name="' + name + '"]').attr('checked', false);

  if (checked == 1) {
    $('input[name="' + name + '"]').each(function () {
      if ($.inArray(parseInt($(this).val()), data) == -1) {
        $(this).attr('checked', 'true');
      }
    })
  } else if (checked == 2) {
    $('input[name="' + name + '"]').each(function () {
      if ($.inArray(parseInt($(this).val()), data) != -1) {
        $(this).attr('checked', 'true');
      }
    })
  }
}

//data=false 表示点击强屏图标
function resetGeoForce(foreach, target, data, type) {
  var forceData = [];
  $('input[name="' + foreach + '"]').each(function () {
    if (data !== false) {
      if (data.length > 0 && type == 1) {
        if ($.inArray(parseInt($(this).val()), data) == -1) {
          $(this).next().attr('data-select', 1);
          $(this).next().attr('src', $(this).next().attr('data-strong-select'));
        } else {
          $(this).next().attr('data-select', 0);
          $(this).next().attr('src', $(this).next().attr('data-strong'));
        }
      } else if (type == 1) {
        $(this).next().attr('data-select', 1);
        $(this).next().attr('src', $(this).next().attr('data-strong-select'));
      } else {
        $(this).next().attr('data-select', 0);
        $(this).next().attr('src', $(this).next().attr('data-strong'));
      }
    }

    if ($(this).next().attr('data-select') == 1)
      forceData.push($(this).next().attr('data-geo'));
  });
  if (forceData.length > 0)
    $('input[name="' + target + '"]').val(forceData.join(','))
  else
    $('input[name="' + target + '"]').val('');
}


//plugin bg

/*
 * Toastr
 * Version 2.0.1
 * Copyright 2012 John Papa and Hans Fjällemark.
 * All Rights Reserved.
 * Use, reproduction, distribution, and modification of this code is subject to the terms and
 * conditions of the MIT license, available at http://www.opensource.org/licenses/mit-license.php
 *
 * Author: John Papa and Hans Fjällemark
 * Project: https://github.com/CodeSeven/toastr
 */
;
(function (define) {
  define(['jquery'], function ($) {
    return (function () {
      var version = '2.0.1';
      var $container;
      var listener;
      var toastId = 0;
      var toastType = {
        error: 'error',
        info: 'info',
        success: 'success',
        warning: 'warning'
      };

      var toastr = {
        clear: clear,
        error: error,
        getContainer: getContainer,
        info: info,
        options: {},
        subscribe: subscribe,
        success: success,
        version: version,
        warning: warning
      };

      return toastr;

      //#region Accessible Methods
      function error(message, title, optionsOverride) {
        return notify({
          type: toastType.error,
          iconClass: getOptions().iconClasses.error,
          message: message,
          optionsOverride: optionsOverride,
          title: title
        });
      }

      function info(message, title, optionsOverride) {
        return notify({
          type: toastType.info,
          iconClass: getOptions().iconClasses.info,
          message: message,
          optionsOverride: optionsOverride,
          title: title
        });
      }

      function subscribe(callback) {
        listener = callback;
      }

      function success(message, title, optionsOverride) {
        return notify({
          type: toastType.success,
          iconClass: getOptions().iconClasses.success,
          message: message,
          optionsOverride: optionsOverride,
          title: title
        });
      }

      function warning(message, title, optionsOverride) {
        return notify({
          type: toastType.warning,
          iconClass: getOptions().iconClasses.warning,
          message: message,
          optionsOverride: optionsOverride,
          title: title
        });
      }

      function clear($toastElement) {
        var options = getOptions();
        if (!$container) {
          getContainer(options);
        }
        if ($toastElement && $(':focus', $toastElement).length === 0) {
          $toastElement[options.hideMethod]({
            duration: options.hideDuration,
            easing: options.hideEasing,
            complete: function () {
              removeToast($toastElement);
            }
          });
          return;
        }
        if ($container.children().length) {
          $container[options.hideMethod]({
            duration: options.hideDuration,
            easing: options.hideEasing,
            complete: function () {
              $container.remove();
            }
          });
        }
      }

      //#endregion

      //#region Internal Methods

      function getDefaults() {
        return {
          tapToDismiss: true,
          toastClass: 'toast',
          containerId: 'toast-container',
          debug: false,

          showMethod: 'fadeIn', //fadeIn, slideDown, and show are built into jQuery
          showDuration: 300,
          showEasing: 'swing', //swing and linear are built into jQuery
          onShown: undefined,
          hideMethod: 'fadeOut',
          hideDuration: 1000,
          hideEasing: 'swing',
          onHidden: undefined,

          extendedTimeOut: 1000,
          iconClasses: {
            error: 'toast-error',
            info: 'toast-info',
            success: 'toast-success',
            warning: 'toast-warning'
          },
          iconClass: 'toast-info',
          positionClass: 'toast-top-right',
          timeOut: 5000, // Set timeOut and extendedTimeout to 0 to make it sticky
          titleClass: 'toast-title',
          messageClass: 'toast-message',
          target: 'body',
          closeHtml: '<button>&times;</button>',
          newestOnTop: true
        };
      }

      function publish(args) {
        if (!listener) {
          return;
        }
        listener(args);
      }

      function notify(map) {
        var
          options = getOptions(),
          iconClass = map.iconClass || options.iconClass;

        if (typeof (map.optionsOverride) !== 'undefined') {
          options = $.extend(options, map.optionsOverride);
          iconClass = map.optionsOverride.iconClass || iconClass;
        }

        toastId++;

        $container = getContainer(options);
        var
          intervalId = null,
          $toastElement = $('<div/>'),
          $titleElement = $('<div/>'),
          $messageElement = $('<div/>'),
          $closeElement = $(options.closeHtml),
          response = {
            toastId: toastId,
            state: 'visible',
            startTime: new Date(),
            options: options,
            map: map
          };

        if (map.iconClass) {
          $toastElement.addClass(options.toastClass).addClass(iconClass);
        }

        if (map.title) {
          $titleElement.append(map.title).addClass(options.titleClass);
          $toastElement.append($titleElement);
        }

        if (map.message) {
          $messageElement.append(map.message).addClass(options.messageClass);
          $toastElement.append($messageElement);
        }

        if (options.closeButton) {
          $closeElement.addClass('toast-close-button');
          $toastElement.prepend($closeElement);
        }

        $toastElement.hide();
        if (options.newestOnTop) {
          $container.prepend($toastElement);
        } else {
          $container.append($toastElement);
        }


        $toastElement[options.showMethod](
          { duration: options.showDuration, easing: options.showEasing, complete: options.onShown }
        );
        if (options.timeOut > 0) {
          intervalId = setTimeout(hideToast, options.timeOut);
        }

        $toastElement.hover(stickAround, delayedhideToast);
        if (!options.onclick && options.tapToDismiss) {
          $toastElement.click(hideToast);
        }
        if (options.closeButton && $closeElement) {
          $closeElement.click(function (event) {
            event.stopPropagation();
            hideToast(true);
          });
        }

        if (options.onclick) {
          $toastElement.click(function () {
            options.onclick();
            hideToast();
          });
        }

        publish(response);

        if (options.debug && console) {
          console.log(response);
        }

        return $toastElement;

        function hideToast(override) {
          if ($(':focus', $toastElement).length && !override) {
            return;
          }
          return $toastElement[options.hideMethod]({
            duration: options.hideDuration,
            easing: options.hideEasing,
            complete: function () {
              removeToast($toastElement);
              if (options.onHidden) {
                options.onHidden();
              }
              response.state = 'hidden';
              response.endTime = new Date(),
                publish(response);
            }
          });
        }

        function delayedhideToast() {
          if (options.timeOut > 0 || options.extendedTimeOut > 0) {
            intervalId = setTimeout(hideToast, options.extendedTimeOut);
          }
        }

        function stickAround() {
          clearTimeout(intervalId);
          $toastElement.stop(true, true)[options.showMethod](
            { duration: options.showDuration, easing: options.showEasing }
          );
        }
      }

      function getContainer(options) {
        if (!options) {
          options = getOptions();
        }
        $container = $('#' + options.containerId);
        if ($container.length) {
          return $container;
        }
        $container = $('<div/>')
          .attr('id', options.containerId)
          .addClass(options.positionClass);
        $container.appendTo($(options.target));
        return $container;
      }

      function getOptions() {
        return $.extend({}, getDefaults(), toastr.options);
      }

      function removeToast($toastElement) {
        if (!$container) {
          $container = getContainer();
        }
        if ($toastElement.is(':visible')) {
          return;
        }
        $toastElement.remove();
        $toastElement = null;
        if ($container.children().length === 0) {
          $container.remove();
        }
      }

      //#endregion

    })();
  });
}(typeof define === 'function' && define.amd ? define : function (deps, factory) {
    if (typeof module !== 'undefined' && module.exports) { //Node
      module.exports = factory(require(deps[0]));
    } else {
      window['toastr'] = factory(window['jQuery']);
    }
  }));

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
