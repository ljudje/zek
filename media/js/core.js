/*!
 * jquery.matchHeight-min.js v0.5.2
 * http://brm.io/jquery-match-height/
 * License: MIT
 */
(function(e){var t=-1,n=-1;var r=function(t){var n=1,r=e(t),s=null,o=[];r.each(function(){var t=e(this),r=t.offset().top-i(t.css("margin-top")),u=o.length>0?o[o.length-1]:null;if(u===null){o.push(t)}else{if(Math.floor(Math.abs(s-r))<=n){o[o.length-1]=u.add(t)}else{o.push(t)}}s=r});return o};var i=function(e){return parseFloat(e)||0};e.fn.matchHeight=function(t){if(t==="remove"){var n=this;this.css("height","");e.each(e.fn.matchHeight._groups,function(e,t){t.elements=t.elements.not(n)});return this}if(this.length<=1)return this;t=typeof t!=="undefined"?t:true;e.fn.matchHeight._groups.push({elements:this,byRow:t});e.fn.matchHeight._apply(this,t);return this};e.fn.matchHeight._groups=[];e.fn.matchHeight._throttle=80;e.fn.matchHeight._maintainScroll=false;e.fn.matchHeight._apply=function(t,n){var s=e(t),o=[s];var u=e(window).scrollTop(),a=e("html").outerHeight(true);var f=s.parents().filter(":hidden");f.css("display","block");if(n){s.each(function(){var t=e(this),n=t.css("display")==="inline-block"?"inline-block":"block";t.data("style-cache",t.attr("style"));t.css({display:n,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px"})});o=r(s);s.each(function(){var t=e(this);t.attr("style",t.data("style-cache")||"").css("height","")})}e.each(o,function(t,r){var s=e(r),o=0;if(n&&s.length<=1)return;s.each(function(){var t=e(this),n=t.css("display")==="inline-block"?"inline-block":"block";t.css({display:n,height:""});if(t.outerHeight(false)>o)o=t.outerHeight(false);t.css("display","")});s.each(function(){var t=e(this),n=0;if(t.css("box-sizing")!=="border-box"){n+=i(t.css("border-top-width"))+i(t.css("border-bottom-width"));n+=i(t.css("padding-top"))+i(t.css("padding-bottom"))}t.css("height",o-n)})});f.css("display","");if(e.fn.matchHeight._maintainScroll)e(window).scrollTop(u/a*e("html").outerHeight(true));return this};e.fn.matchHeight._applyDataApi=function(){var t={};e("[data-match-height], [data-mh]").each(function(){var n=e(this),r=n.attr("data-match-height")||n.attr("data-mh");if(r in t){t[r]=t[r].add(n)}else{t[r]=n}});e.each(t,function(){this.matchHeight(true)})};var s=function(){e.each(e.fn.matchHeight._groups,function(){e.fn.matchHeight._apply(this.elements,this.byRow)})};e.fn.matchHeight._update=function(r,i){if(i&&i.type==="resize"){var o=e(window).width();if(o===t)return;t=o}if(!r){s()}else if(n===-1){n=setTimeout(function(){s();n=-1},e.fn.matchHeight._throttle)}};e(e.fn.matchHeight._applyDataApi);e(window).bind("load",function(t){e.fn.matchHeight._update()});e(window).bind("resize orientationchange",function(t){e.fn.matchHeight._update(true,t)})})(jQuery);

/* Start Up */
function StartUp(runnable)
{
	$(document).ready(runnable.run);
}

var Core =
{
	currenturl: null,

	run: function ()
	{
		Core.currenturl = (location.href.indexOf ('#') > -1) ? location.href.substring (0, location.href.indexOf ('#')) : location.href;

		Core.initHistory();
	},
	initHistory: function () {
		var History = window.History; // Note: We are using a capital H instead of a lower h
		if ( !History.enabled ) {
			// History.js is disabled for this browser.
			return false;
		}

		// Bind to StateChange Event
		History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
			var State = History.getState(); // Note: We are using History.getState() instead of event.state
			//History.log(State.data, State.title, State.url);
		});
	}
}
StartUp(Core);

var GeneralStartup =
{
	run: function()
	{
		$("table tr:even").addClass("odd");
		$('img[align=right]').css('float', 'right');
		$('img[align=left]').css('float', 'left');
	}
}
StartUp(GeneralStartup);

var ExternalLinks =
{
	run: function()
	{
		$('a[href^="http:"]:not(".internal")').bind('click', ExternalLinks.click);
		$('a[href^="https:"]:not(".internal")').bind('click', ExternalLinks.click);
		$('a[rel="external"]').bind('click', ExternalLinks.click);
	},
	click: function(event)
	{
		open(this.href);
		return false;
	}
}
StartUp(ExternalLinks);
