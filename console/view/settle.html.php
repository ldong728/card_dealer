<?php global $getStr,$page,$num,$orderIndex,$order,$list;
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
                    <td><input type="checkbox" id="<?php echo $card_code?>" </td>
                    <td><?php echo $row['card_order_id']?></td>
                    <td><?php echo $row['card_title']?></td>
                    <td><?php echo $row['price']?></td>
                    <td><?php echo $row['consume_time']?></td>
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
        <div class="head" style="width: 98%;"><span>搜索产品</span></div>
        <div class="main">
            <form id="form_search" method="post" action="?/goods/mod-sheet/index.html">
                <table class="table">
                    <tbody><tr>
                        <td align="right" width="150px">
                            <select name="field">
                                <option value="goo_title">产品名称</option>
                                <option value="goo_sn">产品编号</option>
                                <option value="goo_text">产品描述</option>
                            </select>
                        </td>
                        <td>
                            <input name="key" type="text">
                            <input class="button" type="button" value="搜索产品" onclick="do_search()">
                        </td>
                    </tr>
                    </tbody></table>
            </form>
        </div>
    </div>
    <script language="javascript">
        var lang_if_del_goods = "您确定要删除该产品吗？";
    </script>

    <script language="javascript">
        function do_search()
        {
            var obj = document.getElementById("form_search");
            site = obj.action.lastIndexOf("/");
            str = obj.action.substr(site,obj.action.length - site);
            obj.action = obj.action.substr(0,site) + '/field-' + obj.field.value + '/key-' + obj.key.value + str;
            obj.submit();
        }
        function del_goods(val)
        {
            if(confirm(lang_if_del_goods))
            {
                ajax("post","?/deal/dir-goods/","cmd=del_goods&id=" + val,
                    function(data)
                    {
                        if(data == 1) document.location.replace(document.location.href);
                    });
            }
        }
    </script>

</div>