<?php
/**
 * 推荐请求JS代码模版
 * @createtime 2012-09-26
 */

! defined ( 'ALIYUNREC' ) && exit ( 'Forbidden' );

return <<<EOT
var aliyun_recommend_apps_request = {
	init : function () {
		var extraParams = "&v=<<version>>";
		var url = "";
		extraParams += this.getTitle() + this.getUrl() + this.getKeyword() + this.getCharset();
		for(var i=0;i<aliyun_recommend_apps.length;i++) {
			url = aliyun_recommend_apps[i] + extraParams;
			this.loadScript(url);
		}
	},
	getTitle : function () {
		var title = document.title;
		return (title) ? ('&title=' + encodeURIComponent(title)) : '';
	},
	getUrl : function () {
		var url = window.location.href;
		return (url) ? ('&surl=' + encodeURIComponent(url)) : '';
	},
	getCharset : function () {
		var charset = (document.charset) ? document.charset : document.characterSet;
		return '&charset=' + charset;
	},
	getKeyword : function () {
		referer = document.referrer ? document.referrer : '';
		if (!referer) return '';
		var keyword = "";
		keyword = this.getQueryString("wd", referer);
		if (keyword == "") {
			keyword = this.getQueryString("q", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("word", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("query", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("search", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("keyword", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("kw", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("w", referer);
		}
		if (keyword == "") {
			keyword = this.getQueryString("p", referer);
		}
		return (keyword) ? ('&keyword=' + encodeURIComponent(keyword)) : '';
	},
	getQueryString : function (name, urls) {
		var regString = "(^|\\\\?|&)"+name+"=([^&]*)(\\\\s|&|$)";
		var reg = new RegExp(regString,"i");
		if (reg.test(urls))
			return RegExp.$2.replace(/\+/g, " ");
		return "";
	},
	loadScript : function (url) {
		url = url + "&ts=" + new Date().getTime();
		var s = document.createElement("script");
		s.charset = "utf-8";
		s.async = 1;
		s.src = url;
		document.body.insertBefore(s, document.body.firstChild);
	}
};
aliyun_recommend_apps_request.init();
EOT;
