<head>
    <?php include 'templates/header.php'?>
</head>
<body>
<?php include 'templates/jssdkIncluder.php'?>
    <div class="wrap">
        <div class="warning">
            <?php echo $errmsg?>
        </div>
        <div>
            <button onclick="wx.closeWindow()">关闭</button>
        </div>
    </div>
</body>