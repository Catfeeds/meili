/**
 * 地图标注
 */

//覆盖物对象   整个标注窗口对象
var marker, mapObj, oDrag = document.getElementById("lyq-map");
// 高德地图API功能
function mapInit(){
    //创建地图对象
    mapObj = new AMap.Map("lyq-map-all-map", {
        //二维地图显示视口
        view: new AMap.View2D({
            center:new AMap.LngLat(116.397428,39.90923),//地图中心点
            zoom:13 //地图显示的缩放级别
        })
    });
    mapObj.setDefaultCursor("Crosshair");//设置鼠标样式
    //在地图中添加ToolBar插件
    mapObj.plugin(["AMap.ToolBar"],function(){
        toolBar = new AMap.ToolBar();
        mapObj.addControl(toolBar);
    });
    //为地图注册click事件获取鼠标点击出的经纬度坐标
    var clickEventListener = AMap.event.addListener(mapObj,'click',function(e){
        //清除已标注覆盖物
        clearLays();
        //创建新覆盖物
        marker = new AMap.Marker({
            position:new AMap.LngLat(e.lnglat.getLng(),e.lnglat.getLat())
        });
        //覆盖物添加到地图
        marker.setMap(mapObj);
        //获取点击点的经纬度
        document.getElementById("lng").value = e.lnglat.getLng();
        document.getElementById("lat").value = e.lnglat.getLat();
    });
}

//开始标注
function labelMap(){
    mapInit();
    oDrag.style.display = "block";
}
//清除覆盖物
function clearLays(){
    //清除已标注覆盖物
    mapObj.clearMap();
}
//拖动窗口隐藏  取消标注
function cancelLabel(){
    //清除所有标注 与坐标
    mapObj.clearMap();
    document.getElementById("lng").value = '';
    document.getElementById("lat").value = '';
    oDrag.style.display = "none";
}
//标注后 保存位置 显示保存后地图
function saveLabel(){
    oDrag.style.display = "none";//拖动矿消失
    var lng = document.getElementById("lng").value;
    var lat = document.getElementById("lat").value;
    var mapShowObj = new AMap.Map("show_map", {
        //二维地图显示视口
        view: new AMap.View2D({
            center:new AMap.LngLat(lng,lat),//地图中心点
            zoom:mapObj.getZoom() //地图显示的缩放级别
        })
    });
    //创建新覆盖物
    var markerShow = new AMap.Marker({
        position:new AMap.LngLat(lng,lat)
    });
    //覆盖物添加到地图
    markerShow.setMap(mapShowObj);
}
