<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\phpStudy2\PHPTutorial\WWW\tp5\public/../application/index\view\index\index.html";i:1560736909;}*/ ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
        <title></title>
        <script src="/static/jquery.min.js" ></script>
    </head>
    <body>
        <div class="margin-div" style="width:500px;margin:200px auto;">
            <a href="#" class="btn" lang="zh-cn">中文</a>
            <a href="#" class="btn" lang="en-us">英文</a>
            <div id="mydiv"><?php echo lang('name1'); ?></div>
            <div id="mydiv"><?php echo lang('name2'); ?></div>
            <div id="mydiv"><?php echo lang('name3'); ?></div>
            <div id="mydiv"><?php echo lang('name4'); ?></div>
            <div id="mydiv"><?php echo lang('name5'); ?></div>
            <div id="mydiv"><?php echo $goods_info['name']; ?></div>
        </div>

        <script>
            $(function () {
                $('.btn').click(function () {
                    var data = {'lang': $(this).attr('lang')};
                    $.get("<?php echo url('index/lang'); ?>", data, function () {
                        location.reload();
                    })
                })
            })
        </script>
    </body>
</html>