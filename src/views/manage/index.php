<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

</head>

<body>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>

<script type="text/javascript">

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }

    $(function () {
        var url = "index.php?action=manage.index&page=home&lang=" + getLanguage();
        location.href = url;
    });

</script>

</body>
</html>




