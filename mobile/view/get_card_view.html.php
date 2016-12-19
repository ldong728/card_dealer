<head>
    <?php include 'templates/header.php'?>
<!--    <link rel="stylesheet" href="stylesheet/order.css"/>-->
</head>

<body>

</body>
<?php include_once 'templates/jssdkIncluder.php' ?>

<script>
    wx.ready(function(){
            wx.addCard({
                cardList: [
                    <?php foreach($cardInfList as $v):?>
                    {
                        cardId: '<?php echo $v['id']?>',
                        cardExt: '<?php echo $v['ext']?>'
                    },
                    <?php endforeach?>

                ], // 需要添加的卡券列表
                success: function (res) {
                    var cardList = res.cardList; // 添加的卡券列表信息
//                    alert(cardList);
                },
                fail:function (res) {
//                    alert(data.errMsg);
                },
                complete:function(res){
//                    alert("complete");
                }
            });
    }

    );

</script>