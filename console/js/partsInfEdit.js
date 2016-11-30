//$('input').attr('onkeypress',"if(event.keyCode == 13) return false;");//屏蔽回车键
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
    //alert('change');
    sc_id=$("#sc_id option:selected").val()
    $("#g_name").load("ajax_request.php", {
        categoryCheck: sc_id,
        situation:9
    }, $("#g_name").empty())
});
$('#changeSc').change(function(){
    $.post('ajax_request.php',{alterCategory: $("#changeSc option:selected").val(),g_id:g_id},function(data){

    });
});
//detail 更改
$(document).on('change', '.category', function () {
    $.post('ajax_request.php', {changeCategory: 1, d_id: $(this).attr('id'), value: $(this).val()});

})
$(document).on('change', '.sale', function () {
    $.post('ajax_request.php', {changeSale: 1, d_id: $(this).attr("id"), value: $(this).val()});
});
$(document).on('change', '.wholesale', function () {
    $.post('ajax_request.php', {changeWholesale: 1, d_id: $(this).attr("id"), value: $(this).val()});
});
$(document).on('click', '#add_category', function () {
    $.post('ajax_request.php', {addNewCategory: 1, g_id: g_id}, function (data) {
        $('#goods_detail').append(data);
    });
});
$(document).on('click', '.is_cover', function () {
    $.post('ajax_request.php', {set_cover_id: $(this).val(), g_id: g_id});
});
$(document).on('click','.img-upload',function(){
    $('#parts-img-up').click();
});
$(document).on('change','#parts-img-up',function(){
    $.ajaxFileUpload({
        url:'upload.php?g_id='+g_id,
        secureuri: false,
        fileElementId: $(this).attr('id'), //文件上传域的ID
        dataType: 'json', //返回值类型 一般设置为json
        success: function (v, status){
            var random=123;
            var isCheck = (1 == v.front_cover ? 'checked = true' : '');
            var content ='<img class="demo" src= "../' + v.url +'" alt = "error" />';
            $('#goods_image').empty();
            $('#goods_image').append('<a class="img-upload">'+content+'</a><input type="file"id="parts-img-up"name="parts-img-up"style="display: none">');;

        },  //服务器成功响应处理函数
        error: function(data, status, e){
            alert(e);
        }
    });
});
$(document).on('click','.hostset',function(){
   var stu=$(this).prop('checked');
    $.post('ajax_request.php',{hostGoodsSet:1,g_id:$(this).attr('id'),part_g_id:g_id,situation:stu},function(data){

    });
});
/**
 * 获取货品信息
 */

function getGInf() {
//alert('get')
    $('#hidden_g_id').val(g_id);
    $('#goods_detail').empty();
    $('#goods_image').empty();
    $('#intro').empty();
    $('#changeSituation').empty();
    $('.parm-set').empty();
    $('#host_set').empty();
    $('#changeCategory').css('display','none');
    $.post("ajax_request.php", {get_parts_inf:1,g_id: g_id}, function (data) {
        var inf = eval('(' + data + ')');
        if(0==inf.goodsInf.situation){
            var stub='<a href="consle.php?goodsSituation=9&g_id='+g_id+'">上架</a>'
        }else{
            var stub='<a href="consle.php?goodsSituation=0&g_id='+g_id+'">下架</a>'
        }
        $('#intro').append(inf.goodsInf.intro);
        $('#changeSituation').append(stub);
        $('#name').val(inf.goodsInf.name);
        $('#produce_id').val(inf.goodsInf.produce_id);

                var detailContent = '<p>规格：<input type="text" class="category" id="' + inf.goodsInf.d_id + '"value="' + inf.goodsInf.d_name + '"/>' +
                    '售价：<input type="text" class="sale" id="' +inf.goodsInf.d_id + '"value="' + inf.goodsInf.sale + '"/>' +
                    '</p>';
                $('#goods_detail').append(detailContent);
        if (null != inf.goodsInf.url) {
                var content ='<img class="demo" src= "../' + inf.goodsInf.url + '" alt = "error" />'
        }else{
            var content='';
        }
        $('#goods_image').append('<div class="module-title"><h4>图片展示</h4></div><a class="img-upload">'+content+'</a><input type="file"id="parts-img-up"name="parts-img-up"style="display: none">');
        $('#host_set').append('<div class="module-title"><h4>对应产品</h4></div>')
        $.each(inf.hostGoods,function(k,v){
            var check=v.checked!=null?'checked="true"':''
            var con='<input type=checkbox class="hostset"id="'+ v.id+'"'+check+'/>'+ v.name
            $('#host_set').append(con)
        })
        $('#g_inf').slideDown('fast');
        $('#changeCategory').css('display','block');
    });
}