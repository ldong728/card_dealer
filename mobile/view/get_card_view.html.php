<head>
    <?php include 'templates/header.php'?>
<!--    <link rel="stylesheet" href="stylesheet/order.css"/>-->
</head>

<body>

</body>
<?php include_once 'templates/jssdkIncluder.php' ?>

<script>
    var cardReadyRequest=[
        <?php foreach($cardInfList as $v):?>
        {
            cardId: '<?php echo $v['id']?>',
            cardExt: '<?php echo $v['ext']?>'
        },
        <?php endforeach?>
    ];
    var cardinf={module:'get_card',cardList:{},getedCount:{}};
    $.each(cardReadyRequest,function(k,v){
        var value=eval('('+ v.cardExt+')');
        var key=value.nonce_str.slice(14);
        var orderid=value.nonce_str.slice(0,14);
        cardinf.cardList[key]={order_id:value.nonce_str.slice(0,14),card_id: v.cardId,card_code:''};
        cardinf.getedCount[orderid]=null==cardinf.getedCount[orderid]? 1:cardinf.getedCount[orderid]+1;
    });
    wx.ready(function(){
            wx.addCard({
                cardList: cardReadyRequest, // 需要添加的卡券列表
                success: function (res) {
                    var cardList = res.cardList; // 添加的卡券列表信息
                    $.each(cardList,function(k,v){
                        var data=eval('('+ v.cardExt+')');
                        var key=data.nonce_str.slice(14);
                        cardinf.cardList[key].card_code= v.code;
                    });
                    $.post('ajax.php',cardinf,function(data){
                        var re=eval('('+data+')');
                        alert(re.errcode)
                    });
                },
                fail:function (res) {
//                    alert(data.errMsg);
                },
                complete:function(res){
//                    var cardList = res.cardList; // 添加的卡券列表信息
//                    $.each(cardList,function(k,v){
//                        var data=eval('('+ v.cardExt+')');
//                        var key=data.nonce_str.slice(14);
//                        cardinf.cardList[key].card_code= v.code;
//                    });
//                    $.post('ajax.php',cardinf,function(data){
//
//                    });
                }
            });
    }

    );

</script>