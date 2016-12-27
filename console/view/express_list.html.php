<?php global $getStr,$page,$num,$orderIndex,$order,$list;
?>

<?php global $getStr,$page,$num,$orderIndex,$order,$list;
?>

<div id="core" style="height: 618px;">
    <div class="block">
        <div class="head" style="width: 98%;"><span>产品列表</span></div>
        <div class="main">
            <table class="table sheet">
                <tbody><tr class="h">
                    <td>卡券名</td>
                    <td>收货地址</td>
                    <td>创建时间</td>
                    <td>操作</td>
                </tr>
                <?php foreach($list as $row):?>
                    <tr>
                        <td><?php echo $row['card_title']?></td>
                        <td><?php echo $row['province'].' '.$row['city'].' '.$row['area'].' '.$row['address'].' '.$row['name'].' '.$row['phone']?></td>
                        <td><?php echo $row['create_time']?></td>
                        <td><button class="button">详情</button></td>
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

    <script language="javascript">
        var lang_if_del_goods = "您确定要删除该产品吗？";
    </script>

    <script language="javascript">
        $('#add_operator').click(function(){
            var phone=$('#phone').val();
            var password=$('#password').val();
            $.post('ajax_request.php',{action:'add_operator',data:{phone:phone,password:password}},function(data){
                var back=backHandle(data);
                if(back){
                    location.href='?<?php echo $getStr?>';
                }else{
                    alert('faile');
                }
            })
        });
    </script>

</div>