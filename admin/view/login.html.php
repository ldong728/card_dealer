<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>-->
<!--    <title>登入</title>-->
<!--    <script type="text/javascript" src="../js/jquery.js"></script>-->
<!--    <link rel="stylesheet" type="text/css" href="stylesheet/style.css">-->
<!--</head>-->
<!---->
<!--<body>-->
<!--<form action="index.php?login=1"method="post">-->
<!--    <div>-->
<!--        <label>用户名:</label>-->
<!--        <input type="text" name="adminName"placeholder="输入用户名">-->
<!--        </div>-->
<!--    <div>-->
<!--        <label>密码：</label>-->
<!--        <input type="password"name="password"placeholder="输入密码">-->
<!---->
<!--    </div>-->
<!--    <input type="submit"value="确定">-->
<!---->
<!---->
<!--</form>-->
<!--</body>-->
<!--</html>-->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>后台登录</title>
    <meta name="author" content="DeathGhost"/>
    <link rel="stylesheet" type="text/css" href="stylesheet/style2.css"/>
    <style>
        body {
            height: 100%;
            background: #2673BF;
            overflow: hidden;
        }

        canvas {
            z-index: -1;
            position: absolute;
        }
    </style>
    <script src="../js/jquery.js"></script>
<!--    <script src="js/verificationNumbers.js"></script>-->
<!--    <script src="js/Particleground.js"></script>-->
<!--    <script>-->
<!--        $(document).ready(function () {-->
<!--            //粒子背景特效-->
<!--            $('body').particleground({-->
<!--                dotColor: '#2b82d9',-->
<!--                lineColor: '#2b82d9'-->
<!--            });-->
<!--            //验证码-->
<!--//            createCode();-->
<!---->
<!--        });-->
<!--    </script>-->
</head>
<body>

<a href="http://www.gooduo.net"><div class="logo"></div></a>
<dl class="admin_login">
    <dt>
        <strong>后台管理系统</strong>
        <em>Management System</em>
    </dt>
    <form action="index.php?login=1" method="post">
        <dd class="user_icon">
            <input type="text" placeholder="账号" class="login_txtbx" name="adminName"/>
        </dd>
        <dd class="pwd_icon">
            <input type="password" placeholder="密码" class="login_txtbx" name="password"/>
        </dd>
        <!--    <dd class="val_icon">-->
        <!--        <div class="checkcode">-->
        <!--            <input type="text" id="J_codetext" placeholder="验证码" maxlength="4" class="login_txtbx">-->
        <!--            <canvas class="J_codeimg" id="myCanvas" onclick="createCode()">对不起，您的浏览器不支持canvas，请下载最新版浏览器!</canvas>-->
        <!--        </div>-->
        <!--        <input type="button" value="验证码核验" class="ver_btn" onClick="validate();">-->
        <!--    </dd>-->
        <dd>
            <input type="button" value="立即登陆" class="submit_btn"onclick="submit()"/>
        </dd>
    </form>
    <dd>
        <p>© 2015-2016 gooduo.net 版权所有</p>
    </dd>
</dl>
</body>
</html>
