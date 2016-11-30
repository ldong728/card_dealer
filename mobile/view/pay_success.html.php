<head>
    <?php include 'templates/header.php'?>
    <link rel="stylesheet" href="stylesheet/order.css"/>
</head>
<body>
<div class="wrap">
    </header>
    <div class="orderComfirm">
        <div>
            <h1>支付成功</h1>
        </div>
        <div>
            <h5>订单号：<?php echo $orderId?></h5>
            <h6>订单状态：已支付</h6>
        </div>
        <a class="orderSettle" id="wxpay"href="#">返回</a>
    </div>
    <?php include_once 'templates/foot.php'?>
</div>
</body>