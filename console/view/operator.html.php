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
                    <td>头像</td>
                    <td>手机号</td>
                    <td>密码</td>
                    <td>操作</td>
                </tr>
                <?php foreach($list as $row):?>
                    <tr>
                        <td><img src="<?php echo isset($row['headimgurl'])? $row['headimgurl']:''?>" style="width: 35px"></td>
                        <td><?php echo $row['phone']?></td>
                        <td>**********</td>
                        <td></td>
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
        <div class="head" style="width: 98%;"><span>添加操作员</span></div>
        <div class="main">
                <table class="table">
                    <tbody><tr>
                        <td align="right" width="150px">
                            <input id="phone" name="phone" type="number" max="11" placeholder="手机号码">
                        </td>
                        <td>
                            <input id="password" name="password" type="text" placeholder="密码">
                            <input id="add_operator" class="button" type="button" value="新建操作员">
                        </td>
                    </tr>
                    </tbody></table>
        </div>
    </div>
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