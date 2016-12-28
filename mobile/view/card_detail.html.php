<!DOCTYPE html>
<html>
<head lang="en">
    <?php include 'templates/header.php'?>
    <?php include 'templates/jssdkIncluder.php'?>
<!--    <link rel="stylesheet" href="stylesheet/table_temp.css?t=--><?php //echo rand(1000,9999)?><!--"/>-->
    <title></title>
</head>
<body>
<div class="wrap">
    <div class="card_inf">

    </div>
    <div class="card_menu_area">
        <div class="menu">
            <input class="card_order_number" type="tel" placeholder="请输入数量"/>
            <input class="card_order_confirm" type="button" value=" 购买"/>
        </div>
    </div>

</div>
<script>
    var cardId='<?php echo $cardId ?>';
    $('.card_order_confirm').click(function(){
        var number=$('.card_order_number').val();
        $.post('ajax.php',{module:'pay_pre',card_id:cardId,number:number},function(data){
            console.log(data);
            var back=eval('('+data+')');
            if(0== back.errcode){
                wx.chooseWXPay(
                    {
                        timestamp: back.data.timestamp,//这里是timestamp 键中的字母s要小写，妈的
                        nonceStr: back.data.nonceStr,
                        package: back.data.package,
                        signType: back.data.signType,
                        paySign: '<?php echo $preSign['paySign']?>',
                        success: function (res) {
                            if('get_brand_wcpay_request:ok'==res.err_msg){
//                    alert('pay succes')
                                $('.stu').empty();
                                $('.stu').append('支付成功');
                                $('.orderSettle').show();
                            }else{
//                    alert('false:'+res.err_msg);
                            }
                            window.location.href='controller.php?customerInf=1';
                            // 支付成功后的回调函数
                        }
                    }
                )

            }else{
                alert(data.err_code);
            }
        });
//        alert(cardId+': '+number);
    });
</script>

</body>
</html>