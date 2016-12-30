<head>
    <?php include 'templates/header.php'?>
    <link rel="stylesheet" href="stylesheet/mall.css?t=<?php echo rand(1000,9999)?>"/>
    <?php include 'templates/jssdkIncluder.php' ?>
</head>

<body>
<div class="header">
    <div class="h-wrap">
        <div class="h-l">
            <a href="?module=card_mall">
                <input type="submit" value="微信卡券" class="sub1 ">
            </a>
        </div>
        <div class="h-r">
            <a href="?module=my_card">
                <input type="submit" value="我的卡券" class="sub2 s-cur">
            </a>
        </div>
    </div>
</div>
<div class="main">
    <?php foreach($cardList as $row):?>
        <div class="main-a">
            <a>
                <div class="m-l float" style="background-color: <?php echo $row['color']?>;border-color: <?php echo $row['color']?>">
                    <div class="m-l-t"><?php echo $row['card_title'] ?></div>
                    <div class="m-l-m">状态 : 可使用</div>
                    <div class="m-l-b">有效期至<?php echo $row['end_time']?></div>
                </div>
            </a>
            <a class="use_card" id="<?php echo $row['card_id']?>" data-code="<?php echo $row['card_code']?>">
                <div class="m-r float" style="border-color: <?php echo $row['color']?>;color: <?php echo $row['color']?>">使&nbsp;用</div>
            </a>
        </div>
    <?php endforeach ?>
</div>
<script>
    wx.ready(function () {
        $('.use_card').click(function () {
            var cardId = $(this).attr('id');
            var card_code=$(this).data('code');
            wx.openCard({cardList:[{cardId:cardId,code:card_code}]});
//            $.post('ajax.php', {module: 'choose_card', card_id: cardId}, function (data) {
//                var re = backHandle(data);
//                re.success = function (back) {
//                    var list=eval('('+back.cardList+')');
//                    var value=[];
//                    $.each(list,function(k,v){
//                        value.push({id: v.card_id,code: v.encrypt_code})
//                    });
//                    $.post('ajax.php',{module:'encryp_code',code_list:value},function(data){
//                        var re=backHandle(data);
//                        if(re)wx.openCard({cardList:re});
//                    });
//                };
//                wx.chooseCard(re);
//
//            });
        });
    });

</script>

</body>