<head>
    <?php include 'templates/header.php'?>
    <!--    <link rel="stylesheet" href="stylesheet/order.css"/>-->
    <link rel="stylesheet" href="stylesheet/ticket.css"/>
    <?php include_once 'templates/jssdkIncluder.php' ?>
</head>

<body>
<div class="header">卡券信息确认</div>
<div class="main-b">
    <a id="start_scan">
        <div class="img"></div>
    </a>
    <input type="number" class="search" placeholder="输入卡券号" id="code">
    <input type="button" class="button" id="consume" value="核&nbsp;销">
</div>


</body>

<script>
    $('#consume').click(function(){
        var code=$('#code').val();
        $.post('ajax.php',{action:'consume',data:code},function(re){
            var value=backHandle(re);
            if(value){
                alert('ok');
            }else{
                if(confirm('错误：'+value.errmsg+',是否继续')){
                    wx.scanQRCode(
                        myScan
                    )
                }else{
//                        wx.closeWindow();
                }
            }
        });
    });
    $('#start_scan').click(function(){
        wx.scanQRCode(myScan);
    });


</script>
<script>
    var myScan={
        needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
        scanType: ["barCode"], // 可以指定扫二维码还是一维码，默认二者都有
        success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            var code=result.slice(9);
            $('#code').val(code);
        },
        cancel: function(res){
//            $.post('ajax.php',{action:'clear_session'},function(re){
//                wx.closeWindow();
//            });
        }

    }
    wx.ready(function(){
            wx.scanQRCode(
                myScan
            );
        }
    );
</script>