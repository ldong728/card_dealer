if (g_id != -1) {
    $("#sc_id").val(sc_id);
    $("#made_in").val(mi);
    $.post('ajax_request.php', {newGoods: g_id}, function (data) {
        var inf = eval('(' + data + ')');
        $('#g_name').append('<option value = "' + inf.id + '">' + inf.name + '</option>');
    });
    getGInf();
}

$("#g_name").change(function () {
    g_id = $("#g_name option:selected").val();
    getGInf();
});
$("#sc_id").change(function () {
    $("#g_name").load("ajax_request.php", {
        categoryCheck: $("#sc_id option:selected").val(),
        situation:1
    }, $("#g_name").empty())
});
$('#changeSc').change(function(){
    $.post('ajax_request.php',{alterCategory: $("#changeSc option:selected").val(),g_id:g_id},function(data){
    });
});
$(document).on('change', '.category', function () {
    $.post('ajax_request.php', {changeCategory: 1, d_id: $(this).attr('id'), value: $(this).val()},function(data){
        showToast('修改成功');
    });

})
$(document).on('change', '.sale', function () {
    //alert('change');
    $.post('ajax_request.php', {changeSale: 1, d_id: $(this).attr("id"), value: $(this).val()},function(data){
        showToast('修改成功');
    });

});
$(document).on('change', '.wholesale', function () {
    $.post('ajax_request.php', {changeWholesale: 1, d_id: $(this).attr("id"), value: $(this).val()},function(data){
        showToast('修改成功');
    });
});
$(document).on('click', '#add_category', function () {
    $.post('ajax_request.php', {addNewCategory: 1, g_id: g_id}, function (data) {
            $('#add-button').before( '<p>规格：<input class="detail-input category" type="text" id="' + data + '"value="规格' +data + '"/>' +
            '售价：<input class="detail-input" type="text" class="sale" id="' +data + '"value="9999"/>' +
            '<a class="detail-delete" href="consle.php?del_detail_id=' +data + '&g_id=' + g_id + '">删除此规格</a>' +
            '</p>');
    });
});

$(document).on('click', '.is_cover', function () {
    $.post('ajax_request.php', {set_cover_id: $(this).val(), g_id: g_id},function(data){
        showToast('已设为封面图')
    });
});
$(document).on('click','.img-upload',function(){
   $('#g-img-up').click();
});
$(document).on('change','#g-img-up',function(){
    $.ajaxFileUpload({
        url:'upload.php?g_id='+g_id,
        secureuri: false,
        fileElementId: $(this).attr('id'), //文件上传域的ID
        dataType: 'json', //返回值类型 一般设置为json
        success: function (v, status){
            if('SUCCESS'== v.state){
                var isCheck = (1 == v.cover ? 'checked = true' : '');
                var content = '<div class="demo-box"><input type="radio" name="is_cover"class="is_cover"value="' + v.id + '"' + isCheck + '/>作为缩略图'
                    + '<a href="#"class="deleteImg"id="'+v.md5+'"><img class="demo" src= "../' + v.url + '" alt = "error" /></a></div>';

                $('.img-upload').before(content);
            }else{
                showToast(v.state);
            }


        }  //服务器成功响应处理函数
    });

});
$(document).on('click','.alt_img',function(){
    var id=$(this).attr('id').slice(3)
    $('#alt_img'+id).click();
})
$(document).on('change','.alt_input',function(){
   var id=$(this).attr('id').slice(7);
    $.ajaxFileUpload({
        url:'upload.php?alt_img='+id,
        secureuri: false,
        fileElementId: 'alt_img'+id, //文件上传域的ID
        dataType: 'json', //返回值类型 一般设置为json
        success: function (v, status){
            if('SUCCESS'== v.state){
                $('#img'+id).attr('src','../'+ v.url);
                //var isCheck = (1 == v.cover ? 'checked = true' : '');
                //var content = '<div class="demo-box"><input type="radio" name="is_cover"class="is_cover"value="' + v.id + '"' + isCheck + '/>作为缩略图'
                //    + '<a href="#"class="deleteImg"id="'+v.md5+'"><img class="demo" src= "../' + v.url + '" alt = "error" /></a></div>';
                //
                //$('.img-upload').before(content);
            }else{
                showToast(v.state);
            }


        }  //服务器成功响应处理函数
    });
});


$(document).on('click','.deleteImg',function(){
    var id=$(this).attr('id');
    $.post('ajax_request.php',{del_g_img:1,md5:id,g_id:g_id},function(data){
        $('#'+id).parent().remove();
    });
});
$(document).on('click','.part_dft',function(){
   var id=$(this).attr('id').slice(5);
    var stu=true==$(this).prop('checked')?1:0;
    $.post('ajax_request.php',{change_part_stu:1,id:id,value:stu},function(data){
        if(data==1){
            showToast('已设为默认配件')
        }else{
            showToast('已取消默认配件')
        }
    });

});
$(document).on('click','.coop_dft',function(){
    var id=$(this).attr('id').slice(4);
    var stu=true==$(this).prop('checked')?1:0;
    $.post('ajax_request.php',{change_coop_stu:1,part_id:id,g_id:g_id,value:stu},function(data){
        if(data==1){
            showToast('已设定')
        }else{
            showToast('已取消')
        }
    });

});

/**
 * 获取货品信息
 */

function getGInf() {
    $('#g_id_img').val(g_id);
    $('#hidden_g_id').val(g_id);
    $('#goods_detail').empty();
    $('#goods_image').empty();
    $('#intro').empty();
    $('#changeSituation').empty();
    $('.parm-set').empty();
    $('#host_set').empty();
    $('#coop_set').empty();
    $('#changeCategory').css('display','none');

    $.post("ajax_request.php", {get_g_inf:1,g_id: g_id}, function (data) {
        var inf = eval('(' + data + ')');
        if(0==inf.goodsInf.situation){
            var stub='<a href="consle.php?goodsSituation=1&g_id='+g_id+'">上架</a>'
        }else{
            var stub='<a href="consle.php?goodsSituation=0&g_id='+g_id+'">下架</a>'
        }
        $('#intro').append(inf.goodsInf.intro);
        $('#changeSituation').append(stub);
        $('#name').val(inf.goodsInf.name);
        $('#s_name').val(inf.goodsInf.made_in);
        $('#produce_id').val(inf.goodsInf.produce_id);
        if(null!=inf.goodsInf.inf) {
            um.setContent(inf.goodsInf.inf);
        }else{
            um.setContent('');
        }
        if(null!=inf.afterInf) {
            afterEdit.setContent(inf.afterInf);
        }else{
            afterEdit.setContent('');
        }

        if(null!=inf.detail) {
            $.each(inf.detail, function (k, v) {
                var content = '<p>规格：<input class="detail-input category" type="text" id="' + v.id + '"value="' + v.category + '"/>' +
                    '售价：<input class="detail-input sale" type="text" class="sale" id="' + v.id + '"value="' + v.sale + '"/>' +
                    '<a class="detail-delete" href="consle.php?del_detail_id=' + v.id + '&g_id=' + g_id + '">删除此规格</a>' +
                    '</p>';
                $('#goods_detail').append(content);
            });
            $('#goods_detail').append('<div class="divButton"id="add-button"><p id="add_category">添加规格</p></div>');
        }
        if (null != inf.img) {
            $('#goods_image').append('<div class="module-title"><h4>图片展示</h4></div>');
            $.each(inf.img, function (k, v) {
                var isCheck = (1 == v.front_cover ? 'checked = true' : '');
                var content = '<div class="demo-box"><input type="radio" name="is_cover"class="is_cover"value="' + v.id + '"' + isCheck + '/>作为缩略图'
                    + '<a class="alt_img"id="alt'+v.id+'"><img class="demo"id="img'+ v.id+'" src= "../' + v.url + '" alt = "error" /></a><input type="file"class="alt_input"id="alt_img'+ v.id+'"name="alt_img'+ v.id+'"style="display: none">'
                    +'<a href="#"class="deleteImg"id="'+v.remark+'">删除</a></div>'
                $('#goods_image').append(content);
            });

        }
        $('#goods_image').append('<a class="img-upload"></a><input type="file"id="g-img-up"name="g-img-up"style="display: none">');
        var precon='<div class="module-title">'+
            '<h4>参数设置</h4>'+
            '</div><form id="updateParm"action="consle.php?updateParm=1&g_id='+g_id+'"method="post">';
        $.each(inf.parm,function(k,v){
            var cateName=k;
            var con='<table class="parmInput"><tr><td colspan="2">'+cateName+'</td></tr>'
            $.each(v,function(subk,subv){
                var scon='<tr><td>'+subv.name+'</td><td><input type="text" name="'+subv.col+'"value="'+subv.value+'"/></td></tr>'
                con+=scon;
            });
            con+='</table>'
            precon+=con
        });
        $('.parm-set').append(precon+'<button>提交参数修改</button></form>');

        $('#host_set').append('<div class="module-title"><h4>配件默认状态</h4></div>');
        if(null!=inf.parts) {
            $.each(inf.parts, function (k, v) {
                //alert('have parts');
                var checked = 1 == v.dft_check ? 'checked="checked"' : ''
                var con = '<div class="option_block"><input type="checkbox"class="part_dft"' + checked +
                    'id="parts' + v.id + '"/>' + v.part_name + ' ' + v.part_produce_id +'</div>'
                $('#host_set').append(con);

            });
        }
        $('#coop_set').append('<div class="module-title"><h4>搭配产品</h4></div>');
        if(null!=inf.coop) {
            $.each(inf.coop, function (k, v) {
                //alert('have parts');
                var checked = 1 == v.checked ? 'checked="checked"' : ''
                var con = '<div class="option_block"><input type="checkbox"class="coop_dft"' + checked +
                    'id="coop' + v.id + '"/>' + v.name + ' ' + v.produce_id +'</div>'
                $('#coop_set').append(con);
            });
        }
        $('#g_inf').slideDown('fast');
        $('#changeCategory').css('display','block');
    });
}
