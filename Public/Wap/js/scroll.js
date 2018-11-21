var isLoading = false, query = getQuery(), p = 1;
$(function() {
    getList();
    $('.infinite-scroll').on('infinite', function () {
        getList();
    });
});
function getList() {
    if(isLoading === false) {
        isLoading = true;
        showScrollLoader();
        $.post(target, query).success(function(data) {
            if(data.status == 0) {
                //var html = getHtml('');
                //$('.scroll-append-box').append(html);
                if(p == 1) {
                    $('.scroll-append-box').append('<div style="padding: 40px;text-align: center">暂无结果！</div>');
                }
            } else {
                p++;
                query = getQuery();
                var html = ''
                for(var i in data.data) {
                    html += getHtml(data.data[i]);
                }
                $('.scroll-append-box').append(html);
                isLoading = false;
            }
            hideScrollLoader();
        });
    }
}
function showScrollLoader() {
    $('.infinite-scroll-preloader').show();
}
function hideScrollLoader() {
    $('.infinite-scroll-preloader').hide();
}