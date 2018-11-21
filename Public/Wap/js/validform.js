//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
var tip = true, submitBtnObj = null;
$(function(){
    $(".form-horizontal").Validform({
        tiptype:function(msg,o,cssctl) { if(o.type == 1) {tip = false;} if(o.type == 3 && tip === true) { showPop(msg, 'error', 1500, ''); } },
        tipSweep:true,
        ajaxPost:true,
        beforeSubmit:function(curform){
            //在验证成功后，表单提交前执行的函数，curform参数是当前表单对象。
            //这里明确return false的话表单将不会提交;
            submitBtnObj = $(curform).find('.submit-btn');
            submitBtnObj.prop('disabled', true);
        },
        callback:function(data) {
            tip = true;
            if (data.status == 1) {
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
            submitBtnObj.prop('disabled', false);
        }
    });
});