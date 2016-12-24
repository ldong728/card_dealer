<?php
include_once '../wechat/interfaceHandler.php';
include_once '../wechat/jssdk.php';

$jssdk = new JSSDK(WEIXIN_ID);

$signPackage = $jssdk->GetSignPackage();

?>

    <script src="https://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script>
    wx.config({
        debug:false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'hideMenuItems',
            'chooseImage',
            'uploadImage',
//            'scanQRCode',
//            'getLocation',
//            'openLocation',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'chooseWXPay',
            'chooseCard',
            'addCard',
            'openCard',
            'scanQRCode',
            'closeWindow'
        ]
    });
//    wx.ready(function(){

//        alert()
//        wx.hideOptionMenu();
//        wx.scanQRCode({
//            needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
//            scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
//            success: function (res) {
//                var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
//            }
//        });
//    })
</script>
