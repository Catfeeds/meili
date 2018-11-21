//dom加载完成后执行的js
function beforeGet(){}
function afterGet(){}
function beforePost(){}
function afterPost(){}
var cz = ''; //执行的什么操作 回调callback判断
$(function(){
    //ajax get请求
    $('.ajax-get').click(function() {
        //var target,that = $(this);
        //var data_ing_html = that.attr('data-ing-html');
        //var data_old_html = that.find('span.label').html();
        //beforeGet();
        //if ( that.hasClass('confirm') ) {
        //    var data_confirm = (that.attr('data-confirm') == null ? '确认要执行该操作吗?' : that.attr('data-confirm'));
        //    if(!confirm(data_confirm)) {
        //        return false;
        //    }
        //}
        //if ( (target = that.attr('href')) || (target = that.attr('url')) ) {
        //    if(data_ing_html != null) {
        //        that.find('span.label').html(data_ing_html);
        //    }
        //    cz = that.attr('data-cz'); //执行的什么操作 回调callback判断
        //    $.get(target).success(function(data){
        //        afterGet();
        //        that.find('span.label').html(data_old_html);
        //        if (data.status==1) {
        //            if (data.url) {
        //                showPop(data.info, 'success', 1500, data.url);
        //            } else {
        //                showPop(data.info, 'success', 1500, 'callback');
        //            }
        //        } else {
        //            if (data.url) {
        //                showPop(data.info, 'error', 1500, data.url);
        //            } else {
        //                showPop(data.info, 'error', 1500, 'callback');
        //            }
        //        }
        //    });
        //}
        ajaxGet(this);
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function() {
        var target,query,form,that = $(this);
        var target_form = that.attr('target-form');
        var need_confirm=false;
        var data_confirm = (that.attr('data-confirm') == null ? '确认要执行该操作吗?' : that.attr('data-confirm'));
        var data_ing_html = that.attr('data-ing-html');
        var data_old_html = that.html();
        beforePost();
        if( (that.attr('type')=='submit') || (target = that.attr('href')) || (target = that.attr('url')) ) {
            form = $('.'+target_form);
            if (that.attr('hide-data') === 'true') {//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            } else if (form.get(0)==undefined) {
            	return false;
            } else if ( form.get(0).nodeName=='FORM' ) {
                if ( that.hasClass('confirm') ) {
                    if(!confirm(data_confirm)) {
                        return false;
                    }
                }
                if(that.attr('url') !== undefined) {
                	target = that.attr('url');
                } else {
                	target = form.get(0).action;
                }
                query = form.serialize();
            } else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v) {
                    if(v.type=='checkbox' && v.checked==true) {
                        need_confirm = true;
                    }
                })
                if ( need_confirm && that.hasClass('confirm') ) {
                    if(!confirm(data_confirm)){
                        return false;
                    }
                }
                query = form.serialize();
            } else {
                if ( that.hasClass('confirm') ) {
                    if(!confirm(data_confirm)) {
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            that.addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            if(data_ing_html != null) {
                that.html(data_ing_html);
            }
            cz = that.attr('data-cz'); //执行的什么操作 回调callback判断
            $.post(target,query).success(function(data) {
                afterPost();
                that.html(data_old_html);
                if (data.status==1) {
                    if (data.url) {
                        showPop(data.info, 'success', 1500, data.url);
                    } else {
                        that.removeClass('disabled').prop('disabled',false);
                        showPop(data.info, 'success', 1500, 'callback');
                    }
                } else {
                    if (data.url) {
                        showPop(data.info, 'error', 1500, data.url);
                    } else {
                        that.removeClass('disabled').prop('disabled',false);
                        showPop(data.info, 'error', 1500, 'callback');
                    }
                }
            });
        }
        return false;
    });
});

function ajaxGet(obj) {
    var target,that = $(obj);
    var data_ing_html = that.attr('data-ing-html');
    var data_old_html = that.find('span.label').html();
    beforeGet();
    if ( that.hasClass('confirm') ) {
        var data_confirm = (that.attr('data-confirm') == null ? '确认要执行该操作吗?' : that.attr('data-confirm'));
        if(!confirm(data_confirm)) {
            return false;
        }
    }
    if ( (target = that.attr('href')) || (target = that.attr('url')) ) {
        if(data_ing_html != null) {
            that.find('span.label').html(data_ing_html);
        }
        cz = that.attr('data-cz'); //执行的什么操作 回调callback判断
        $.get(target).success(function(data){
            afterGet();
            that.find('span.label').html(data_old_html);
            if (data.status==1) {
                if (data.url) {
                    showPop(data.info, 'success', 1500, data.url);
                } else {
                    showPop(data.info, 'success', 1500, 'callback');
                }
            } else {
                if (data.url) {
                    showPop(data.info, 'error', 1500, data.url);
                } else {
                    showPop(data.info, 'error', 1500, 'callback');
                }
            }
        });
    }
}

function showPop(msg, flag, time, redirect) {
    if(cz == 'sign' && flag == 'success') {
        $('.sign .sign-con').html('<p class="a1"><span>+</span><em>'+msg+'</em><i>积分</i></p>'); return;
    } else if(cz == 'sign' && flag == 'error') {
        $('.sign .sign-con').html('<p class="a2">'+msg+'</p>'); return;
    }
    //alert(msg);
    $('.lyq-notification h1').html(msg);
    $(".lyq-notification,.bg").show();
    setTimeout(function(){
        $(".lyq-notification,.bg").hide();
        $('.lyq-notification h1').html('');
        if(redirect == 'callback'){
            if(flag == 'success') {
                success_callback(); return false;
            }
            if(flag == 'error') {
                error_callback(); return false;
            }
        } else if(redirect != '' && redirect != 'callback') {
            window.location.href = redirect;
        }
    }, time);
}

var x = 60;
function time(o) {
    if (x == 0) {
        o.removeAttribute("disabled");
        o.value="获取验证码";
        x = 60;
    } else {
        o.setAttribute("disabled", true);
        o.value= x + "s重新获取";
        x--;
        setTimeout(function(){time(o)},1000)
    }
}

function getSmsVerify(account, unique_code, target, that) {
    if(account == '') {
        showPop('请输入手机号', 'error', 1500, ''); return;
    }
    $.post(target, {account:account,unique_code:unique_code}).success(function(data) {
        if (data.status == 1) {
            showPop(data.info, 'success', 1500, '');
            time(that);
        } else {
            showPop(data.info, 'error', 1500, '');
        }
    });
}

function checkSmsVerify(account, unique_code, target, verify, that, form_obj) {
    if(account == '') {
        showPop('请输入手机号', 'error', 1500, ''); return;
    }if(verify == '') {
        showPop('请输入验证码', 'error', 1500, ''); return;
    }
    that.setAttribute("disabled", 'true');
    $.post(target, {account:account,unique_code:unique_code,verify:verify}).success(function(data) {
        if (data.status == 1) {
            form_obj.submit();
        } else {
            showPop(data.info, 'error', 1500, '');
        }
        that.removeAttribute("disabled");
    });
}