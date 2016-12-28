<?php global $getStr,$num,$orderIndex,$order,$list;
?>
<div id="core" style="height: 658px;">
    <div class="block">
        <div class="head" style="width: 98%;"><span>卡券列表</span></div>
        <div class="main">
            <table class="table sheet">
                <tbody><tr class="h">
                    <td width="100px">商户id</td>
                    <td width="80px">商户代号</td>
                    <td width="80px">商户名</td>
                    <td width="80px">商户信息</td>
                    <td width="80px">商户状态</td>
                    <td width="100px">操作</td>
                </tr>
                <?php foreach($list as $row):?>
                    <tr>
                        <td><?php echo $row['id']?></td>
                        <td><?php echo $row['p_code']?></td>
                        <td><?php echo $row['p_name']?></td>
                        <td><?php echo $row['p_inf']?></td>
                        <td><?php echo $row['p_status']?></td>
                    </tr>
                <?php endforeach ?>
                <tr>
                    <td colspan="6">
                        <div class="page_link">
                            <div class="in">
                                <span>共1页</span>
                                <span>第1页</span>
                                <form id="form_jump" action="" method="get">
                                    <input type="hidden" name="url" value="?/about/mod-sheet/page-1/index.html">
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
        <div class="head" style="width: 98%;"><span>使用说明</span></div>
        <div class="main content">

        </div>
    </div>

</div>