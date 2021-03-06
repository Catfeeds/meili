/**
 * jQuery.convert.js1.0 master
 * extends: jQuery - underscore
 * time: 2016-9-12
 * author: iexn
 */
(function($) {
	$.fn.extend({
		changeClass: function(remove, add, condite) {
			$(this).removeClass(remove);
			$(this).addClass(add);
		},
		convert: function(param, hid) {
			var setAuto;
			hid = hid || '';
			if(_.isString(param)) {
				param = {
					pro: param,
					hid: hid
				}
			}
			param = _.defaults(param, {
				pro: 'convert_spro',
				hid: 'convert_shid',
				show: false,
				event: 'click',
				delay: false,
				posite: false,
				nexts: false,
				trigger: false,
				infinite: false,
				before: function(index, prev) {},
				after: function(index, prev) {},
			});
			if(_.isString(param.show)) {
				param.show = param.show.split('|');
				param.pro = param.show[0];
				param.hid = param.show[1] || '';
			}
			if(_.isObject(param.trigger)) {
				for(var t in param.trigger) {
					if(_.isString(param.trigger[t])) {
						param.trigger[t] = param.trigger[t].split('|');
						param.trigger[t] = {
							pro: param.trigger[t][0],
							hid: param.trigger[t][1] || ''
						};
					}
				}
			}
			var $this = this; // 显示集元素
			// 手动切换
			if(!$this.hasClass('notPartakeAll')) {
				$this.on(param.event, function() {
					if(!$(this).hasClass('notPartake')) {
						Change($this.index(this));
					}
				});
			}
			// 自动切换
			if(false !== param.delay) {
				setAuto = setInterval(auto, param.delay);
				$this.on('mousemove', function() {
					clearInterval(setAuto);
				});
				$this.on('mouseout', function() {
					setAuto = setInterval(auto, param.delay);
				});
			}
			// 自动执行
			function auto() {
				Change(($this.index(+$this.filter('.' + param.pro)) + 1));
			}
			// 固定位置切换
			if(false !== param.posite) {
				param.posite = param.posite.split('|');
				var $posite = $('.' + param.posite[0]);
				var $event = param.posite[1] || 'click';
				if(!$posite.hasClass('notPartakeAll')) {
					$posite.on($event, function() {
						if(!$(this).hasClass('notPartake')) {
							Change($(this).attr('data-convert-posite') || 0);
						}
					});
				}
			}
			// 前进后退切换
			if(false !== param.nexts) {
				param.nexts = param.nexts.split('|');
				var $nexts = $('.' + param.nexts[0]);
				var $event = param.nexts[1] || 'click';
				if(!$nexts.hasClass('notPartakeAll')) {
					$nexts.on($event, function() {
						if(!$(this).hasClass('notPartake')) {
							Change(+$this.index($this.filter('.' + param.pro)) + +$(this).attr('data-convert-nexts') || 0);
						}
					});
				}
			}
			// 焦点切换
			if(false !== param.trigger) {
				_.each(param.trigger, function(elem, index) {
					elem = _.defaults(elem, {
						pro: '',
						hid: '',
						event: 'click'
					});
					var $tri = $('.' + index);
					if(!$tri.hasClass('notPartakeAll')) {
						$tri.on(elem.event, function() {
							if(!$(this).hasClass('notPartake')) {
								Change($tri.index(this));
							}
						});
					}
				});
			}
			// 切换核心
			function Change(index) {
				var prev = $this.index($this.filter('.' + param.pro));
				index = index % $this.length;
				index = index < 0 ? $this.length + index : index;
				if(!$this.eq(index).hasClass(param.pro)) {
					param.before($this.eq(index), index, prev);
					// 手动切换class
					$this.changeClass(param.pro, param.hid);
					$this.eq(index).changeClass(param.hid, param.pro);
					// 焦点切换class
					_.each(param.trigger, function(elem, i) {
						var $tri = $('.' + i);
						$tri.changeClass(elem.pro, elem.hid);
						$tri.eq(index).changeClass(elem.hid, elem.pro);
					});
					param.after($this.eq(index), index, prev);
				} else if(true === param.infinite) {
					param.before($this.eq(index), -1, prev);
					$this.eq(index).changeClass(param.pro, param.hid);
					_.each(param.trigger, function(elem, i) {
						$('.' + i).eq(index).changeClass(elem.pro, elem.hid);
					});
					param.after($this.eq(index), -1, prev);
				}
			}
			_.each($this, function(elem, index) {
				_.each(param.trigger, function(elem2, i) {
						var $tri = $('.' + i);
						if($(elem).hasClass(param.pro)) {
							$tri.eq(index).changeClass(elem2.hid, elem2.pro);
						} else {
							$tri.eq(index).changeClass(elem2.pro, elem2.hid);
						}
					});
			});
			return true;
		}
	});
})(jQuery);