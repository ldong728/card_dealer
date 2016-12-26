<div class="main">
    <table class="table">
        <tbody>
        <tr>
            <td width="100">当前版本：</td>
            <td width="150">GOODUO PHP V1.0</td>
            <td width="100">最新版本：</td>
            <td width="150">GOODUO PHP V1.0</td>
        </tr>
        <tr>
            <td>网站目录：</td>
            <td><?php echo DOMAIN?></td>
            <td>服务器时间：</td>
            <td><?php echo timeUnixToMysql(time())?></td>
        </tr>
        <tr>
            <td>用户IP：</td>
            <td><?php echo $_SERVER['REMOTE_ADDR']?></td>
            <td>用户浏览器：</td>
            <td><?php echo $_SERVER['HTTP_USER_AGENT'] ?></td>
        </tr>
        <tr>
            <td>服务器名：</td>
            <td><?php echo $_SERVER['HTTP_HOST']?></td>
            <td>服务器IP：</td>
            <td><?php echo $_SERVER['SERVER_ADDR']? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']?></td>
        </tr>

        </tbody>
    </table>
</div>