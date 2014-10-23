<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>江湖邀请令</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <!--    <link href="--><?php //echo URL::asset('css/bootstrap.css'); ?><!--" rel="stylesheet">-->
    <!--    <link href="--><?php //echo URL::asset('css/bootstrap-responsive.css'); ?><!--" rel="stylesheet">-->
</head>
<body>
<div class="container">
    <style>

        .jumbotron {
            padding: 60px;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 200;
            line-height: 30px;
            color: inherit;
            background-color: #eeeeee;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
        }

        .btn {
            border: none;
            background: #bdc3c7;
            color: #ffffff;
            padding: 9px 12px 10px;
            line-height: 22px;
            text-decoration: none;
            text-shadow: none;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
            -webkit-transition: 0.25s;
            -moz-transition: 0.25s;
            -o-transition: 0.25s;
            transition: 0.25s;
            -webkit-backface-visibility: hidden;
        }

        .btn:hover,
        .btn:focus,
        .btn-group:focus .btn.dropdown-toggle {
            background-color: #cacfd2;
            color: #ffffff;
            outline: none;
            -webkit-transition: 0.25s;
            -moz-transition: 0.25s;
            -o-transition: 0.25s;
            transition: 0.25s;
            -webkit-backface-visibility: hidden;
        }

        .btn:active,
        .btn-group.open .btn.dropdown-toggle,
        .btn.active {
            background-color: #a1a6a9;
            color: rgba(255, 255, 255, 0.75);
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
        }

        .btn.btn-lg {
            font-size: 16.996px;
            /* 17px */

            line-height: 20px;
            padding: 12px 18px 13px;
        }

        .btn.btn-large > [class^="fui-"] {
            top: 0;
        }

        .btn.btn-large > [class^="fui-"].pull-right {
            margin-right: -2px;
        }

        .btn.btn-primary {
            background-color: #1abc9c;
        }

        .btn.btn-primary:hover,
        .btn.btn-primary:focus,
        .btn-group:focus .btn.btn-primary.dropdown-toggle {
            background-color: #48c9b0;
        }

        .btn.btn-primary:active,
        .btn-group.open .btn.btn-primary.dropdown-toggle,
        .btn.btn-primary.active {
            background-color: #16a085;
        }

        .jumbotron h1 {
            margin-bottom: 0;
            font-size: 60px;
            line-height: 1;
            letter-spacing: -1px;
            color: inherit;
        }

        .jumbotron li {
            line-height: 30px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 10px 0;
            font-family: inherit;
            font-weight: bold;
            line-height: 20px;
            color: inherit;
            text-rendering: optimizelegibility;
        }

        h1 small,
        h2 small,
        h3 small,
        h4 small,
        h5 small,
        h6 small {
            font-weight: normal;
            line-height: 1;
            color: #999999;
        }

        h1,
        h2,
        h3 {
            line-height: 40px;
        }

        h1 {
            font-size: 38.5px;
        }

    </style>

    <div class="jumbotron">
        <h1>江湖邀请令</h1>

        <p>您的小伙伴邀请您加入 pow server和他一起闯荡江湖,这个邀请15分钟内有效,请少侠注意哦</p>

        <p>
            <a href="<?php echo $url ?>" class="btn btn-primary btn-lg">
                点击这里 加入系统
            </a>
        </p>

        <p>
            如果您的系统无法点击可以通过下方的网址
        </p>

        <p>
            <?php echo $url ?>
        </p>


    </div>
</div>
</body>
</html>