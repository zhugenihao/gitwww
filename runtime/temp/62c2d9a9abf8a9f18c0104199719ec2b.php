<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\phpStudy2\PHPTutorial\WWW\tp5\public/../application/index\view\index\index.html";i:1559024315;}*/ ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
        <title></title>
        <script src="/static/jquery.min.js" ></script>
    </head>
    <body>
        <div id="mydiv">按钮</div>
        <script>
            $(function () {
                $("#mydiv").mousedown(function () {
                    setTimeout(function () {
                        alert('你长按了');
                    }, 400);
                });
            })
        </script>
    </body>
</html>