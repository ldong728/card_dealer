<head>
    <?php include 'templates/header.php' ?>
    <link rel="stylesheet" href="stylesheet/order-detail.css"/>
</head>
<body>
<div class="wrap">
    <div class="ordreDetail">
        <div class="orderTransp">
            <div class="transp_hd"></div>
            <div class="address">
                <p class="add_tit">收货地址</p>

                <p id="address"><?php htmlout($order_inf['province'])?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php htmlout($order_inf['city']) ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php htmlout($order_inf['area']) ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php htmlout($order_inf['address']) ?></p>

                <p id="nameAndTel"><?php echo $order_inf['name'] ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order_inf['phone'] ?></p>
            </div>
            <div class="transport transp_info" style="overflow: hidden; display: none;">
                <p class="tran_tit">物流信息读取中..</p>
            </div>
            <div class="show_wuliu"></div>
            <div class="hide_wuliu"></div>
        </div>
        <div class="orderstate">
            <div>
                <span class="cl_sta">订单状态:</span>
                <a class="cl_red" style="width: 20%; background: none;"><?php echo getOrderStu($order_inf['stu']) ?></a>
                <span class="drawback"></span>
            </div>
            <div>订单编号：<span id="orderno"><?php echo $order_inf['id'] ?></span></div>
        </div>
        <dl class="orderList">
            <?php $total = 0 ?>
            <?php foreach ($ordeDetailQuery as $row): ?>
                <div id="orderLineList">
                    <dd>
                        <div class="pd_detail">
                            <a href="#"
                               class="pd_name">
                                <?php echo $row['name'] ?>
                            </a>

                            <p class="pd_guige">规格: <span class="cl_grey"><?php echo $row['category'] ?></span></p>

                            <p class="pd_count">数量: <span class="cl_red"><?php echo $row['number'] ?></span></p>

                            <p class="pd_price">总价: <span class="cl_red">￥<?php echo $row['total'] ?></span></p>
                        </div>
                    </dd>
                    <dd class="borderLine">

                    </dd>
                </div>
                <?php $total = $total + $row['total'] ?>
            <?php endforeach ?>
        </dl>
        <div class="orderInfo">
            <div>建立时间：<span id="orderTime"><?php echo $order_inf['order_time'] ?></span></div>
            <div>支付方式：<span id="payType">在线支付</span></div>
            <div class="express noDisplay">配送方式：<span
                    id="deliveryCompany"><?php echo $order_inf['express_name'] ?></span></div>
            <div class="express noDisplay">物流单号：<span id="deliveryCompany"><?php echo $order_inf['express_order'] ?>
            </div>
            <div class="express noDisplay"><a href="http://m.kuaidi100.com/index_all.html?type=<?php echo $order_inf['express_id']?>&postid=<?php echo $inf['express_order']?>">点击查询物流</a></div>
        </div>
        <div class="order_ft">
                <span class="order_amount">
                    共计：

                <span class="cl_red">
                    ￥<?php echo number_format($order_inf['total_fee'],2,'.','') ?>
                </span>
                <span class="transp">
                    含运费￥<?php echo $order_inf['express_price'] ?>
                </span></span>
        </div>
        <div class="remark">
            <span>买家留言：</span><span><?php htmlout($order_inf['remark'])?></span>
        </div>
        <div class="order_btn noDisplay">
            <a class="btn_orange payOrder  payButton" href="#" id="pay_now">
                立即付款
            </a>
            <a class="btn_white cansel_btn payButton" id="cancel_oder">
                取消订单
            </a>
        </div>
        <div class="review noDisplay">
            <a class="btn_orange review_button" href="#" id="pay_now">
                评价
            </a>
        </div>
        <div class="blank"></div>
    </div>
    <div class="toast"></div>
    <?php include_once 'templates/foot.php'?>
</div>
<script>
    var orderId = '<?php echo $order_inf['id']?>';
    var orderstu =<?php echo $order_inf['stu']?>;
    var totalFee=<?php echo $order_inf['total_fee'] ?>;
        $('.noDisplay').css('display','none');
        switch (orderstu){
            case 0:{
                $('.order_btn').css('display','block');
                break;
            }
            case 2:{
                $('.express').css('display','block');
                $('.review').css('display','block');
                break;
            }
            case 3:{
                $('.express').css('display','block');
                break;
            }
        }
    $('#cancel_oder').click(function(){
        $.post('ajax.php',{cancel_order:1,order_id:orderId},function(data){
            if(data==0){
                window.location.href = 'controller.php?customerInf=1';
            }else{
                alert('此订单暂时无法删除，请联系客服处理')
            }
        });
    });
    $('#pay_now').click(function () {
        window.location.href = 'controller.php?pay_order=1&order_id=' + orderId + '&order_stu=' + orderstu + '&total_fee='+totalFee;
    });
    $('.review_button').click(function(){
        window.location.href='controller.php?review=1&order_id='+orderId;
    })
</script>
</body>