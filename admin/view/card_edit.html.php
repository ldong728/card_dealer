<?php global $parnerQuery;?>
<script src="js/ajaxfileupload.js"></script>

<div id="core" style="height: 618px;">
    <div class="block">
        <div class="head" style="width: 98%;"><span>上传logo</span></div>
        <div class="main">
                <table class="table">
                    <tbody><tr>
                        <td align="right" width="150px">
                            logo：
                        </td>
                        <td>
                            <span id="show_pic_1"></span>
                            <label class="uploadImg blank" <?php echo $articleInf ?'style="display:none"': 'style="display:inline-block"'?>>
                                <span>插入图片</span>
                            </label>
                            <img class="uploadImg" id="title_demo" style="padding: 0; max-width: 70px;height: auto;display: <?php echo $articleInf ? 'block':'none'?>" <?php echo $articleInf ? 'src="../'.$articleInf['art_img'].'"':''?>/>

                            <input type="hidden" name="art_img" id="title_name" <?php echo $articleInf? 'value="'.$articleInf['art_img'].'"':''?>/>
                            &nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    </tbody></table>
        </div>
    </div>
    <div class="space"></div>
    <div class="block">
        <div class="head" style="width: 98%;"><span>卡券信息</span></div>
        <div class="main">
            <table class="table baseInfo">
                <tr>
                    <input type="hidden" class="base_info" id="logo_url">
                    <td>商户名字：</td><td><input class="base_info" id="brand_name" type="text" placeholder="商户名字"></td>
                    <td>卡券名字：</td><td><input class="base_info" id="title" placeholder="卡券名"></td>
                </tr>
                <tr>
                    <td>颜色：</td>
                    <td>
                        <select class="base_info" id="color">
                            <option value="Color010" style="color: #63b359">#63b359</option>
                            <option value="Color020" style="color: #2c9f67">#2c9f67</option>
                            <option value="Color030" style="color: #509fc9">#509fc9</option>
                            <option value="Color040" style="color: #5885cf">#5885cf</option>
                            <option value="Color050" style="color: #9062c0">#9062c0</option>
                            <option value="Color060" style="color: #d09a45">#d09a45</option>
                            <option value="Color070" style="color: #e4b138">#e4b138</option>
                            <option value="Color080" style="color: #ee903c">#ee903c</option>
                            <option value="Color081" style="color: #f08500">#f08500</option>
                            <option value="Color082" style="color: #a9d92d">#a9d92d</option>
                            <option value="Color090" style="color: #dd6549">#dd6549</option>
                            <option value="Color100" style="color: #cc463d">#cc463d</option>
                            <option value="Color101" style="color: #cf3e36">#cf3e36</option>
                            <option value="Color102" style="color: #5E6671">#5E6671</option>
                        </select>
                    </td>
                    <td>服务电话：</td><td><input type="tel" class="base_info" id="service_phone" </td>
                </tr>
                <tr>
                    <td>副标题：</td><td><input type="text" class="base_info" id="sub_title"></td>
                    <td>code展示类型：</td><td>默认<input type="hidden" class="base_info" id="code_type" value="CODE_TYPE_ONLY_BARCODE"> </td>
                </tr>
                <tr>
                    <td>使用提醒：</td><td><input type="text" maxlength="25" class="base_info" id="notice"></td>
                    <td>卡券使用说明：</td><td><textarea cols="30" rows="3" class="base_info" id="description"></textarea></td>
                </tr>
                <tr>
                    <td>生效时间：</td><td><input class="date_info" data-key="date_info" id="begin_timestamp" type="date" ></td>
                    <td>有效期至：</td><td><input class="date_info" id="end_timestamp" type="date" ></td>
                </tr>
                <tr>
                    <td>发行量：</td><td><input class="sku" id="quantity" type="number" value="1000"></td><td>定价：</td><td><input type="text" id="price"></td>
                </tr>
                <tr>
                    <td>选择商户</td>
                    <td><select id="partner_id">
                            <?php foreach($parnerQuery as $row):?>
                            <option value="<?php echo $row['id']?>"><?php echo $row['p_code']?></option>
                            <?php endforeach ?>
                    </select></td>
                    <td colspan="2"><button class="button" id="card_create">创建</button> </td>
                </tr>

<!--                <div class="wrap" id="card">-->
<!--                <input class="card_type" id="card_type" placeholder="卡券类型">-->
<!---->
<!--                <div class="gift" id="gift">-->
<!--                    <div class="base_info" id="base_info">-->
<!--                        <input class="logo_url" id="logo_url" placeholder="卡券商户logo">-->
<!--                        <input class="brand_name" id="brand_name" placeholder="商户名字">-->
<!--                        <input class="code_type" id="code_type" placeholder="Code展示类，如二维码">-->
<!--                        <input class="title" id="title" placeholder="卡券名">-->
<!--                        <input class="sub_title" id="sub_title" placeholder="券名">-->
<!--                        <input class="color" id="color" placeholder="券颜色">-->
<!--                        <input class="notice" id="notice" placeholder="卡券使用提醒出示核销">-->
<!--                        <input class="service_phone" id="service_phone" placeholder="服务电话">-->
<!--                        <input class="description" id="description" placeholder="卡券使用说明">-->
<!---->
<!--                        <div class="date_info" id="date_info">-->
<!--                            <input class="type" id="type" placeholder="使用时间类型">-->
<!--                            <input class="begin_timestamp" id="begin_timestamp" placeholder="启用时间">-->
<!--                            <input class="end_timestamp" id="end_timestamp" placeholder="结束时间">-->
<!--                        </div>-->
<!--                        <div class="sku" id="sku">-->
<!--                            <input class="quantity" id="quantity" placeholder="数量">-->
<!--                        </div>-->
<!--                        <input class="get_limit" id="get_limit" placeholder="每人领券数量限制">-->
<!--                        <input class="use_custom_code" id="use_custom_code" placeholder="是否自定义code码">-->
<!--                        <input class="bind_opennid" id="bind_opennid" placeholder="是否指定用户领取">-->
<!--                        <input class="can_share" id="can_share" placeholder="卡券领取是否可分享">-->
<!--                        <input class="can_give_friend" id="can_give_friend" placeholder="卡券能否转赠">-->
<!--                        <input class="center_title" id="center_title" placeholder="卡券顶部居中按钮如立即使用">-->
<!--                        <input class="center_sub_title" id="center_sub_title" placeholder="入口下方立即享受优惠">-->
<!--                        <input class="center_url" id="center_url" placeholder="顶部居中的url">-->
<!--                        <input class="custom_url_name" id="custom_url_name" placeholder="营销入口提示如卖场大优惠">-->
<!--                        <input class="custom_url" id="custome_url" placeholder="自定义跳转URL">-->
<!--                        <input class="custom_url_sub_title" id="custon_url_sub_title" placeholder="提示语如更多惊喜">-->
<!--                        <input class="promotion_url_name" id="promotion_url_name" placeholder="产品介绍">-->
<!--                        <input class="promotion_url" id="promotion_url" placeholder="外链地址链接">-->
<!--                        <input class="source" id="source" placeholder="大众点评">-->
<!--                    </div>-->
<!--                    <input class="gift" id="gift" placeholder="兑换物品">-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
    <div class="space"></div>

    <input type="file" id="logo-up" name="logo-up" style="display: none">
    <script>
        var card = {
            card: {
                card_type: 'GIFT', gift: {
                    base_info: {
                        date_info: {type: 'DATE_TYPE_FIX_TIME_RANGE'},
                        sku: {},
                        get_limit: 500,
                        use_custom_code: false,
                        bind_openid: true,
                        can_share: false,
                        can_give_friend: true,
                        center_sub_title: "线下使用",
                        custom_url_name: "立即使用",
                        custom_url: "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN ?>/mobile/controller.php?consume_online=1",
                        custom_url_sub_title: "快递到家",
                        promotion_url_name: "更多优惠",
                        promotion_url: "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN ?>/mobile/controller.php?card_mall=1",
                        source: "谷多电子商务"
                    },
                    gift:"卡券测试"
                }
            }
        };
    </script>
    <script>
        $('#card_create').click(function() {
            $.each($('.base_info'), function (k, v) {
                var value = jQuery(v);
                card.card.gift.base_info[v.id] = value.val();
            });
            var title=$('#title').val();
            var color=$('#color').find('option:selected').text();
            var begin=Math.round(new Date($('#begin_timestamp').val()).getTime()/1000);
            var end=Math.round(new Date($('#end_timestamp').val()).getTime()/1000);
            var quantity=$('#quantity').val();
            card.card.gift.base_info.sku.quantity = quantity;
            card.card.gift.base_info.date_info.begin_timestamp = begin;
            card.card.gift.base_info.date_info.end_timestamp = end;
            var cardString=JSON.stringify(card);
//            alert(JSON.stringify(card));
//            var data={card: card, inf:{partner_id: $('#partner_id').val(),title:title,color:color,begin:begin,end:end,quantity:quantity}, price: {0: $('#price').val()}}
//            alert(JSON.stringify(data));

            $.post('ajax_request.php', {
                pms: pms,
                method: 'create_card',
                data: {card: cardString, inf:{partner_id: $('#partner_id').val(),title:title,color:color,begin:begin,end:end,quantity:quantity}, price: {0: $('#price').val()}}
            }, function (data) {
                var re=backHandle(data);
                alert(re);
            });

        });
        $(document).on('click', '.uploadImg', function () {
            $('#logo-up').click();
        });
        $(document).on('change', '#logo-up', function () {
            $.ajaxFileUpload({
                url: 'upload.php',
                secureuri: false,
                fileElementId: $(this).attr('id'), //文件上传域的ID
                dataType: 'json', //返回值类型 一般设置为json
                success: function (v, status) {
                    if ('SUCCESS' == v.state) {
//                                var content = '<a href="#"class="delete-front-img"id="'+ v.id+'"><img src="../'+ v.url+'"/></a>';
//                                $('.front-img-upload').before(content);
                        $('#title_demo').attr('src',v.logo);
                        $('#title_demo').fadeIn('fast');
                        $('.blank').hide();
                        $('#title_name').val(v.url);
                        $('#logo_url').val(v.logo);
                    } else {
                        showToast(v.state);
                    }
                },//服务器成功响应处理函数
                error: function (d) {
                    alert('error');
                }
            });
        });


        function fillData(element) {
            var obj = jQuery(element);
            var data = {};
            if (obj.children().length > 0) {
                data = {};
                $.each(obj.children(), function (k, v) {
                    data[v.id]=fillData(v);
                });
                return data;
            } else {
                return element.value;
            }
        }
    </script>

</div>