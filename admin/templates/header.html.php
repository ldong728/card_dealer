<!DOCTYPE html>
<html lang="cn">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title><?php echo $title ?></title>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet/style.css?v=<?php echo rand(1000, 9999) ?>">
    <link rel="stylesheet" type="text/css" href="stylesheet/style2.css?v=<?php echo rand(1000, 9999) ?>">
    <script src="js/html5.js"></script>
    <style>
        dt {
            cursor: pointer;
        }
    </style>

</head>



<body>
<div class="header">
    <h1><img src="logo/logo.png"/></h1>
    <ul class="rt_nav">
        <li><a href="#" target="_blank" class="website_icon">站点首页</a></li>
        <li><a href="index.php?logout=1" class="quit_icon">安全退出</a></li>
    </ul>
</div>
<div class="toast"></div>
<div class="loading"></div>
<aside class="lt_aside_nav content mCustomScrollbar">
    <h2><a href="index.php">起始页</a></h2>
    <ul>
        <?php foreach($_SESSION['pms'] as $key=>$row):?>
            <li>
                <dl class="main-menu">
                    <dt><?php echo $row['name']?></dt>
                    <?php foreach($row['sub'] as $subrow):?>
                        <dd style="display: <?php echo isset($_GET['menu'])&&$_GET['menu']==$row['key'] ? 'block' : 'none' ?>"><a href="index.php?menu=<?php echo $key?>&sub=<?php echo $subrow['key']?>"><?php echo $subrow['name']?></a>
                        </dd>
                    <?php endforeach ?>
                </dl>
            </li>
        <?php endforeach ?>
    </ul>
</aside>
<script>
    $('dt').click(function () {
        $(this).nextAll('dd').slideToggle('fast');
    });
    var pms="<?php echo $_GET['menu']?>";
</script>
<section class="rt_wrap content mCustomScrollbar">
    <div cla="rt_content">


