<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title>数据管理平台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="<?php echo URL::asset('css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo URL::asset('css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo URL::asset('css/pow_style.css'); ?>" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo URL::asset('img/favicon.ico'); ?> ">
    <link href="<?php echo URL::asset('css/essage.css'); ?> " rel="stylesheet">
    <link href="<?php echo URL::asset('bower_components/bootstrap-select/dist/css/bootstrap-select.css'); ?> "
          rel="stylesheet">
    <script src="<?php echo URL::asset('/bower_components/jquery/dist/jquery.min.js'); ?>"></script>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="<?php echo URL::asset('js/html5shiv.js'); ?>"></script>
    <![endif]-->
    <script>
        document.domain = '<?php echo \Utils\Env::getRootDomain(); ?>';
        var webconfig = <?php echo json_encode($webconfig);?>;
        var page = <?php  echo empty($_GET['page'])?0:($_GET['page']>1?$_GET['page']:0); ?>;
    </script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <!--            <a data-target=".navbar-collapse" data-toggle="collapse" class="btn navbar-btn">-->
            <!--                <span class="glyphicon glyphicon-bar"></span>-->
            <!--                <span class="glyphicon glyphicon-bar"></span>-->
            <!--                <span class="glyphicon glyphicon-bar"></span>-->
            <!--            </a>-->
            <a class="pow-brand" href="<?php echo URL::action('DashBoardController@index') ?>">数据管理平台</a>

            <div class="pull-right">
                <ul class="nav pull-left pow_main_nav">
                    <?php foreach ($navs as $cur => $navMenu): ?>
                        <li class="<?php echo $nav === $cur ? 'active' : ''; ?> pull-left">
                            <a <?php if (!empty($navMenu['target'])) echo 'target="' . $navMenu['target'] . '"' ?>
                                href="<?php echo $navMenu['url']; ?>">
                                <?php echo $navMenu['label']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="navbar-text pull-right">
                    欢迎 <a href="javascript:void(0);"><?php echo Auth::user()->name; ?></a> 登陆，<a
                        href="<?php echo URL::action('LoginController@logout') ?>">注销</a>
                </p>
            </div>
            <!--/.navbar-collapse -->
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="">
        <?php if ($leftMenu): ?>
            <div class="col-md-2">
                <div class="sidebar-nav">
                    <ul class="nav nav-list">
                        <?php foreach ($leftMenu as $group => $other): ?>
                            <li class="nav-header"><?php echo $group ?></li>
                            <?php foreach ($other as $lm): ?>
                                <?php if (!$lm['url']): ?>
                                    <li class="nav-header"><?php echo $lm['label']; ?></li>
                                <?php else: ?>
                                    <li class="<?php echo isset($lm['menu']) && $menu === $lm['menu'] ? 'active' : ''; ?>">
                                        <a href="<?php echo $lm['url'] ?>"><?php echo $lm['label']; ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        <?php endif; ?>
        <div class="col-md-10" style="padding-bottom:20px">

