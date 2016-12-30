<head>
    <?php include 'templates/header.php'?>
    <link rel="stylesheet" href="stylesheet/ticket.css?t=<?php echo rand(1000,9999)?>"/>
</head>
<body>
<div class="header">发货确认</div>
<div class="main">
    <div class="inf_wrap">
<!--        <div class="inf inf_title"></div><div class="inf_content"></div>-->
    </div>
    <p><strong>卡券名：</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $cardDetail['card_title']?></p>
    <br>
    <p><strong>卡券信息：</strong>&nbsp;&nbsp;&nbsp;&nbsp;  有效日期至<?php echo $cardDetail['end_time']?></p>
    <br>
    <p><strong>收货地址：</strong>&nbsp;&nbsp;</p><div class="address-block">
        <a href="controller.php?action=consume_online&address_change">
            <?php echo $address['province'].$address['city'].$address['area']?></br>
            <?php echo $address['address']?></br>
            <?php echo $address['name']?></br>
            <?php echo $address['phone']?></br>
        </a>
    </div>
    <br>
    <input type="button" class="button confirm_btn" value="确认发货">
</div>
</body>


<script>
    $('.confirm_btn').click(function(){
        location.href='controller.php?action=consume_confirm&address_id='+$('.address-block').get(0).id;
    });

</script>