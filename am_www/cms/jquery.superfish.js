/*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */

;(function($){
	$.fn.superfish = function(op){

		var sf = $.fn.superfish,
			c = sf.c,
			$arrow = $(['<span class="',c.arrowClass,'"> &#187;</span>'].join('')),
			over = function(){
				var $$ = $(this), menu = getMenu($$);
				clearTimeout(menu.sfTimer);
				$$.showSuperfishUl().siblings().hideSuperfishUl();
			},
			out = function(){
				var $$ = $(this), menu = getMenu($$), o = sf.op;
				clearTimeout(menu.sfTimer);
				menu.sfTimer=setTimeout(function(){
					o.retainPath=($.inArray($$[0],o.$path)>-1);
					$$.hideSuperfishUl();
					if (o.$path.length && $$.parents(['li.',o.hoverClass].join('')).length<1){over.call(o.$path);}
				},o.delay);	
			},
			getMenu = function($menu){
				var menu = $menu.parents(['ul.',c.menuClass,':first'].join(''))[0];
				sf.op = sf.o[menu.serial];
				return menu;
			},
			addArrow = function($a){ $a.addClass(c.anchorClass).append($arrow.clone()); };
			
		return this.each(function() {
			var s = this.serial = sf.o.length;
			var o = $.extend({},sf.defaults,op);
			o.$path = $('li.'+o.pathClass,this).slice(0,o.pathLevels).each(function(){
				$(this).addClass([o.hoverClass,c.bcClass].join(' '))
					.filter('li:has(ul)').removeClass(o.pathClass);
			});
			sf.o[s] = sf.op = o;
			
			$('li:has(ul)',this)[($.fn.hoverIntent && !o.disableHI) ? 'hoverIntent' : 'hover'](over,out).each(function() {
				if (o.autoArrows) addArrow( $('>a:first-child',this) );
			})
			.not('.'+c.bcClass)
				.hideSuperfishUl();
			
			var $a = $('a',this);
			$a.each(function(i){
				var $li = $a.eq(i).parents('li');
				$a.eq(i).focus(function(){over.call($li);}).blur(function(){out.call($li);});
			});
			o.onInit.call(this);
			
		}).each(function() {
			var menuClasses = [c.menuClass];
			if (sf.op.dropShadows  && !($.browser.msie && $.browser.version < 7)) menuClasses.push(c.shadowClass);
			$(this).addClass(menuClasses.join(' '));
		});
	};

	var sf = $.fn.superfish;
	sf.o = [];
	sf.op = {};
	sf.IE7fix = function(){
		var o = sf.op;
		if ($.browser.msie && $.browser.version > 6 && o.dropShadows && o.animation.opacity!=undefined)
			this.toggleClass(sf.c.shadowClass+'-off');
		};
	sf.c = {
		bcClass     : 'sf-breadcrumb',
		menuClass   : 'sf-js-enabled',
		anchorClass : 'sf-with-ul',
		arrowClass  : 'sf-sub-indicator',
		shadowClass : 'sf-shadow'
	};
	sf.defaults = {
		hoverClass	: 'sfHover',
		pathClass	: 'overideThisToUse',
		pathLevels	: 1,
		delay		: 800,
		animation	: {opacity:'show'},
		speed		: 'normal',
		autoArrows	: true,
		dropShadows : true,
		disableHI	: false,		// true disables hoverIntent detection
		onInit		: function(){}, // callback functions
		onBeforeShow: function(){},
		onShow		: function(){},
		onHide		: function(){}
	};
	$.fn.extend({
		hideSuperfishUl : function(){
			var o = sf.op,
				not = (o.retainPath===true) ? o.$path : '';
			o.retainPath = false;
			var $ul = $(['li.',o.hoverClass].join(''),this).add(this).not(not).removeClass(o.hoverClass)
					.find('>ul').hide().css('visibility','hidden');
			o.onHide.call($ul);
			return this;
		},
		showSuperfishUl : function(){
			var o = sf.op,
				sh = sf.c.shadowClass+'-off',
				$ul = this.addClass(o.hoverClass)
					.find('>ul:hidden').css('visibility','visible');
			sf.IE7fix.call($ul);
			o.onBeforeShow.call($ul);
			$ul.animate(o.animation,o.speed,function(){ sf.IE7fix.call($ul); o.onShow.call($ul); });
			return this;
		}
	});

})(jQuery);


/******** CSS********/
/*SUPER FISH MENU*/

/*** ESSENTIAL STYLES ***/
.sf-menu, .sf-menu * {margin:0;padding:0;list-style:none;}
.sf-menu {line-height:1.0;}
.sf-menu ul {position:absolute;top:-999em;width:10em; 
/* left offset of submenus need to match (see below) */
}
.sf-menu ul li {width:100%;}
.sf-menu li:hover {visibility:inherit; /* fixes IE7 'sticky bug' */}
.sf-menu li {float:left;position:relative;}
.sf-menu a {display:block;position:relative;}
.sf-menu li:hover ul,.sf-menu li.sfHover ul {left:0;top:2.5em; /* match p ul list item height */z-index:99;}
ul.sf-menu li:hover li ul,ul.sf-menu li.sfHover li ul {top:-999em;}
ul.sf-menu li li:hover ul,ul.sf-menu li li.sfHover ul {left:10em; /* atch ul width */top:0;}
ul.sf-menu li li:hover li ul,ul.sf-menu li li.sfHover li ul {top:-999em;}
ul.sf-menu li li li:hover ul,ul.sf-menu li li li.sfHover ul {left:10em; /* match ul width */top:0;}

/*** DEMO SKIN ***/
.sf-menu {float:left;margin-bottom:1em;}
.sf-menu a {border-left:1px solid #fff;border-top:1px solid #CFDEFF;padding: .75em 1em;text-decoration:none;}
.sf-menu a, .sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/color:#13a;}
.sf-menu li {background:#BDD2FF;}
.sf-menu li li {background:#AABDE6;}
.sf-menu li li li {background:#9AAEDB;}
.sf-menu li:hover, .sf-menu li.sfHover,.sf-menu a:focus, .sf-menu :hover, .sf-menu a:active {background:#CFDEFF;outline:0;}

/*** arrows **/
.sf-menu a.sf-with-ul {padding-right: 2.25em;min-width:1px; /* trigger IE7 hasLayout so spans position accurately */}
.sf-sub-indicator {position:absolute;display:block;right:.75em;top:1.05em; /* IE6 only */
width:10px;height:10px;text-indent: -999em;overflow:hidden;background:url('../images/arrows-ffffff.png') no-repeat -10px -100px; /* 8-bit indexed alpha png. IE6 gets solid image only */}
a > .sf-sub-indicator {  /* give all except IE6 the correct values */top:.8em;background-position: 0 -100px; /* use translucent arrow for modern browsers*/}
/* apply hovers to modern browsers */
a:focus > .sf-sub-indicator,a:hover > .sf-sub-indicator,a:active > .sf-sub-indicator,li:hover > a > .sf-sub-indicator,li.sfHover > a > .sf-sub-indicator {background-position: -10px -100px; /* arrow hovers for modern browsers*/}

/* point right for anchors in subs */
.sf-menu ul .sf-sub-indicator { background-position:  -10px 0; }
.sf-menu ul a > .sf-sub-indicator { background-position:  0 0; }
/* apply hovers to modern browsers */
.sf-menu ul a:focus > .sf-sub-indicator,.sf-menu ul a:hover > .sf-sub-indicator,.sf-menu ul a:active > .sf-sub-indicator,.sf-menu ul li:hover > a > .sf-sub-indicator,.sf-menu ul li.sfHover > a > .sf-sub-indicator {background-position: -10px 0; /* arrow hovers for modern browsers*/}

/*** shadows for all but IE6 ***/
.sf-shadow ul {background:url('../images/shadow.png') no-repeat bottom right;padding: 0 8px 9px 0;-moz-border-radius-bottomleft: 17px;-moz-border-radius-topright: 17px;-webkit-border-top-right-radius: 17px;-webkit-border-bottom-left-radius: 17px;}
.sf-shadow ul.sf-shadow-off {background: transparent;}

/*** adding the class sf-navbar in addition to sf-menu creates an all-horizontal nav-bar menu ***/
.sf-navbar {background:#BDD2FF;height:2.5em;padding-bottom:2.5em;position:relative;}
.sf-navbar li {background:#AABDE6;position:static;}
.sf-navbar a {border-top:none;}
.sf-navbar li ul {width:44em; /*IE6 soils itself without this*/}
.sf-navbar li li {background:#BDD2FF;position:relative;}
.sf-navbar li li ul {width:13em;}
.sf-navbar li li li {width:100%;}
.sf-navbar ul li {width:auto;float:left;}
.sf-navbar a, .sf-navbar a:visited {border:none;}
.sf-navbar li.current {background:#BDD2FF;}
.sf-navbar li:hover,.sf-navbar li.sfHover,.sf-navbar li li.current,.sf-navbar a:focus, .sf-navbar a:hover, .sf-navbar a:active {background:#BDD2FF;}
.sf-navbar ul li:hover,.sf-navbar ul li.sfHover,ul.sf-navbar ul li:hover li,ul.sf-navbar ul li.sfHover li,.sf-navbar ul a:focus, .sf-navbar ul a:hover, .sf-navbar ul a:active {background:#D1DFFF;}
ul.sf-navbar li li li:hover,ul.sf-navbar li li li.sfHover,.sf-navbar li li.current li.current,.sf-navbar ul li li a:focus, .sf-navbar ul li li a:hover, .sf-navbar ul li li a:active {background:#E6EEFF;}
ul.sf-navbar .current ul,ul.sf-navbar ul li:hover ul,ul.sf-navbar ul li.sfHover ul {left:0;top:2.5em; /* match top ul list item height */}
ul.sf-navbar .current ul ul {top: -999em;}

.sf-navbar li li.current > a {font-weight:bold;}

/*** point all arrows down ***/
/* point right for anchors in subs */
.sf-navbar ul .sf-sub-indicator { background-position: -10px -100px; }
.sf-navbar ul a > .sf-sub-indicator { background-position: 0 -100px; }
/* apply hovers to modern browsers */
.sf-navbar ul a:focus > .sf-sub-indicator,.sf-navbar ul a:hover > .sf-sub-indicator,.sf-navbar ul a:active > .sf-sub-indicator,.sf-navbar ul li:hover > a > .sf-sub-indicator,.sf-navbar ul li.sfHover > a > .sf-sub-indicator {background-position: -10px -100px; /* arrow hovers for modern browsers*/}

/*** remove shadow on first submenu ***/
.sf-navbar > li > ul {background: transparent;padding: 0;-moz-border-radius-bottomleft: 0;-moz-border-radius-topright: 0;-webkit-border-top-right-radius: 0;-webkit-border-bottom-left-radius: 0;}

/*** adding sf-vertical in addition to sf-menu creates a vertical menu ***/
.sf-vertical, .sf-vertical li {
	width:	10em;
}
/* this lacks ul at the start of the selector, so the styles from the main CSS file override it where needed */
.sf-vertical li:hover ul,
.sf-vertical li.sfHover ul {
	left:	10em; /* match ul width */
	top:	0;
}

/*** alter arrow directions ***/
.sf-vertical .sf-sub-indicator { background-position: -10px 0; } /* IE6 gets solid image only */
.sf-vertical a > .sf-sub-indicator { background-position: 0 0; } /* use translucent arrow for modern browsers*/

/* hover arrow direction for modern browsers*/
.sf-vertical a:focus > .sf-sub-indicator,
.sf-vertical a:hover > .sf-sub-indicator,
.sf-vertical a:active > .sf-sub-indicator,
.sf-vertical li:hover > a > .sf-sub-indicator,
.sf-vertical li.sfHover > a > .sf-sub-indicator {
	background-position: -10px 0; /* arrow hovers for modern browsers*/
}