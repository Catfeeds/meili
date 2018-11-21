$(document).ready(function(){
    // 全选
    $('.check-all').click(
        function(){
            var that = $(this);
            that.parents('table').find("input[type='checkbox']").attr('checked', that.is(':checked'));
            if(that.is(':checked') == true) {
                that.parents('table').find("div.checker span").addClass('checked');
            } else {
                that.parents('table').find("div.checker span").removeClass('checked');
            }
        }
    );
    //隔行换色
    $('.tbody tr:odd').addClass("odd-row");

    //表单样式
    $('input[type=checkbox],input[type=radio],input[type=file]').uniform();

    //重置表格里的复选框
    $('table.reset-checkbox').find("input[type='checkbox']").attr('checked', false);
    $('table.reset-checkbox').find("div.checker span").removeClass('checked');
    //按钮点击跳转
    $('.href').bind('click',function(){ window.location.href = $(this).attr('url'); });

    //下拉菜单点击事件
    $('ul.dropdown-menu li').bind('click',function(){
        var that = $(this);
        that.parents('div.btn-group').find('button.checked').html(that.attr('data-title'));
        that.parents('div.btn-group').next('input').val(that.attr('data-value'));
    });

    //下拉菜单 选中
    $('div.btn-group').each(function(key,obj){
        if($(obj).find('ul li.selected a').html() == null) {
            $(obj).find('button.checked').html($(obj).find('button.checked').attr('data-default'));
        } else {
            $(obj).find('button.checked').html($(obj).find('ul li.selected a').html());
        }
    });

    //只能填写数字
    $('input.number-only').keyup(function(){
        this.value=this.value.replace(/[^\d]/g,'');
    });
    $('input.number-only').blur(function(){
        this.value=this.value.replace(/[^\d]/g,'');
    });
    //只能填写数字和小数点
    $('input.decimal-only').keyup(function(){
        this.value=this.value.replace(/[^\d.]/g,'');
    });
    $('input.decimal-only').blur(function(){
        this.value=this.value.replace(/[^\d.]/g,'');
    });

    $('a.refresh').click(function(){ window.location.reload(); })

    //定义setTimeout执行方法  双击文本框内容清空
    var TimeFn = null;
    $('input[type="text"]').click(function () {
        // 取消上次延时未执行的方法
        clearTimeout(TimeFn);
        //执行延时
        TimeFn = setTimeout(function(){
            //在此处写单击事件要执行的代码
        },300);
    });
    $('input[type="text"]').dblclick(function () {
        //取消上次延时未执行的方法
        clearTimeout(TimeFn);
        //双击事件的执行代码
        $(this).val('');
    });

    //点击高级查询
    $('.senior').click(function(){
        if($(this).next('div.senior-search').css('display') == 'none') {
            $(this).next('div.senior-search').show();
        } else {
            $(this).next('div.senior-search').hide();
        }
    });
    //$(document).click(function (ev) {
    //    if ($(ev.target).parents('div.senior-search').html() == null && $(ev.target).attr("class") != 'btn btn-warning senior') {
    //        $("div.senior-search").hide();
    //    }
    //});


    $('td.quick-edit').click(function () {
        // 取消上次延时未执行的方法
        clearTimeout(TimeFn);
        //执行延时
        TimeFn = setTimeout(function(){
            //在此处写单击事件要执行的代码
        },300);
    });
    //列表中单一字段修改
    $('td.quick-edit').dblclick(function () {
        //取消上次延时未执行的方法
        clearTimeout(TimeFn);
        //双击事件的执行代码
        var that = $(this), value = that.html(), is_html_flag = false;
        //是否存在span标签
        if(value.indexOf('span') >= 0) {
            value = that.find('span.quick-edit-value').html(); is_html_flag = true;
        }
        //是否变动文本框宽度
        var width = that.attr('data-width') == null ? 100 : that.attr('data-width');
        //value不存在标签才能 进行下一步
        if(value.match(new RegExp(/<[^>]+>/g)) == null) {
            var html = '<input type="text" style="margin-bottom:0;width:' + width + 'px" data-old-value="' + value + '" ' +
                'data-model="' + that.attr('data-model') + '" data-field="' + that.attr('data-field') + '" data-id="' + that.attr('data-id') + '" ' +
                'onblur="quick_edit_input_blur(this)">';
            if(is_html_flag)
                that.find('span.quick-edit-value').html(html);
            else
                that.html(html);
            that.find('input').focus().val(value);
        }
    });
    //自定义页号跳转
    $('#go').click(function(){
        var that = $(this), p = that.attr('data-p'), url = that.attr('data-url'),page_num = $('#page_num').val(), suffix = that.attr('data-suffix');
        if(page_num == '') {
            alert('请输入页号！'); return;
        } else {
            if(url.indexOf('/p/') == -1)
                url = url.replace('.'+suffix,'/p/'+page_num+'.'+suffix);
            else
                url = url.replace('/p/'+p,'/p/'+page_num);
            window.location.href = url;
        }
    });

    //列表中快速编辑下拉选择框 选中
    $('.quick-edit-select').each(function(key,obj){
        var focus_value = $(obj).attr('data-focus-value');
        $(obj).find('option').each(function(op_key,op_obj){
            if($(op_obj).val() == focus_value) {
                $(op_obj).attr('selected','selected'); return false;
            }
        });
    });
});
//$(function(){
//    $(window).resize(function(){
//        var winW = $(window).width();
//        var winH = $(window).height();
//        $(".img-enlarge-box .img-enlarge").click(function(){
//            //如果没有图片则不显示
//            if($(this).attr('src') === undefined){
//                return false;
//            }
//            // 创建弹出框以及获取弹出图片
//            var imgPopup = "<div id=\"uploadPop\" class=\"upload-img-popup\"></div>"
//            //var imgItem = $(this).find(".upload-pre-item").html();
//            var imgItem = "<img src=\""+$(this).attr('src')+"\"/>";
//
//            //如果弹出层存在，则不能再弹出
//            var popupLen = $(".upload-img-popup").length;
//            if( popupLen < 1 ) {
//                $(imgPopup).appendTo("body");
//                $(".upload-img-popup").html(
//                    imgItem + "<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\" style=\"color: #000;font-size: 16px;\"><i class=\"fa fa-remove\"></i></a>"
//                );
//            }
//
//            // 弹出层定位
//            var uploadImg = $("#uploadPop").find("img");
//            var popW = uploadImg.width()>600?600:uploadImg.width();
//            var popH = uploadImg.width()>600?uploadImg.height()*600/uploadImg.width():uploadImg.height();
//            var left = (winW -popW)/2;
//            var top = (winH - popH)/2 + 30;
//            $(".upload-img-popup").css({
//                "max-width" : winW * 0.9,
//                "left": left,
//                "top": top
//            });
//			uploadImg.css({
//			    "width":popW,
//                "height":popH
//			});
//        });
//        // 关闭弹出层
//        $("body").on("click", "#uploadPop .close-pop", function(){
//            $(this).parent().remove();
//        });
//    }).resize();
//
//    // 缩放图片
//    function resizeImg(node,isSmall){
//        if(!isSmall){
//            $(node).height($(node).height()*1.2);
//        } else {
//            $(node).height($(node).height()*0.8);
//        }
//    }
//})

//快速编辑
function quick_edit_input_blur(obj) {
    var that = $(obj), model = that.attr('data-model'), field = that.attr('data-field'), id = that.attr('data-id'), new_value = that.val(), old_value = that.attr('data-old-value');
    var target = '/'+model+'/quickEdit', data = {model:model,ids:id,field:field,value:new_value};
    if(new_value == old_value) {
        $(obj).parent().html(old_value); return;
    }
    $.post(target,data).success(function(data){
        if (data.status == 1) {
            $(obj).parent().html(new_value);
        } else {
            $(obj).parent().html(old_value);
            updateAlert(data.info,'alert-error');
            setTimeout(function() {
                $('.alert').hide();
            }, 1500);
        }
    });
}
//快速编辑
function quick_edit_select_change(obj) {
    var that = $(obj), model = that.attr('data-model'), field = that.attr('data-field'), id = that.attr('data-id'), value = that.val();
    var target = '/'+model+'/quickEdit', data = {model:model,ids:id,field:field,value:value};
    $.post(target,data).success(function(data){
        if (data.status == 1) {
            updateAlert(data.info,'alert-success');
        } else {
            updateAlert(data.info,'alert-error');
        }
        setTimeout(function() {
            $('.alert').hide();
        }, 1500);
    });
}