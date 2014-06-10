
<?php

$fillArray = array();
$fill = false;

function getStyle($width,$height){
    if(empty($GLOBALS['fillArray'])){
        $GLOBALS['fillArray']['height'] = $height;
        $GLOBALS['fillArray']['width'] = $width;
        return  'style="position: absolute;left: 0;top: 0;"';
    }elseif((int)$GLOBALS['fillArray']['height']==358){
        $GLOBALS['fillArray']['height'] = $height;
        $w = $GLOBALS['fillArray']['width'];
        if(isset($GLOBALS['maxWidth']) && $GLOBALS['maxWidth']===false){
            $GLOBALS['fillArray']['height'] = 358;
            $GLOBALS['fillArray']['width'] +=190;
        }
        $GLOBALS['maxWidth'] = $GLOBALS['fillArray']['width'] + $width;
        return  'style="position: absolute;left: '.$w.'px;top: 0;"';
    }elseif ((int)$GLOBALS['fillArray']['height']<358){
        $w = $GLOBALS['fillArray']['width'];
        $h = $GLOBALS['fillArray']['height'];
        if($GLOBALS['maxWidth']>($w+$width)){
            $GLOBALS['fillArray']['width'] = $GLOBALS['fillArray']['width'] +$width;
        }elseif($GLOBALS['maxWidth']==($w+$width)){
            $GLOBALS['fillArray']['width'] = $GLOBALS['maxWidth'];
            $GLOBALS['fillArray']['height'] = 358;
        }elseif($GLOBALS['maxWidth']<($w+$width)){
            $GLOBALS['fillArray']['width'] = $GLOBALS['fillArray']['width']+190;
            $GLOBALS['maxWidth'] =false;
            $GLOBALS['fillArray']['height'] = 358;
        }

        return  'style="position: absolute;left: '.$w.'px;top: '.$h.'px;overflow:hidden;"';
    }
    return '';
}

function show_api()
{
    $condition = '';
    if (isset($_GET['geo'])) {
        $condition = '&geo='.$_GET['geo'];
    }

    $url_home = Config::get('app.url') . '/home?version=0.003' . $condition;
    $home_json = call_api('get', $url_home);
    $home_array = json_decode($home_json, true);
    if (isset($home_array['code']) && $home_array['code'] === 1 && isset($home_array['data'])) {
        return $home_array['data'];
    }
    return null;
}

function call_api($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    return curl_exec($curl);
}

$data = show_api();
if (empty($data)) {
    return 'data error!';
}
?>



<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title>数据管理平台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="<?php echo URL::asset('css/bootstrap.css');?>" rel="stylesheet">
    <link href="<?php echo URL::asset('css/bootstrap-responsive.css');?>" rel="stylesheet">

    <link rel="shortcut icon" href="<?php echo URL::asset('img/favicon.ico'); ?> ">

    <link href="<?php echo URL::asset('css/x-man.css'); ?> " rel="stylesheet">

    <script src="<?php echo URL::asset('js/jquery-1.8.3.min.js'); ?>"></script>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="<?php echo URL::asset('js/html5shiv.js'); ?>"></script>
    <![endif]-->
    <script>
        document.domain =  '<?php echo \Utils\Env::getRootDomain(); ?>';
    </script>
    <style>
        .container{
            background: url("http://img.vision.demo1.pptv.com/images/a2/73/a27355eb9df26293584441b97f505bfa47dab271.jpeg");
            padding: 40px;
            width: auto;
            overflow-y:hidden;
            overflow-x:auto ;
        }
        .title{
            position: absolute;
            bottom: 0;
            left: 20%;
            color: #000000;
        }
        .metroLayOut{
            position: relative;
            height: 358px;
        }
        .home_large{
            width: 236px;
            height: 358px;
            overflow: hidden;
        }
        .home_special,.home_special_img_bg{
            width: 380px;
            height: 179px;
        }
        .home_special_img_left{
            position: absolute;
            width: 150px;
            height: 180px;
            left: 0;
            bottom: 0;
        }
        .home_special_img_right{
            position: absolute;
            width: 200px;
            right: 0;
            bottom: 0;
            padding: 0;
        }

        .home_video,.home_history{
            width: 190px;
            height: 179px;
            overflow: hidden;
        }

        .home_large_img{
            height: 358px;
        }
        .home_special_img{
            height: 190px;
            width: 380px;
        }

        .home_video_img,.home_history_img{
            width: 190px;
            height: 179px;
        }
        .areaFilter{
            position: relative;
            z-index: 9999;
        }
        img{
            padding: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="metroLayOut">
        <?php foreach ($data as $index => $ra): ?>

            <?php if((int)$index===2) : ?>
                <div class="home_history ic"   <?php echo getStyle('190','179') ?>>
                    <img class="home_history_img" src="http://img.vision.demo1.pptv.com/images/92/a7/92a71e6048507278b413ca555bda5f63c9002807.jpeg" >
                </div>
                <div class="home_video ic"  <?php echo getStyle('190','179') ?>>
                    <img class="home_video_img" src="<?php echo $ra['bgimg'] ?>" >
                    <span class="title"><?php echo $ra['title']; ?></span>
                </div>
            <?php elseif(isset($ra['content_type'])) : ?>
                <div class="home_large ic" data-type="home_large" <?php echo getStyle('236','358') ?>>
                    <img class="home_large_img" src="<?php echo $ra['bgimg'] ?>" >
                    <span class="title"><?php echo $ra['title']; ?></span>
                </div>
            <?php elseif(isset($ra['imgs'])) : ?>
                 <div class="home_special ic"  <?php echo getStyle('380','179') ?>>
                    <div class="home_special_img_bg" style="overflow: hidden">
                        <img class="home_special_img" src="<?php echo $ra['bgimg'] ?>" >
                    </div>
                        <?php if(!empty($ra['imgs'][0])) : ?>
                            <img class="home_special_img_left" src="<?php echo $ra['imgs'][0] ?>" >
                        <?php endif; ?>
                        <?php if(!empty($ra['imgs'][1])) : ?>
                            <img class="home_special_img_right" src="<?php echo $ra['imgs'][1] ?>" >
                        <?php endif; ?>
                     <span class="title"><?php echo $ra['title']; ?></span>
                 </div>
            <?php  else: ?>
                <div class="home_video ic"  <?php echo getStyle('190','179') ?>>
                    <img class="home_video_img" src="<?php echo $ra['bgimg'] ?>" >
                    <span class="title"><?php echo $ra['title']; ?></span>
                </div>
        <?php endif;endforeach;?>
    </div>
    <div style="clear: both;"></div>
</div>
<div class="areaFilter">
    <div  class="area_list well" id="">
        <ul class="area_city clearfix" style="margin:0 0 10px 0;">
            <?php foreach(Config::get('params.areaFilterList') as $areaCode=>$areaName):?>
                <li style="float: left;list-style: none;width: 120px;">
                    <label class="checkbox inline">
                        <input class="JLocation" data-url="http://cms.troyfan.demo1.pptv.com/show_api?geo=<?php echo $areaCode ?>" type="radio" id="" name="geo" <?php echo (isset($_GET['geo']) && $areaCode==$_GET['geo'])?'checked="checked"':''; ?> value="<?php echo $areaCode;?>"> <?php echo $areaName;?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
        $(function(){
            $('.JLocation').click(function(){
                var h = $(this).attr('data-url');
                console.log(111);
                window.location.href = h;
            })
        })
    </script>
</div>
<script src="<?php echo URL::asset('js/bootstrap.min.js'); ?> "></script>
</body>
</html>




