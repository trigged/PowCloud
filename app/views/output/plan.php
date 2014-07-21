<html>
<body>

<?php
if (isset($data)) {
    foreach ($data as $property) {
        $text = '';
        foreach ($property as $value) {
            $text .= $value . "   ";
        }
        if (!empty($text)) {
            echo "<h3>$text</h3>";
        }
    }

} else {
    echo "<h4>您请求的方式有问题 请和管理员联系</h4>";
}
?>


</body>
</html>
