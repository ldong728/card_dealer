<head>
    <?php include 'templates/header.php'?>
        <link rel="stylesheet" href="stylesheet/mall.css?t=<?php echo rand(1000,9999)?>"/>
</head>

<body>
<div class="header">
    <div class="h-wrap">
        <div class="h-l">
            <a href="index.html">
                <input type="submit" value="微信卡券" class="sub1 s-cur">
            </a>
        </div>
        <div class="h-r">
            <a href="mycard.html">
                <input type="submit" value="我的卡券" class="sub2">
            </a>
        </div>
    </div>
</div>
<div class="main">
    <?php foreach($cardList as $row):?>
    <div class="main-a">
        <a href="?module=card_order&card_id=<?php echo $row['card_id']?>">
            <div class="m-l float" style="background-color: <?php echo $row['color']?>;border-color: <?php echo $row['color']?>">
                <div class="m-l-t"><?php echo $row['card_title'] ?></div>
                <div class="m-l-m">零售价 : <?php echo $row['price']?>元</div>
                <div class="m-l-b">有效期至<?php echo $row['end_time']?></div>
            </div>
        </a>
        <a href="?module=card_order&card_id=<?php echo $row['card_id']?>">
            <div class="m-r float" style="border-color: <?php echo $row['color']?>">购&nbsp买</div>
        </a>
    </div>
    <?php endforeach ?>
</div>

</body>