<head>
    <?php include 'templates/header.php'?>
    <link rel="stylesheet" href="stylesheet/address.css?t=<?php echo rand(1000,9999)?>"/>
</head>
<body>
<div class="wrap">
    <div class="editAddress">
        <ul class="addressList">
            <?php foreach($addrlist as $row):?>
            <li id="li<?php echo $row['id']?>">
                <a class="address" href="controller.php?action=consume_online&address_id=<?php echo $row['id']?>">
                    <div class="address_hd">
                        <p>
                            <?php htmlout( $row['name'])?>
                            <span><?php echo $row['phone']?></span>
                        </p>
                        <span><?php htmlout($row['province'].$row['city'].$row['area'].'  '.$row['address'])?></span>
                    </div>
                </a>
                <div class="address_ft"id="<?php echo $row['id']?>">
                    <a class="default <?php echo (1==$row['dft_a']? 'choice':'')?> setdefault"id="dft<?php echo $row['id']?>">设为默认</a>
                    <a class="revise"id="alt<?php echo $row['id']?>"></a>
                    <a class="delete"id="dlt<?php echo $row['id']?>"style="float:right;display:block;padding-top:10px "></a>
                </div>
            </li>
            <?php endforeach?>
        </ul>
        <a class="addressAdd">
            添加新地址
        </a>
    </div>
    <div class="add_Address"id="add_addr"style="display: none">
            <div class="inputBox">
                <input type="hidden" name="address_id" id="address_id" value="-1">
                <input type="text" id="name" name="name" placeholder="请输入姓名">
                <input type="tel" id="phone" name="phone" placeholder="请输入手机号">
            </div>
            <div class="select" id="area-select">

            </div>
            <div class="textarea">
                <textarea id="address" name="address" placeholder="请输入详细地址"></textarea>
            </div>
            <div class="b_btn">
                <input type="submit" class="btn_save" value="保存地址">
            </div>

    </div>
    <div class="toast"></div>
    </div>
<script src="../js/ajaxcity.jquery.js"></script>
<script>
    var url = 'city.php';
    var provinceurl = url + '?a=province';
    var cityurl = url + '?a=city&pid=';
    var areaurl = url + '?a=area&pid=';
    var city_config = {
        'province':'pro',
        'city':'city',
        'area':'area'
    };
    $('#area-select').ajax_city_select(city_config);
    $('select').css('display','block');
</script>
<script>
        $(document).on('click','.revise',function(){
            $.post('ajax.php',{addr:'addr_edit',id:$(this).attr('id').slice(3)},function(data){
                $('#add_addr').fadeIn('slow');
                $('#add_addr').show();
                var value=backHandle(data);
                $('#address_id').val(value.id);
                $('#name').val(value.name);
                $('#phone').val(value.phone);
                $('#pro').val(value.pro_id);
                $('#city').append('<option value="'+value.city_id+'"selected="selected">'+value.city+'</option>');
                $('#area').append('<option value="'+value.area_id+'"selected="selected">'+value.area+'</option>');
                $('#address').text(value.address);
            });
        });
        $(document).on('click','.addressAdd',function(){
            $.post('ajax.php',{addr:'addr_count'},function(data){
                var re=backHandle(data);
                if(re <5){
                    $('#address_id').val(-1);
//                    $('#add_addr').fadeToggle('slow');
                    $('#add_addr').toggle();
                }else{

                    showToast('无法添加新地址');
                }
            })

        });
        $(document).on('click','.default',function(){
            alert('press');
            var id=$(this).attr('id').slice(3);
            var _=$(this);
            if(!_.hasClass('choice')){
                $('.default').removeClass('choice');
                _.addClass('choice');
                $.post('ajax.php',{addr:'set_addr_default',id:id},function(data){
                    if(backHandle(data)){

                    }
                });
            }


        });
        $(document).on('click','.delete',function(){
            var id=$(this).attr('id').slice(3);
            $.post('ajax.php',{deleteAddr:1,id:id},function(data){
               $('li#li'+id).fadeOut('slow');
            });
        });
        $(document).on('click', '.btn_save', function () {
            var addrData = {
                name: $('#name').val(),
                phone: $('#phone').val(),
                pro_id: $('#pro').val(),
                city_id: $('#city').val(),
                area_id: $('#area').val(),
                province: $('#pro :selected').text(),
                city: $('#city :selected').text(),
                area: $('#area :selected').text(),
                address: $('#address').val()
            };
            if($('#address_id').val()>-1)addrData.id=$('#address_id').val();
            $.post('ajax.php',{addr:'add_addr',data:addrData},function(data){
                if(backHandle(data)){
                   location.href='controller.php?consume_online=1';
                }
            });

        });


</script>
</body>