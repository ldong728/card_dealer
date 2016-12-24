<!doctype html>
<html>
<head>
    <?php include 'templates/header.php'?>
    <link rel="stylesheet" href="stylesheet/user.css"/>
    <script src="../js/lazyload.js"></script>
</head>
<body>
<div class="age_login">
    <form id="form_user_login" method="post" action="?module=login&log_mode=<?php echo $logMode?>">
        <input name="cmd" type="hidden" value="user_login"/>
        <input class="txt" name="userphone" type="tel" maxlength="11" placeholder="手机号码" />
        <input class="txt" name="password" type="password" placeholder="密码" />
        <input class="bt" type="submit" value="登 &nbsp; 录" />
    </form>
</div>
</body>
</html>