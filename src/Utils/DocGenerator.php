<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 12/12/13
 * Time: 6:07 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Utils;


use Forms;
use Operator\ReadApi;

class DocGenerator
{

    static $template_title = '# %s';

    static $template_filed = '%s // %s';

    static $model = null;

    static $path = '/path';

    static $domain = 'domain';

    static $params = '?version=1&token=';

    static $template_about = '

## About

&copy; 2013 [PPTV](http://www.pptv.com)  [Invoation team](http://p.demo1.pptv.com/w/tv_collection/server_protocol/)

    ';

    static $template_not_support = "\r\n`503 这个方法不允许访问, 没有开启这个权限`";

    static $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">

<link href="css/markdown.css" media="all" rel="stylesheet" type="text/css" />
</head>

<body>
%s

</body>
</html>
';

    static $template_model = '
## 模型

```
%s
```
';

    static $template_result = '
### API 返回


所有的请求均返回以下结构,data 的具体内容见每一个接口返回的 response

```
{
  "code": 1,  //-1 failed ,1 success
  "message": "message",
  "data":    result
}
```';

    static $template_index = '
### all

```
[GET] request: http://%s%s%s
```

```
response :

[
 %s
  ,models,
]
```

';

    static $template_create = '
### create

```
[POST] request: http://%s%s%s

POST_DATA  {"data":model}
```

```
response :

 %s
```
';

    static $template_update = '
### update
```
[PUT] request: http://%s%s/id%s
POST_DATA  {"field":value}
```

```
response :

{
  %s
}
```';

    static $template_delete = '
### delete

```
[DELETE] request: http://%s%s/id%s
```

```
response

null
```';

    static function all()
    {
        return 'create markdown html';
    }

    public static function getDoc($table_name, $app_id)
    {
        $table = ReadApi::getTableInfo($table_name);
        $path = \Path::find($table['path_id']);
        if ($path && $path->exists) {
            $path = $path->toArray();

            if ($path['name']) {
                self::$path = $path['name'];
            }
            if ($path['host_id']) {
                $host = \Host::find($path['host_id'])->toArray();
                if ($host['host']) {
                    self::$domain = $host['host'];
                }
            }
        }

        self::buildParams($app_id);

        $doc = '';
        $model = self::getModels($table['id'], $table_name, $table['property']);

        $title = sprintf(self::$template_title, $table['table_name']);
        if ($table['table_alias']) {
            $title .= sprintf('(%s)', $table['table_alias']);
        }

        if ($table['index'] == 1) {
            $all = self::index();
        } else {
            $all = self::notSupport('all');
        }
        if ($table['create'] == 1) {
            $create = self::create();
        } else {
            $create = self::notSupport('create');
        }
        if ($table['update'] == 1) {
            $update = self::update();
        } else {
            $update = self::notSupport('update');
        }

        if ($table['delete'] == 1) {
            $delete = self::delete();
        } else {
            $delete = self::notSupport('delete');
        }
        $model = sprintf(self::$template_model, $model);
        $doc .= $title . $model . self::$template_result . $all . $create . $update . $delete . self::$template_about;
        require_once('markdown_extended.php');

        $my_html = MarkdownExtended($doc);
        return sprintf(self::$html, $my_html);

    }

    static function  buildParams($app_id)
    {
        $token = UseHelper::makeToken($app_id);
        self::$params .= $token;

    }

    static function getModels($id, $table_name, $property)
    {
        $model = null;
        //get models filed common

        $forms = Forms::where('models_id', $id)->get()->toArray();
        $data = array();
        $result = '{ ';
        $length = 0;
        foreach ($forms as $form) {
            if ($form['field'] != 'geo') {
                $data[$form['field']] = $form['label'];
                $field_length = strlen($form['field']);
                if ($length < $field_length) {
                    $length = $field_length;
                }
            }
        }

        if (is_string($property)) {
            $property = json_decode($property, true);
        }
        if (isset($property['geo'])) {
            unset($property['geo']);
        }
        if (isset($property['parent'])) {
            unset($property['parent']);
        }
        $children = null;
        if (isset($property['children'])) {
            $tmp = $property['children']['default'];
            $value = explode(':', $tmp);
            if (count($value) == 2) {
                $field = $value[0];
                $sub_table_name = $value[1];
                if (isset($data[$field])) {
                    $data[$field] = $data[$field] . '[' . $sub_table_name . ',....]';
                } else {
                    $data[$field] = '[' . $sub_table_name . '_id,....]';
                }

                $table = ReadApi::getTableInfo($sub_table_name);
                $children = self::getModels($table['id'], $sub_table_name, $table['property']);
            }
            unset($property['children']);
        }
        $property['id'] = null;
        foreach ($property as $field => $value) {
            if ($field == 'template') {
                //have no idea to handler this
            }

            $result .= "\r\n  " . $field . str_repeat(' ', $length - strlen($field) + 5);
            if (isset($data[$field])) {
                $result .= "// " . $data[$field];;
            }
        }
        $result .= "\r\n}";
        self::$model = $result;
        $result = $table_name . ' :' . $result;
        if ($children) {
            $result .= "\r\n\r\n" . $children;
        }
        return $result;
    }

    static function index()
    {
        return sprintf(self::$template_index, self::$domain, self::$path, self::$params, self::$model);
    }

    static function notSupport($method)
    {
        return '
###' . $method . self::$template_not_support;
    }

    static function create()
    {
        return sprintf(self::$template_create, self::$domain, self::$path, self::$params, self::$model);

    }

    static function update()
    {
        return sprintf(self::$template_update, self::$domain, self::$path, self::$params, self::$model);
    }

    static function delete()
    {
        return sprintf(self::$template_delete, self::$domain, self::$path, self::$params);
    }
}