//覆盖物对象  地图对象
var marker, map;
//整个标注窗口对象  小地图显示对象
var oDrag = document.getElementById("lyq-map"), showMap = document.getElementById("show-map");
// 百度地图API功能 创建Map对象
map = new BMap.Map("lyq-map-all-map");
//为地图添加点击事件
map.addEventListener("click", label);
//滚动缩放设置
map.enableScrollWheelZoom(true);
// 右下比例尺
map.addControl(new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));
//鼠标样式
map.setDefaultCursor("Crosshair");
//右上角，仅包含平移和缩放按钮
map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT}));

//地图初始化操作
function mapInit(city){
    // 初始化地图,设置城市和地图级别。
    map.centerAndZoom(city,12);
}

//城市下拉选择
var cityList = new BMapLib.CityList({
    container: 'lyq-map-container',
    map: map
});

//点击方法  标注 添加覆盖物 获取经纬度
function label(e){
    //清楚覆盖物
    clearLays();
    // 创建标注
    marker = new BMap.Marker(new BMap.Point(e.point.lng, e.point.lat));
    //添加标注到地图
    map.addOverlay(marker);
    //获取经纬度 缩放级别
    document.getElementById("hidden-lng").value = e.point.lng;
    document.getElementById("hidden-lat").value = e.point.lat;
    document.getElementById("hidden-zoom").value = map.getZoom();

    var geoc = new BMap.Geocoder();
    var pt = e.point;
    geoc.getLocation(pt, function(rs) {
        var addComp = rs.addressComponents;
        //alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
        document.getElementById("hidden-province").value = addComp.province;
        document.getElementById("hidden-city").value = addComp.city;
        document.getElementById("hidden-district").value = addComp.district;
        document.getElementById("hidden-street").value = addComp.street;
        document.getElementById("hidden-street-number").value = addComp.streetNumber;
    });
}
//清除标注
function clearLays(){
    map.clearOverlays();//清除标注
}
//开始标注
function labelMap(city){
    //拓展 可根据ip地址定位
    mapInit(city);
    //地图弹出框
    oDrag.style.display = "block";
}
//拖动窗口隐藏  取消标注
function cancelLabel(){
    //清除所有标注 与坐标
    map.clearOverlays();
    //清空经纬度数据
    document.getElementById("hidden-lng").value = '';
    document.getElementById("hidden-lat").value = '';
    document.getElementById("hidden-zoom").value = '';
    document.getElementById("hidden-province").value = '';
    document.getElementById("hidden-city").value = '';
    document.getElementById("hidden-district").value = '';
    document.getElementById("hidden-street").value = '';
    document.getElementById("hidden-street-number").value = '';
    oDrag.style.display = "none";
}
//标注后 保存位置 显示保存后地图
function saveLabel(){
    //拖动矿消失
    oDrag.style.display = "none";
    //小地图显示 宽高赋值
    showMap.style.width = "300px";
    showMap.style.height = "250px";
    //创建标注后地图
    var map_show = new BMap.Map("show-map");
    var lng = document.getElementById("hidden-lng").value;
    var lat = document.getElementById("hidden-lat").value;
    var zoom = document.getElementById("hidden-zoom").value;
    //map.getZoom()获取缩放值
    map_show.centerAndZoom(new BMap.Point(lng, lat), zoom);
    // 创建标注
    var marker_show = new BMap.Marker(new BMap.Point(lng, lat));
    // 将标注添加到地图中
    map_show.addOverlay(marker_show);
    //marker_show.setAnimation(BMAP_ANIMATION_BOUNCE);
    //经纬度 缩放级别 复赋值到表单
    document.getElementById("form-lng").value = lng;
    document.getElementById("form-lat").value = lat;
    document.getElementById("form-zoom").value = zoom;
    document.getElementById("form-province").value = document.getElementById("hidden-province").value;
    document.getElementById("form-city").value = document.getElementById("hidden-city").value;
    document.getElementById("form-district").value = document.getElementById("hidden-district").value;
    document.getElementById("form-street").value = document.getElementById("hidden-street").value;
    document.getElementById("form-street-number").value = document.getElementById("hidden-street-number").value;

    $("input[name='unity_id']").val('');
    $('button.checked').html('--全部--');
}

//建立一个自动完成的对象  文本框自动加载地区
//var auto = new BMap.Autocomplete({
//    "input" : "suggestId",
//    "location" : map
//});