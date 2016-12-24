<head>
    <?php include 'templates/header.php'?>
    <!--    <link rel="stylesheet" href="stylesheet/order.css"/>-->
    <link rel="stylesheet" href="stylesheet/user.css"/>
    <?php include_once 'templates/jssdkIncluder.php' ?>
</head>

<body class="age_bg">
<?php //include 'templates/nav.php'?>
<div class="sub_age">
    <ul class="clearfix recorder_list">
    </ul>

</div>


</body>


<script>

    var myScan={
        needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
        scanType: ["barCode"], // 可以指定扫二维码还是一维码，默认二者都有
        success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            $.post('ajax.php',{action:'consume',data:result.slice(9)},function(re){
                var value=backHandle(re);
                if(value){
                    alert('ok');
                }else{
                    if(confirm('错误：'+value.errmsg+',是否继续')){
                        wx.scanQRCode(
                            myScan
                        )
                    }else{
                        wx.closeWindow();
                    }
                }
            });
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