<?php global $getStr,$page,$num,$orderIndex,$order,$list,$balence;
?>

<div id="core" style="height: 618px;">
    <div class="block">
        <div class="head" style="width: 98%;"><span>产品列表</span></div>
        <div class="main">
            <table class="table sheet">
                <tbody><tr class="h">
                    <td></td>
                    <td>订单号</td>
                    <td>卡券名</td>
                    <td><a href="?<?php echo $getStr.'&index=price'?><?php echo 'price'==$orderIndex&&'asc'==$order ? '&order=desc':'' ?>">金额</a></td>
                    <td><a href="?<?php echo $getStr.'&index=consume_time'?><?php echo 'consume_time'==$orderIndex&&'asc'==$order ? '&order=desc':'' ?>">核销时间</td>
                    <td>操作</td>
                </tr>
                <?php foreach($list as $row):?>
                <tr>
                    <td><input type="checkbox" id="<?php echo $row['card_code']?>" </td>
                    <td><?php echo $row['card_order_id']?></td>
                    <td><?php echo $row['card_title']?></td>
                    <td><?php echo $row['price']?></td>
                    <td><?php echo $row['consume_time']?></td>
                    <td><button class="button settle" id="btn<?php echo $row['card_code']?>">结算</button></td>
                </tr>
                <?php endforeach ?>
                <tr>
                    <td colspan="7">
                        <div class="page_link">
                            <div class="in">
                                <span>共1页</span>
                                <span>第1页</span>
                                <form id="form_jump" action="" method="get">
                                    <input type="hidden" name="url" value="?/goods/mod-sheet/page-1/index.html">
                                    <input class="text" type="text" style="width:30px" name="page" value="1">
                                    <input class="button" type="button" onclick="page_jump('1')" value="跳转">
                                </form>
                            </div>
                        </div>
                        <!-- GOODUO -->
                    </td>
                </tr>
                </tbody></table>
        </div>
    </div>
    <div class="space"></div>
    <div class="block">
        <div class="head" style="width: 98%;"><span>账户信息</span></div>
        <div class="main">

                <table class="table">
                    <tbody><tr>
                        <td align="right" width="150px">
                            当前账户：
                        </td>
                        <td>
                            ￥<?php echo $balence?>
                        </td>
                    </tr>
                    </tbody></table>
        </div>
    </div>
    <script language="javascript">
        var lang_if_del_goods = "您确定要删除该产品吗？";
    </script>

    <script language="javascript">
        $('.settle').click(function(){
            var card_code=$(this).attr('id').slice(3);
            settleCard([card_code],function(data){
                var re=backHandle(data);
                if(re) {
                    location.href='?<?php echo $getStr?>';
                }else{
                    alert('结算失败，请重试');
                }
            });
        })
        function settleCard(codeList,callback){
            $.post('ajax_request.php',{action:'settle_card',data:codeList},callback);
        }
    </script>

</div>