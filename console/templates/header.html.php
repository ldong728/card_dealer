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
        <li>
            <dl class="main-menu">
                <dt>首页管理</dt>
<!--                --><?php //if (isset($_SESSION['pms']['index'])): ?>
<!--                <dd style="display: --><?php //echo isset($_GET['index']) ? 'block' : 'none' ?><!--"><a href="index.php?index=1">首页编辑</a>-->
<!--                    </dd>--><?php //endif ?>
                <?php if (isset($_SESSION['pms']['index'])): ?>
                <dd style="display: <?php echo isset($_GET['index']) ? 'block' : 'none' ?>"><a href="index.php?categorylist=1">内容分类</a>
                    </dd><?php endif ?>
            </dl>
        </li>
        <li>
            <dl class="main-menu">
                <dt>分组管理</dt>
                <?php if (isset($_SESSION['pms']['group'])): ?>
                <dd style="display: <?php echo isset($_GET['groupManager']) ? 'block' : 'none' ?>"><a
                            href="index.php?groupManager=1&groupList=1">分组列表</a></dd><?php endif ?>
            </dl>
        </li>
        <li>
            <dl class="main-menu">
                <dt>用户管理</dt>
                <?php if (isset($_SESSION['pms']['user'])): ?>
                <dd style="display: <?php echo isset($_GET['user']) ? 'block' : 'none' ?>"><a
                            href="index.php?user=1&userList=1">用户列表</a></dd><?php endif ?>
            </dl>
        </li>
        <li>
            <dl class="main-menu">
                <dt>通知管理</dt>
                <?php if (isset($_SESSION['pms']['notice'])): ?>
                <dd style="display: <?php echo isset($_GET['notice']) ? 'block' : 'none' ?>"><a
                            href="index.php?notice=1&newNotice=-1">新建通知</a></dd><?php endif ?>
                <?php if (isset($_SESSION['pms']['notice'])): ?>
                <dd style="display: <?php echo isset($_GET['notice']) ? 'block' : 'none' ?>"><a
                            href="index.php?notice=1&sendNotice=-1">发送通知</a></dd><?php endif ?>
                <?php if (isset($_SESSION['pms']['notice'])): ?>
                <dd style="display: <?php echo isset($_GET['notice']) ? 'block' : 'none' ?>"><a
                            href="index.php?notice=1&noticeList=0">状态查询</a></dd><?php endif ?>
                <?php if (isset($_SESSION['pms']['notice'])): ?>
                <dd style="display: <?php echo isset($_GET['notice']) ? 'block' : 'none' ?>"><a
                            href="index.php?notice=1&orders=1">历史通知</a></dd><?php endif ?>
            </dl>
        </li>
        <?php if (isset($_SESSION['pms']['news'])): ?>
            <li>
                <dl class="main-menu">
                    <dt>图文信息管理</dt>
                    <dd style="display: <?php echo isset($_GET['news']) ? 'block' : 'none' ?>"><a
                            href="index.php?news=1&newslist=1">图文信息列表</a></dd>
                    <dd style="display: <?php echo isset($_GET['news']) ? 'block' : 'none' ?>"><a
                            href="index.php?news=1&createNews=2">新建图文信息</a></dd>
                    <!--                    <dd><a href="index.php?sdp=1&usersdp=1">微商管理</a></dd>-->
                    <!--                    <dd><a href="index.php?sdp=1&sdpInf=1">数据分析</a></dd>-->
                </dl>
            </li>
        <?php endif ?>
        <?php if(isset($_SESSION['pms']['jm'])):?>
        <li>
            <dl class="main-menu">
                <dt>军民融合</dt>
                <dd style="display: <?php echo isset($_GET['jm']) ? 'block' : 'none' ?>"><a
                        href="index.php?jm=1&jm_cate=1">管理分类</a></dd>
                <dd style="display: <?php echo isset($_GET['jm']) ? 'block' : 'none' ?>"><a
                        href="index.php?jm=1&jm_list=1">文章列表</a></dd>
                <dd style="display: <?php echo isset($_GET['jm']) ? 'block' : 'none' ?>"><a
                        href="index.php?jm=1&jm_create=1">新建文章</a></dd>
            </dl>
        <?php endif ?>
        <?php if(isset($_SESSION['pms']['std'])):?>
            <li>
                <dl class="main-menu">
                    <dt>学习平台管理</dt>
                    <dd style="display: <?php echo isset($_GET['std']) ? 'block' : 'none' ?>"><a
                            href="index.php?std=1&questionList=1">题目列表</a></dd>
                    <dd style="display: <?php echo isset($_GET['std']) ? 'block' : 'none' ?>"><a
                            href="index.php?std=1&createQuestion=1">新建</a></dd>
                    <dd style="display: <?php echo isset($_GET['std']) ? 'block' : 'none' ?>"><a
                            href="index.php?std=1&userScore=2">用户成绩</a></dd>
                </dl>
            </li>
        <?php endif ?>

        <?php if(isset($_SESSION['pms']['bbs'])):?>
            <li>
                <dl class="main-menu">
                    <dt>互动社区管理</dt>
                    <dd style="display: <?php echo isset($_GET['bbs']) ? 'block' : 'none' ?>"><a
                            href="index.php?bbs=1&bbslist=1">帖子列表</a></dd>
                    <dd style="display: <?php echo isset($_GET['bbs']) ? 'block' : 'none' ?>"><a
                            href="index.php?bbs=1&createTopic=2">发帖</a></dd>
                </dl>
            </li>
        <?php endif ?>

        <?php if (isset($_SESSION['pms']['operator'])): ?>
        <li>
            <dl class="main-menu">
                <dt>管理员</dt>

                    <dd><a href="index.php?wechatConfig=1">微信公众号</a></dd>
                    <dd><a href="index.php?operator=1">管理员信息</a></dd>
            </dl>
        </li>
        <?php endif ?>
        <li>
            <p class="btm_infor">© 谷多网络 版权所有</p>
        </li>
    </ul>
</aside>
<script>
    $('dt').click(function () {
        $(this).nextAll('dd').slideToggle('fast');
    });
</script>
<section class="rt_wrap content mCustomScrollbar">
    <div cla="rt_content">


