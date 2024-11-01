<?php
/**
 * 推荐发送JS代码模版
 * @createtime 2012-09-26
 */

! defined ( 'ALIYUNREC' ) && exit ( 'Forbidden' );

return <<<EOT
<script>
(function () {
   if (typeof(aliyun_recommend_opts) == "object") {
      var key = [];
      for (var i in aliyun_recommend_opts) {
         key.push(i);
      }
      var img = new Image();
      var d = new Date().getTime();
      img.onload = function () {
         img = null;
      }
      img.onerror = function () {
         img = null;
      }
      var Src = "http://rc.so.cnzz.net/stat.gif?url=" + encodeURIComponent(window.location.href) + "&ts=" + d;
      for (var i = 0; i < key.length; i++) {
		 if (key[i] == 'url') continue;
         if (aliyun_recommend_opts[key[i]]) {
            Src += "&" + key[i] + "=" + encodeURIComponent(aliyun_recommend_opts[key[i]]);
         }
      }
      if (key[0]) {
         aliyun_recommend_opts = "";
         img.src = Src;
      }
   }
})();
</script>
EOT;
