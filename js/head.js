//$(document).ready(function(){
//    $(document).on('click','.daohang',function(){
//       triggNav();
//    });
//    $(document).on('click','#search-button',function(){
//        window.location.href='controller.php?getList=1&name='+$('#key-word').val();
//    });
//
//});
//var triggNav=function(){
//    if('none'==$('.head_nav').css('display')){
//        $('.head_nav').css('display','block');
//    }else{
//        $('.head_nav').css('display','none');
//    }
//
//}
function showToast(str){
    $('.toast').empty();
    $('.toast').append(str)
    $('.toast').fadeIn('fast')
    var t = setTimeout('$(".toast").fadeOut("slow")', 800);
}
function loading(){
    $('.loading').show();
}
function stopLoading(){
    $('.loading').hide();
}
function backHandle(data){
     var re=eval('('+data+')');
    if(0==re.errcode){
        var state= null==re.data?true:re.data;
        console.log(state);
        return state;
    }else{
        console.log('error: '+re.errmsg);
        return false;
    }
}