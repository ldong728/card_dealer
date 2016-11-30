<head>
    <?php include 'templates/header.php'?>
    <link rel="stylesheet" href="stylesheet/order.css"/>
</head>
<body>
<div class="wrap">

    <div class="orderComfirm">
        <a class="address"href="controller.php?editAddress=1&from=<?php echo $from?>">
            <i class="iCar"></i>
            <div class="adInfo">
               <p><?php htmlout($addr['province'].'  '.$addr['city'].'   '.$addr['area'])?></p>
                <p><?php htmlout($addr['address'])?></p>
                <p><?php htmlout($addr['name'])?><span class="recPhone"><?php htmlout($addr['phone'])?></span></p>
            </div>
        </a>
        <ul class="odList">
            <?php foreach($goodsList as $row):?>
                <li>
                    <div class="orderBox">
                        <dl>
                            <dd>
                                <div class="op_detail">
                                    <h3>
                                        <?php echo $row['name']?>
                                    </h3>
                                    <p>规格：<?php echo $row['category']?></p>
                                    <p>数量：<span class="cl_red"><?php echo $row['number']?></span></p>
                                    <p>单价：<span class="cl_red">￥<?php echo $row['price']?></span></p>
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <div class="partBox">
                    <?php foreach($row['parts'] as $prow):?>
                        <div class="part-block">
                        <p><?php echo $prow['part_name']?>:<?php echo $prow['part_produce_id']?></p>
                            <p>数量：<span class="red"><?php echo $prow['part_number']?></span></p>
                            <p>单价：<span class="red">￥<?php echo $prow['part_sale']?></span></p>
                        </div>
                        <div class="vslash"></div>
                    <?php endforeach;?>
                    </div>
                </li>
            <?php endforeach?>
        </ul>
        <div class="orderOther"style="margin: 10px auto 0 auto">
            <div class="remark">
                <div class="remark-title"><h3>用户留言：</h3></div>
                <textarea rows="2"class="remark_field"></textarea>
            </div>
        </div>

        <div class="orderOther card-button-container"style="margin: 10px auto 0 auto">
            <div class="card-button">
                优惠券
            </div>
            <div class="card-content">
            </div>
        </div>
        <div class="ordertotal">
            <span class="realPay">实付款（含运费）：</span>
            <span class="payTotal">
                <span class="cl_red"id="totolfee">￥<?php echo number_format($totalPrice,2,'.','')?></span>
            </span>
        </div>

        <a class="orderSettle" id="orderConfirm"href="#">订单确认</a>
    </div>
    <div class="toast"></div>
</div>
<?php include_once 'templates/jssdkIncluder.php'?>
</body>

<?php
include_once '../wechat/interfaceHandler.php';
include_once '../wechat/cardsdk.php';
$card=new cardsdk();
$sign=$card->getSignPackage("DISCOUNT CASH");
?>
<script>
    var from ='<?php echo $from?>';
    var addrId = <?php echo $addr['id']?>;
    var totalPrice =<?php echo $totalPrice ?>;
</script>
<script>
    var cardId=null;
    var cardCode='none';
    var save=0;
        wx.ready(function(){
            $('.card-button').click(function(){
                wx.chooseCard({
//                shopId: '', // 门店Id
                    cardType: '<?php echo $sign['cardType']?>', // 卡券类型
//                cardId: '', // 卡券Id
                    timestamp: <?php echo $sign['timestamp']?>, // 卡券签名时间戳
                    nonceStr: '<?php echo $sign['nonceStr']?>', // 卡券签名随机串
                    signType: 'SHA1', // 签名方式，默认'SHA1'
                    cardSign: '<?php echo $sign['cardSign']?>', // 卡券签名
                    success: function (res) {
                        var cardList= res.cardList; // 用户选中的卡券列表信息
                        var cardInf=eval('('+cardList+')');
                        $.post('ajax.php?chooseCard=1',{card_id:cardInf[0].card_id,encrypt_code:cardInf[0].encrypt_code,totalPrice:totalPrice},function(data){
                            data=eval('('+data+')');
                            $('.card_detail').empty();
                            if(data.save<0){
                                showToast('此券无法使用')
                            }else{
                                $('.card-detail').append('节省￥'+data.save);
                                showToast('已为您节省'+data.save+'元')
                                $('#totolfee').text('￥'+(totalPrice-data.save));
                                cardId=data.cardId;
                                cardCode=data.cardCode;
                            }
                        });
                    }
                });
            });
        })

</script>

<script>
    $('.ordersettle').click(function(){
//        alert('controller.php?orderConfirm=1&addrId='+addrId+'&from='+from+'&card='+cardCode);
        $.post('ajax.php',{userRemark:1,remark:$('.remark_field').val()},function(data){
            window.location.href='controller.php?orderConfirm=1&addrId='+addrId+'&from='+from+'&card='+cardCode;
        })
    });
</script>
