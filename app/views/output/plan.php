<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="<?php echo URL::asset('css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo URL::asset('css/bootstrap-responsive.css'); ?>" rel="stylesheet">
</head>
<body>

<?php
if (isset($data)) {
    foreach ($data as $property) {
        $text = '';
        foreach ($property as $value) {
            $text .= $value . "   ";
        }
        if (!empty($text)) {
            echo '<h3 class="text-center">' . $text . '</h3>';
        }
    }

} else {
    echo "<h4>您请求的方式有问题 请和管理员联系</h4>";
}
?>


</body>
</html>
