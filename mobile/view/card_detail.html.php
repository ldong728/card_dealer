<!DOCTYPE html>
<html>
<head lang="en">
    <?php include 'templates/header.php' ?>
    <?php include 'templates/jssdkIncluder.php' ?>
    <link rel="stylesheet" href="stylesheet/product.css?t=<?php echo rand(1000, 9999) ?>"/>
    <title></title>
</head>
<body>
<div class="header" style="background-color: <?php echo $cardInf['color']?>">
    <p class="name"><?php echo $cardInf['card_title']?></p>

    <p>售价：￥<?php echo $cardInf['price']?></p>

    <p>有效期至：<?php echo $cardInf['end_time']?></p>
</div>
<div class="main">
</div>
<div class="footer">
    <div class="f-left float">
        <p>数量</p>

        <div class="f-n">
            <input type="button" id="minus" onClick="minus1()" value="-">
            <input type="tel" id="number" maxlength="2" value="1">
            <input type="button" id="add" onClick="add1()" value="+">
        </div>
    </div>
    <div class="f-right float">
        <input class="card_order_confirm" type="submit" value="购买">
    </div>
</div>

<script>
    var minus=document.getElementById("minus");
    var add=document.getElementById("add");
    var number=document.getElementById("number");
    function minus1(){
        if(number.value>1)number.value--;
    }
    function add1(){
        number.value++;
    }
</script>
<script>
    var cardId = '<?php echo $cardId ?>';
    $('.card_order_confirm').click(function () {
        var number = $('#number').val();
        $(this).attr('disabled',true);
        $.post('ajax.php', {module: 'pay_pre', card_id: cardId, number: number}, function (data) {
            console.log(data);
            var back = eval('(' + data + ')');
            if (0 == back.errcode) {
                wx.chooseWXPay(
                    {
                        timestamp: back.data.timestamp,//这里是timestamp 键中的字母s要小写，妈的
                        nonceStr: back.data.nonceStr,
                        package: back.data.package,
                        signType: back.data.signType,
                        paySign: '<?php echo $preSign['paySign']?>',
                        success: function (res) {
                            if ('get_brand_wcpay_request:ok' == res.err_msg) {
//                    alert('pay succes')
                                $('.stu').empty();
                                $('.stu').append('支付成功');
                                $('.orderSettle').show();
                            } else {
//                    alert('false:'+res.err_msg);
                            }
                            window.location.href = 'controller.php?customerInf=1';
                            // 支付成功后的回调函数
                        }
                    }
                )

            } else {
                alert(back.errmsg);
                location.href = '?module=card_bought_list'
            }
        });
//        alert(cardId+': '+number);
    });
</script>

</body>
</html>