<!DOCTYPE html>
<html lang="cn">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title><?php echo $title ?></title>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet/style.css?v=<?php echo rand(1000, 9999) ?>">
<!--    <link rel="stylesheet" type="text/css" href="stylesheet/style2.css?v=--><?php //echo rand(1000, 9999) ?><!--">-->
    <link rel="stylesheet" href="stylesheet/admin.css?v=<?php echo rand(1000,9999)?>">
<!--    <script src="js/html5.js"></script>-->
    <style>
        dt {
            cursor: pointer;
        }
    </style>

</head>
<script>
    var pms="<?php echo $_GET['menu']?>";
</script>



<body>
<div class="toast"></div>
<div class="loading"></div>
<div class="header">
    <div class="header">
        <div class="logo">Gooduo.net</div>
        <div class="link">
            <a href="index.php?logout=1" class="logout"></a>
            <a href="#" target="_blank" class="home"></a>
            <div>
                <h2>gooduo</h2>
                <h3>后台管理员</h3>
            </div>
            <a href="?/basic/mod-admin_list/index.html" target="_blank" class="gooduo"></a>
        </div>
    </div>
</div>
<div id="main">
    <div id="left" style="height: 100%">
        <ul>
            <li class="sys_lang">
                <select onchange="change_lang(this.value)">
                    <option selected="selected" value="admin.php?/basic/mod-basic_info/">中文</option>
                </select>
            </li>
            <?php foreach($_SESSION['pms'] as $key=>$row):?>
                <li>
                    <a class="main_menu"><?php echo $row['name']?></a>
                    <ul class="sub_menu" style="display: <?php echo isset($_GET['menu'])&&$_GET['menu']==$row['key'] ? 'block' : 'none' ?>">
                        <?php foreach($row['sub'] as $subrow):?>
                            <li >
                                <a href="index.php?menu=<?php echo $key?>&sub=<?php echo $subrow['key']?>"><?php echo $subrow['name']?></a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <script>
        $('.main_menu').click(function () {
            $(this).next('ul').slideToggle('fast');

        });
    </script>
    <div id="right" style="height: 100%">
        <div class="m_title">
            <div class="m_local f_l"><a href="?">首页</a><?php echo isset($_GET['menu'])? '&gt;<a href="?menu='.$_GET['menu'].'">'.$_SESSION['pms'][$_GET['menu']]['name'].'</a>':''?>
            <?php echo isset($_GET['sub'])? '&gt;<a href="?menu='.$_GET['menu'].'&sub='.$_GET['sub'].'">'.$_SESSION['pms'][$_GET['menu']]['sub'][$_GET['sub']]['name'].'</a>' : ''?>
               </div>
            <div class="m_time f_r">服务器时间：<?php echo timeUnixToMysql(time())?></div>
            </div>






