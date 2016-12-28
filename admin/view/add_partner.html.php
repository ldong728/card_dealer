
<script type="text/javascript" src="js/md5.js"></script>
<div id="core" style="height: 658px;">
    <div class="block">
        <div class="head" style="width: 98%;"><span>创建商家</span></div>
        <div class="main">
            <table class="table sheet">
                <tbody>
                    <tr>
                        <td>商家代号</td><td><input type="text" id="p_code"> </td>
                    </tr>
                    <tr>
                        <td>商家名称</td><td><input type="text" id="p_name"></td>
                    </tr>
                    <tr>
                        <td>商家密码</td><td><input type="text" id="password"></td>
                    </tr>
                    <tr>
                        <td>商家信息</td><td><textarea cols="30" rows="3" id="p_inf"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2"><button class="button" id="add">添加</button></td>
                    </tr>
                </tbody></table>
        </div>
    </div>
    <div class="space"></div>
    <script>
       $('#add').click(function(){
           var empty=false;
           var data={};
           $.each($('input'),function(k,v){
               if(v.value)data[v.id]= v.value;
               else empty=true;
           });
           if(!empty){
               data.p_inf=$('#p_inf').val();
               data.password=hex_md5(data.password);
               addRecord('partner_tbl',data,'ignore',function(data){
                   location.href='?menu=<?php echo $_GET['menu']?>&sub=partner_list'
               });
           }
       }) ;
    </script>