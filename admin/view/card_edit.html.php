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
            <table class="table">
                <tr>

                </tr>
            </table>
                <div class="wrap" id="card">
                    <input class="card_type" id="card_type" placeholder="卡券类型">

                    <div class="gift" id="gift">
                        <div class="base_info" id="base_info">
                            <input class="logo_url" id="logo_url" placeholder="卡券商户logo">
                            <input class="brand_name" id="brand_name" placeholder="商户名字">
                            <input class="code_type" id="code_type" placeholder="Code展示类，如二维码">
                            <input class="title" id="title" placeholder="卡券名">
                            <input class="sub_title" id="sub_title" placeholder="券名">
                            <input class="color" id="color" placeholder="券颜色">
                            <input class="notice" id="notice" placeholder="卡券使用提醒出示核销">
                            <input class="service_phone" id="service_phone" placeholder="服务电话">
                            <input class="description" id="description" placeholder="卡券使用说明">

                            <div class="date_info" id="date_info">
                                <input class="type" id="type" placeholder="使用时间类型">
                                <input class="begin_timestamp" id="begin_timestamp" placeholder="启用时间">
                                <input class="end_timestamp" id="end_timestamp" placeholder="结束时间">
                            </div>
                            <div class="sku" id="sku">
                                <input class="quantity" id="quantity" placeholder="数量">
                            </div>
                            <input class="get_limit" id="get_limit" placeholder="每人领券数量限制">
                            <input class="use_custom_code" id="use_custom_code" placeholder="是否自定义code码">
                            <input class="bind_opennid" id="bind_opennid" placeholder="是否指定用户领取">
                            <input class="can_share" id="can_share" placeholder="卡券领取是否可分享">
                            <input class="can_give_friend" id="can_give_friend" placeholder="卡券能否转赠">
                            <input class="center_title" id="center_title" placeholder="卡券顶部居中按钮如立即使用">
                            <input class="center_sub_title" id="center_sub_title" placeholder="入口下方立即享受优惠">
                            <input class="center_url" id="center_url" placeholder="顶部居中的url">
                            <input class="custom_url_name" id="custom_url_name" placeholder="营销入口提示如卖场大优惠">
                            <input class="custom_url" id="custome_url" placeholder="自定义跳转URL">
                            <input class="custom_url_sub_title" id="custon_url_sub_title" placeholder="提示语如更多惊喜">
                            <input class="promotion_url_name" id="promotion_url_name" placeholder="产品介绍">
                            <input class="promotion_url" id="promotion_url" placeholder="外链地址链接">
                            <input class="source" id="source" placeholder="大众点评">
                        </div>
                        <input class="gift" id="gift" placeholder="兑换物品">
                    </div>
                </div>
        </div>
    </div>
    <div class="space"></div>
    <div class="block">
        <div class="head" style="width: 98%;"><span>商家选择</span></div>
        <div class="main">

        </div>
    </div>
    <input type="file" id="logo-up" name="logo-up" style="display: none">
    <script>
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