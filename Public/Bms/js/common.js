//dom加载完成后执行的js
function beforeGet(){}
function afterGet(){}
function afterAjaxGetSuccess(){}
function afterAjaxGetError(){}
function beforePost(){}
function afterPost(){}
function afterAjaxPostSuccess(){}
function afterAjaxPostError(){}
$(function(){
    /**顶部警告栏*/
    var top_alert = $('.top-alert');

    //ajax get请求
    $('.ajax-get').click(function() {
        var target;
        var that = this;
        var data_ing_html = $(this).attr('data-ing-html');
        var data_old_html = $(this).find('span.label').html();
        beforeGet();
        if ( $(this).hasClass('confirm') ) {
            var data_confirm = ($(this).attr('data-confirm') == null ? '确认要执行该操作吗?' : $(this).attr('data-confirm'));
            if(!confirm(data_confirm)) {
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            if(data_ing_html != null) {
                $(that).find('span.label').html(data_ing_html);
            }
            $.get(target).success(function(data){
                afterGet();
                $(that).find('span.label').html(data_old_html);
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    } else {
                        updateAlert(data.info,'alert-success');
                    }
                    setTimeout(function() {
                        if (data.url) {
                            location.href=data.url;
                        } else if( $(that).hasClass('no-refresh')) {
                            top_alert.hide();
                            afterAjaxGetSuccess();
                        } else {
                            window.location.reload();
                        }
                    },1500);
                } else {
                    updateAlert(data.info,'alert-error');
                    setTimeout(function() {
                        if (data.url) {
                            location.href=data.url;
                        } else {
                            top_alert.hide();
                            afterAjaxGetError();
                        }
                    },1500);
                }
            });
        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function() {
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var need_confirm=false;
        var data_confirm = ($(this).attr('data-confirm') == null ? '确认要执行该操作吗?' : $(this).attr('data-confirm'));
        var data_ing_html = $(this).attr('data-ing-html');
        var data_old_html = $(this).html();
        beforePost();
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            form = $('.'+target_form);
            if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            } else if (form.get(0)==undefined) {
            	return false;
            } else if ( form.get(0).nodeName=='FORM' ) {
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm(data_confirm)) {
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined) {
                	target = $(this).attr('url');
                } else {
                	target = form.get(0).action;
                }
                query = form.serialize();
            } else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v) {
                    if(v.type=='checkbox' && v.checked==true || v.type=='input' || v.type=='hidden') {
                        need_confirm = true;
                    }
                })
                if ( need_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm(data_confirm)){
                        return false;
                    }
                }
                query = form.serialize();
            } else {
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm(data_confirm)) {
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            if(data_ing_html != null) {
                $(that).html(data_ing_html);
            }
            $.post(target,query).success(function(data) {
                afterPost();
                $(that).html(data_old_html);
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    } else {
                        updateAlert(data.info ,'alert-success');
                    }
                    setTimeout(function() {
                        if (data.url) {
                            location.href=data.url;
                        } else if( $(that).hasClass('no-refresh')) {
                            top_alert.hide();
                            $(that).removeClass('disabled').prop('disabled',false);
                            afterAjaxPostSuccess()
                        } else {
							$(that).removeClass('disabled').prop('disabled',false);
                            window.location.reload();
                        }
                    },1500);
                } else {
                    updateAlert(data.info,'alert-error');
                    setTimeout(function() {
                        if (data.url) {
                            location.href=data.url;
                        } else {
                            top_alert.hide();
                            $(that).removeClass('disabled').prop('disabled',false);
                            afterAjaxPostError()
                        }
                    },1500);
                }
            });
        }
        return false;
    });

    window.updateAlert = function (text,c) {
		text = text||'default';
		c = c||false;
		if ( text!='default' ) {
            top_alert.find('strong').text(text);
            top_alert.show();
		} else {
            top_alert.hide();
		}
		if ( c!=false ) {
            top_alert.removeClass('alert-error alert-warn alert-info alert-success').addClass(c);
		}
	};

    window.updateAlertWithTime = function (text, type, time) {
        updateAlert(text,type);
        setTimeout(function() {
            $('.top-alert').hide();
        },1500);
    }
});