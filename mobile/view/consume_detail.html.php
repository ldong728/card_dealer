<head>
    <?php include 'templates/header.php'?>
<!--    <link rel="stylesheet" href="stylesheet/address.css"/>-->
</head>
<body>
    <div class="wrap">
        <div class="card_detail">

        </div>

        <div class="address-block" id="<?php echo $address['id']?>">
        <a href="controller.php?action=consume_online&address_change"><?php echo $address['province'].$address['name']?></a>
        </div>


        <div class="confirm">
            <button class="confirm_btn">确认发货</button>
        </div>


    </div>

</body>
<script>
    $('.confirm_btn').click(function(){
        location.href='controller.php?action=consume_confirm&address_id='+$('.address-block').get(0).id;
    });

</script>