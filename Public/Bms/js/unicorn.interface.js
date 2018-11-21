$(document).ready(function(){
	$.fn.peity.defaults.line = {strokeWidth: 1, delimeter: ",", height: 24, max: null, min: 0, width: 50};
	$.fn.peity.defaults.bar = {delimeter: ",", height: 24, max: null, min: 0, width: 50};

    $(".peity-line-success span").peity("line", {colour: "#459D1C", strokeColour: "#459D1C"});
    $(".peity-line-important span").peity("line", {colour: "#B94A48", strokeColour: "#B94A48"});
    $(".peity-line-default span").peity("line", {colour: "#777", strokeColour: "#777"});
    $(".peity-line-inverse span").peity("line", {colour: "#000", strokeColour: "#000"});
    $(".peity-line-info span").peity("line", {colour: "#2B6582", strokeColour: "#2B6582"});
    $(".peity-line-warning span").peity("line", {colour: "#E67E04", strokeColour: "#E67E04"});

	$(".peity-bar-success span").peity("bar", {colour: "#459D1C"});
	$(".peity-bar-important span").peity("bar", {colour: "#B94A48"});
	$(".peity-bar-default span").peity("bar", {colour: "#777"});
    $(".peity-bar-inverse span").peity("bar", {colour: "#000"});
    $(".peity-bar-info span").peity("bar", {colour: "#2B6582"});
    $(".peity-bar-warning span").peity("bar", {colour: "#E67E04"});
});
