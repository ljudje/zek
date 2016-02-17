/*
 * jquery-filestyle
 * doc: http://markusslima.github.io/jquery-filestyle/
 * github: https://github.com/markusslima/jquery-filestyle
 *
 * Copyright (c) 2015 Markus Vinicius da Silva Lima
 * Version 1.5.1
 * Licensed under the MIT license.
 */
(function($){var nextId=0;var JFilestyle=function(element,options){this.options=options;this.$elementjFilestyle=[];this.$element=$(element)};JFilestyle.prototype={clear:function(){this.$element.val("");this.$elementjFilestyle.find(":text").val("")},destroy:function(){this.$element.removeAttr("style").removeData("jfilestyle").val("");this.$elementjFilestyle.remove()},disabled:function(value){if(value===true){if(!this.options.disabled){this.$element.attr("disabled","true");this.$elementjFilestyle.find("label").attr("disabled","true");this.options.disabled=true}}else{if(value===false){if(this.options.disabled){this.$element.removeAttr("disabled");this.$elementjFilestyle.find("label").removeAttr("disabled");this.options.disabled=false}}else{return this.options.disabled}}},buttonBefore:function(value){if(value===true){if(!this.options.buttonBefore){this.options.buttonBefore=true;if(this.options.input){this.$elementjFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{if(value===false){if(this.options.buttonBefore){this.options.buttonBefore=false;if(this.options.input){this.$elementjFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{return this.options.buttonBefore}}},input:function(value){if(value===true){if(!this.options.input){this.options.input=true;this.$elementjFilestyle.prepend(this.htmlInput());this.$elementjFilestyle.find(".count-jfilestyle").remove();this.pushNameFiles()}}else{if(value===false){if(this.options.input){this.options.input=false;this.$elementjFilestyle.find(":text").remove();var files=this.pushNameFiles();if(files.length>0){this.$elementjFilestyle.find("label").append(' <span class="count-jfilestyle">'+files.length+"</span>")}}}else{return this.options.input}}},buttonText:function(value){if(value!==undefined){this.options.buttonText=value;this.$elementjFilestyle.find("label span").html(this.options.buttonText)}else{return this.options.buttonText}},inputSize:function(value){if(value!==undefined){this.options.inputSize=value;this.$elementjFilestyle.find(":text").css("width",this.options.inputSize)}else{return this.options.inputSize}},placeholder:function(value){if(value!==undefined){this.options.placeholder=value;this.$elementjFilestyle.find(":text").attr("placeholder",value)}else{return this.options.placeholder}},htmlInput:function(){if(this.options.input){return'<input type="text" style="width:'+this.options.inputSize+'" placeholder="'+this.options.placeholder+'" disabled> '}else{return""}},pushNameFiles:function(){var content="",files=[];if(this.$element[0].files===undefined){files[0]={name:this.$element.value}}else{files=this.$element[0].files}for(var i=0;i<files.length;i++){content+=files[i].name.split("\\").pop()+", "}if(content!==""){this.$elementjFilestyle.find(":text").val(content.replace(/\, $/g,""))}else{this.$elementjFilestyle.find(":text").val("")}return files},constructor:function(){var _self=this,html="",id=_self.$element.attr("id"),$label,files=[];if(id===""||!id){id="jfilestyle-"+nextId;_self.$element.attr({id:id});nextId++}html='<span class="focus-jfilestyle"><label for="'+id+'" '+(_self.options.disabled?'disabled="true"':"")+"><span>"+_self.options.buttonText+"</span></label></span>";if(_self.options.buttonBefore===true){html=html+_self.htmlInput()}else{html=_self.htmlInput()+html}_self.$elementjFilestyle=$('<div class="jfilestyle '+(_self.options.input?"jfilestyle-corner":"")+" "+(this.options.buttonBefore?" jfilestyle-buttonbefore":"")+'">'+html+"</div>");_self.$elementjFilestyle.find(".focus-jfilestyle").attr("tabindex","0").keypress(function(e){if(e.keyCode===13||e.charCode===32){_self.$elementjFilestyle.find("label").click();return false}});_self.$element.css({position:"absolute",clip:"rect(0px 0px 0px 0px)"}).attr("tabindex","-1").after(_self.$elementjFilestyle);if(_self.options.disabled){_self.$element.attr("disabled","true")}_self.$element.change(function(){var files=_self.pushNameFiles();if(_self.options.input==false){if(_self.$elementjFilestyle.find(".count-jfilestyle").length==0){_self.$elementjFilestyle.find("label").append(' <span class="count-jfilestyle">'+files.length+"</span>")}else{if(files.length==0){_self.$elementjFilestyle.find(".count-jfilestyle").remove()}else{_self.$elementjFilestyle.find(".count-jfilestyle").html(files.length)}}}else{_self.$elementjFilestyle.find(".count-jfilestyle").remove()}});if(window.navigator.userAgent.search(/firefox/i)>-1){this.$elementjFilestyle.find("label").click(function(){_self.$element.click();return false})}}};var old=$.fn.jfilestyle;$.fn.jfilestyle=function(option,value){var get="",element=this.each(function(){if($(this).attr("type")==="file"){var $this=$(this),data=$this.data("jfilestyle"),options=$.extend({},$.fn.jfilestyle.defaults,option,typeof option==="object"&&option);if(!data){$this.data("jfilestyle",(data=new JFilestyle(this,options)));data.constructor()}if(typeof option==="string"){get=data[option](value)}}});if(typeof get!==undefined){return get}else{return element}};$.fn.jfilestyle.defaults={buttonText:"Choose file",input:true,disabled:false,buttonBefore:false,inputSize:"200px",placeholder:""};$.fn.jfilestyle.noConflict=function(){$.fn.jfilestyle=old;return this};$(function(){$(".jfilestyle").each(function(){var $this=$(this),options={buttonText:$this.attr("data-buttonText"),input:$this.attr("data-input")==="false"?false:true,disabled:$this.attr("data-disabled")==="true"?true:false,buttonBefore:$this.attr("data-buttonBefore")==="true"?true:false,inputSize:$this.attr("data-inputSize"),placeholder:$this.attr("data-placeholder")};$this.jfilestyle(options)})})})(window.jQuery);

/**
 * jquery.matchHeight-min.js master v0.6.0
 * http://brm.io/jquery-match-height/
 * License: MIT
 */
(function(c){var n=-1,f=-1,g=function(a){return parseFloat(a)||0},r=function(a){var b=null,d=[];c(a).each(function(){var a=c(this),k=a.offset().top-g(a.css("margin-top")),l=0<d.length?d[d.length-1]:null;null===l?d.push(a):1>=Math.floor(Math.abs(b-k))?d[d.length-1]=l.add(a):d.push(a);b=k});return d},p=function(a){var b={byRow:!0,property:"height",target:null,remove:!1};if("object"===typeof a)return c.extend(b,a);"boolean"===typeof a?b.byRow=a:"remove"===a&&(b.remove=!0);return b},b=c.fn.matchHeight=
		function(a){a=p(a);if(a.remove){var e=this;this.css(a.property,"");c.each(b._groups,function(a,b){b.elements=b.elements.not(e)});return this}if(1>=this.length&&!a.target)return this;b._groups.push({elements:this,options:a});b._apply(this,a);return this};b._groups=[];b._throttle=80;b._maintainScroll=!1;b._beforeUpdate=null;b._afterUpdate=null;b._apply=function(a,e){var d=p(e),h=c(a),k=[h],l=c(window).scrollTop(),f=c("html").outerHeight(!0),m=h.parents().filter(":hidden");m.each(function(){var a=c(this);
	a.data("style-cache",a.attr("style"))});m.css("display","block");d.byRow&&!d.target&&(h.each(function(){var a=c(this),b=a.css("display");"inline-block"!==b&&"inline-flex"!==b&&(b="block");a.data("style-cache",a.attr("style"));a.css({display:b,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px"})}),k=r(h),h.each(function(){var a=c(this);a.attr("style",a.data("style-cache")||"")}));c.each(k,function(a,b){var e=c(b),
		f=0;if(d.target)f=d.target.outerHeight(!1);else{if(d.byRow&&1>=e.length){e.css(d.property,"");return}e.each(function(){var a=c(this),b=a.css("display");"inline-block"!==b&&"inline-flex"!==b&&(b="block");b={display:b};b[d.property]="";a.css(b);a.outerHeight(!1)>f&&(f=a.outerHeight(!1));a.css("display","")})}e.each(function(){var a=c(this),b=0;d.target&&a.is(d.target)||("border-box"!==a.css("box-sizing")&&(b+=g(a.css("border-top-width"))+g(a.css("border-bottom-width")),b+=g(a.css("padding-top"))+g(a.css("padding-bottom"))),
		a.css(d.property,f-b+"px"))})});m.each(function(){var a=c(this);a.attr("style",a.data("style-cache")||null)});b._maintainScroll&&c(window).scrollTop(l/f*c("html").outerHeight(!0));return this};b._applyDataApi=function(){var a={};c("[data-match-height], [data-mh]").each(function(){var b=c(this),d=b.attr("data-mh")||b.attr("data-match-height");a[d]=d in a?a[d].add(b):b});c.each(a,function(){this.matchHeight(!0)})};var q=function(a){b._beforeUpdate&&b._beforeUpdate(a,b._groups);c.each(b._groups,function(){b._apply(this.elements,
		this.options)});b._afterUpdate&&b._afterUpdate(a,b._groups)};b._update=function(a,e){if(e&&"resize"===e.type){var d=c(window).width();if(d===n)return;n=d}a?-1===f&&(f=setTimeout(function(){q(e);f=-1},b._throttle)):q(e)};c(b._applyDataApi);c(window).bind("load",function(a){b._update(!1,a)});c(window).bind("resize orientationchange",function(a){b._update(!0,a)})})(jQuery);

var CMS =
{
	sideFields: [
		'hidden',
		'stat_view'
	],
	tableFields: [
		'view',
		'edit',
		'delete',

		'id',
		'title',
		'picture',
		'name',
		'code',
		'description',
		'date_published'
	],

	run: function () {
		CMS.init();
		CMS.initMenu();
	},
	initMenu: function () {

		$('#navbar h1').prepend('<span class="hoe-sidebar-toggle"><a href="#"></a></span>');

		$('.pg-page-list li.nav-header').each (function () {
			licon = $(this).find('i');
			ltext = $(this).text();
			$(this).html ('').append(licon, $('<span class="menu-text">' + ltext + '</span>'));

			$(this).wrapInner('<a href="#"></a>');
			$(this).addClass('hoe-has-menu');
			$(this).append ($('<ul class="hoe-sub-menu"></ul>').append($(this).nextUntil('li.nav-header')));
		});

		$('.hoe-sub-menu li.active').parents('li').addClass('active');

		jQ2('body')
				.wrapInner('<div id="hoeapp-wrapper" class="hoe-hide-lpanel" hoe-device-type="desktop"></div>')
				.attr('hoe-navigation-type', 'vertical')
				.attr('hoe-nav-placement', 'left')
				.attr('theme-layout', 'wide-layout');


		jQ2('#side-bar')
				.parent().wrapInner('<div id="hoeapp-container" hoe-lpanel-effect="shrink"></div>')
				.attr('hoe-position-type', 'absolute')
				.addClass('sbamatch');

		jQ2('#side-bar').attr('id', 'hoe-left-panel');
		jQ2('#content-block').attr('id', 'main-content');

		jQ2('ul.pg-page-list').addClass('panel-list');

		$('#hoeapp-container,.sbamatch').matchHeight(true);


		/*
		 Created by: @Frank_Alfred
		 DEMO URL: http://gohooey.com/demo/sidebar/hoedemo.html
		 */
		HoeDatapp={appinit:function(){HoeDatapp.HandleSidebartoggle(),HoeDatapp.Handlelpanel(),HoeDatapp.Handlelpanelmenu(),HoeDatapp.Handlethemeoption(),HoeDatapp.Handlesidebareffect(),HoeDatapp.Handlesidebarposition(),HoeDatapp.Handlecontentheight(),HoeDatapp.Handlethemecolor(),HoeDatapp.Handlenavigationtype(),HoeDatapp.Handlesidebarside(),HoeDatapp.Handleactivestatemenu(),HoeDatapp.Handlethemelayout(),HoeDatapp.Handlethemebackground()},Handlethemebackground:function(){function e(){$("#theme-color > a.theme-bg").on("click",function(){$("body").attr("theme-bg",$(this).attr("hoe-themebg-type"))})}e()},Handlethemelayout:function(){$("#theme-layout").on("change",function(){"box-layout"==$(this).val()?$("body").attr("theme-layout","box-layout"):$("body").attr("theme-layout","wide-layout")})},Handleactivestatemenu:function(){$(".panel-list li:not('.hoe-has-menu') > a").on("click",function(){("vertical"==$("body").attr("hoe-navigation-type")||"vertical-compact"==$("body").attr("hoe-navigation-type"))&&(1===$(this).closest("li.hoe-has-menu").length?($(this).closest(".panel-list").find("li.active").removeClass("active"),$(this).parent().addClass("active"),$(this).parent().closest(".hoe-has-menu").addClass("active"),$(this).parent("li").closest("li").closest(".hoe-has-menu").addClass("active")):($(this).closest(".panel-list").find("li.active").removeClass("active"),$(this).closest(".panel-list").find("li.opened").removeClass("opened"),$(this).closest(".panel-list").find("ul:visible").slideUp("fast"),$(this).parent().addClass("active")))})},Handlesidebarside:function(){$("#navigation-side").on("change",function(){"rightside"==$(this).val()?($("body").attr("hoe-nav-placement","right"),$("body").attr("hoe-navigation-type","vertical"),$("#hoeapp-wrapper").removeClass("compact-hmenu")):($("body").attr("hoe-nav-placement","left"),$("body").attr("hoe-navigation-type","vertical"),$("#hoeapp-wrapper").removeClass("compact-hmenu"))})},Handlenavigationtype:function(){$("#navigation-type").on("change",function(){"horizontal"==$(this).val()?($("body").attr("hoe-navigation-type","horizontal"),$("#hoeapp-wrapper").removeClass("compact-hmenu"),$("#hoe-header, #hoeapp-container").removeClass("hoe-minimized-lpanel"),$("body").attr("hoe-nav-placement","left"),$("#hoe-header").attr("hoe-color-type","logo-bg7")):"horizontal-compact"==$(this).val()?($("body").attr("hoe-navigation-type","horizontal"),$("#hoeapp-wrapper").addClass("compact-hmenu"),$("#hoe-header, #hoeapp-container").removeClass("hoe-minimized-lpanel"),$("body").attr("hoe-nav-placement","left"),$("#hoe-header").attr("hoe-color-type","logo-bg7")):"vertical-compact"==$(this).val()?($("body").attr("hoe-navigation-type","vertical-compact"),$("#hoeapp-wrapper").removeClass("compact-hmenu"),$("#hoe-header, #hoeapp-container").addClass("hoe-minimized-lpanel"),$("body").attr("hoe-nav-placement","left")):($("body").attr("hoe-navigation-type","vertical"),$("#hoeapp-wrapper").removeClass("compact-hmenu"),$("#hoe-header, #hoeapp-container").removeClass("hoe-minimized-lpanel"),$("body").attr("hoe-nav-placement","left"))})},Handlethemecolor:function(){function e(){$("#theme-color > a.header-bg").on("click",function(){$("#hoe-header > .hoe-right-header").attr("hoe-color-type",$(this).attr("hoe-color-type"))})}function a(){$("#theme-color > a.lpanel-bg").on("click",function(){$("#hoeapp-container").attr("hoe-color-type",$(this).attr("hoe-color-type"))})}function t(){$("#theme-color > a.logo-bg").on("click",function(){$("#hoe-header").attr("hoe-color-type",$(this).attr("hoe-color-type"))})}e(),a(),t()},Handlecontentheight:function(){function e(){var e=$(window).height(),a=$("#navbar").innerHeight(),t=$("#footer").innerHeight(),o=e-a-t-2,n=e-a-2;$("#main-content ").css("min-height",o),$(".inner-left-panel ").css("height",n)}e(),$(window).resize(function(){e()})},Handlesidebarposition:function(){$("#sidebar-position").on("change",function(){"fixed"==$(this).val()?$("#hoe-left-panel,.hoe-left-header").attr("hoe-position-type","fixed"):$("#hoe-left-panel,.hoe-left-header").attr("hoe-position-type","absolute")})},Handlesidebareffect:function(){$("#leftpanel-effect").on("change",function(){"overlay"==$(this).val()?$("#hoe-header, #hoeapp-container").attr("hoe-lpanel-effect","overlay"):"push"==$(this).val()?$("#hoe-header, #hoeapp-container").attr("hoe-lpanel-effect","push"):$("#hoe-header, #hoeapp-container").attr("hoe-lpanel-effect","shrink")})},Handlethemeoption:function(){$(".selector-toggle > a").on("click",function(){$("#styleSelector").toggleClass("open")})},Handlelpanelmenu:function(){$(".hoe-has-menu > a").on("click",function(){var e=$(this).closest(".hoe-minimized-lpanel").length;if(0===e){$(this).parent(".hoe-has-menu").parent("ul").find("ul:visible").slideUp("fast"),$(this).parent(".hoe-has-menu").parent("ul").find(".opened").removeClass("opened");var a=$(this).parent(".hoe-has-menu").find(">.hoe-sub-menu");a.is(":hidden")?(a.slideDown("fast"),$(this).parent(".hoe-has-menu").addClass("opened")):($(this).parent(".hoe-has-menu").parent("ul").find("ul:visible").slideUp("fast"),$(this).parent(".hoe-has-menu").removeClass("opened"))}})},HandleSidebartoggle:function(){$(".hoe-sidebar-toggle a").on("click",function(){"phone"!==$("#hoeapp-wrapper").attr("hoe-device-type")?($("#hoeapp-container").toggleClass("hoe-minimized-lpanel"),$("#hoe-header").toggleClass("hoe-minimized-lpanel"),"vertical-compact"!==$("body").attr("hoe-navigation-type")?$("body").attr("hoe-navigation-type","vertical-compact"):$("body").attr("hoe-navigation-type","vertical")):$("#hoeapp-wrapper").hasClass("hoe-hide-lpanel")?$("#hoeapp-wrapper").removeClass("hoe-hide-lpanel"):$("#hoeapp-wrapper").addClass("hoe-hide-lpanel")})},Handlelpanel:function(){function e(){var e=$(window)[0].innerWidth;e>=768&&1024>=e?($("#hoeapp-wrapper").attr("hoe-device-type","tablet"),$("#hoe-header, #hoeapp-container").addClass("hoe-minimized-lpanel"),$("li.theme-option select").attr("disabled",!1)):768>e?($("#hoeapp-wrapper").attr("hoe-device-type","phone"),$("#hoe-header, #hoeapp-container").removeClass("hoe-minimized-lpanel"),$("li.theme-option select").attr("disabled","disabled")):"vertical-compact"!==$("body").attr("hoe-navigation-type")?($("#hoeapp-wrapper").attr("hoe-device-type","desktop"),$("#hoe-header, #hoeapp-container").removeClass("hoe-minimized-lpanel"),$("li.theme-option select").attr("disabled",!1)):($("#hoeapp-wrapper").attr("hoe-device-type","desktop"),$("#hoe-header, #hoeapp-container").addClass("hoe-minimized-lpanel"),$("li.theme-option select").attr("disabled",!1))}e(),$(window).resize(e)}},HoeDatapp.appinit();

		setTimeout(function () {
			$(window).trigger('resize');
		}, 500);
	},
	initTabs: function (data)
	{
		return false;
		tabs = $.parseJSON(data);
		if (tabs.tabs != undefined) {

			//	Sidebar
			$outputTabs = $('<ul class="nav nav-tabs" role="tablist"></ul>');
			$outputContent = $('<div class="tab-content"></div>');

			$.each(tabs.tabs, function (tabName, tabData) {
				tabid = CMS.slugify(tabName);
				$('<li role="presentation"><a href="#' + tabid + '" role="tab" data-toggle="tab">' + tabName + '</a></li>').appendTo ($outputTabs)

				$tabContent = $('<div role="tabpanel" class="tab-pane" id="' + tabid + '"></div>');

				$.each(tabData.fields, function (i, tab) {
					if (tab == 'picture') {
						$('[data-toggle-name="picture_edit_action"]').parents('.control-group').appendTo ($tabContent);
					} else {
						$('[data-field-name="' + tab + '"]').parents('.control-group').appendTo ($tabContent);
					}
				});

				$tabContent.appendTo ($outputContent);
			});

			$outputTabs.find('li:eq(0)').addClass('active');
			$outputContent.find('.tab-pane:eq(0)').addClass('active');

			//if (tabs.options.shown_below != undefined && tabs.options.shown_below.length !== false) {
			//	$('[data-field-name="' + tabs.options.shown_below + '"]').parents('.control-group').after($('<div></div>').append ($outputTabs, $outputContent));
			//} else {
				$('.pgui-edit-form form fieldset').after($('<div></div>').append ($outputTabs, $outputContent));
			//}
		}
	},
	init: function () {
		//  Links to list
		if (!$('table.pgui-grid').is('*')) {
			$('.page-header h1').wrapInner ('<a href="' + location.pathname + '" class="kre3-page-title">');
		}


		//	File manager
		if ($('#kre3-filemanager').is('*')) {
			$('.pagination').hide();
			$('#kre3-filemanager').html('<iframe src="_custom/lib/elFinder/elfinder.html"></iframe>');
		}

		$('table.pgui-grid').wrap('<div class="table-responsive"></div>');

		//	Tables
		//var actionscols = parseInt($('table.pgui-grid .header th:eq(0)').attr('colspan'));
		//
		//$('table.pgui-grid tbody tr.pg-row').each(function () {
		//	$('td', this).each(function (index) {
		//		fpos = $.inArray($(this).data('columnName'), CMS.tableFields);
		//		if (fpos < 0) {
		//			$(this).addClass('table-mobile-hidden');
		//			thpos = index - actionscols + 1;
		//
		//			//  TODO: expand btn
		//			$('table.pgui-grid .header th').eq(thpos).addClass('table-mobile-hidden');
		//		}
		//	});
		//});



		//$('table.pgui-grid tr.header th').each(function (i, thData) {
		//	$('table.pgui-grid tbody > tr.pg-row td').eq(i).attr('data-title', $(thData).text().trim());
		//});

		//  File upload
		$(":file").jfilestyle({
			buttonText: "Izberi datoteko ...",
			buttonBefore: true,
		});
	},
	slugify: function (text)
	{
		return text.toString().toLowerCase()
			.replace(/\s+/g, '-')           // Replace spaces with -
			.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
			.replace(/\-\-+/g, '-')         // Replace multiple - with single -
			.replace(/^-+/, '')             // Trim - from start of text
			.replace(/-+$/, '');            // Trim - from end of text
	}
}
$(document).ready(CMS.run);
