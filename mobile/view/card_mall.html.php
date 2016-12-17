<head>
    <?php include 'templates/header.php'?>
        <link rel="stylesheet" href="stylesheet/table_temp.css?t=<?php echo rand(1000,9999)?>"/>
</head>

<body>
<body>
<div class="wrap">
    <?php foreach($cardList as $row):?>
        <?php mylog(getArrayInf($row))?>
    <div class="table bord_size">
        <div class="table_1 left_space"><?php echo $row['card_title'] ?></div>
        <div class="table_2 left_space">零售价:<?php echo $row['price']?>元</div>
        <div class="table_3 left_space">有效日期至2017年12月31日</div>
    </div>
    <div class="button_box bord_size" id="<?php echo $row['card_id']?>">购买</div>
    <?php endforeach ?>
</div>
<script>
    $('.button_box').click(function(){
        var card_id=this.id;
        window.location.href='?module=card_order&card_id='+card_id;

//        alert(card_id);
    });
</script>
</body>