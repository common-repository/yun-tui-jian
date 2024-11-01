<?php
/**
 * 阿里云推荐业务服务
 * @createtime 2012-09-27
 */

! defined ( 'ALIYUNREC' ) && exit ( 'Forbidden' );
require_once ALIYUNREC . '/common/aliyunrec.common.config.service.php';
class AliyunRec_Common_General_Service {
	
	/**
	 * 获取各应用的JS地址
	 * @return string
	 */
	function getApplicationUrls() {
		$applicationIds = AliyunRec_Common_Config_Service::getConfig ( 'common.application' );
		if (! is_array ( $applicationIds ) || count ( $applicationIds ) < 1)
			return '';
		$applicationIds = AliyunRec_Common_General_Service::builApplicationIds ( $applicationIds );
		if (count ( $applicationIds ) < 1)
			return '';
		$urls = AliyunRec_Common_General_Service::buildApplicationUrls ( $applicationIds );
		return AliyunRec_Common_General_Service::buildApplicationJsString ( $urls );
	}
	
	/**
	 * 获取内容页的标签
	 * @param string $title 标题
	 * @param string $thumb 缩略图
	 * @param string $url 文章地址
	 * @param string $tags 文章标签
	 * @return string
	 */
	function getRecommendOptions($title, $thumb = '', $url = '', $tags = '') {
		$template = AliyunRec_Common_Config_Service::getConfig ( 'template.option' );
		if (! $template)
			return '';
		list ( $title, $thumb, $url, $tags ) = array (AliyunRec_Common_General_Service::filterString ( $title ), AliyunRec_Common_General_Service::filterString ( $thumb ), AliyunRec_Common_General_Service::filterString ( $url ), AliyunRec_Common_General_Service::filterString ( $tags ) );
		$params = array ();
		$url && $params [] = "'url':'$url'";
		$title && $params [] = "'title':'$title'";
		$thumb && $params [] = "'thumb':'$thumb'";
		$tags && $params [] = "'tags':'$tags'";
		return str_replace ( '<<content>>', implode ( ",\r\n", $params ), $template );
	}
	
	/**
	 * 私有方法
	 * 获取id
	 * @param array $applicationIds ID信息
	 * @return array
	 */
	function builApplicationIds($applicationIds) {
		list ( $fixedIds, $floatIds ) = array (AliyunRec_Common_General_Service::buildFixedIds ( $applicationIds ), AliyunRec_Common_General_Service::buildFloatIds ( $applicationIds ) );
		return array_merge ( $fixedIds, $floatIds );
	}
	
	/**
	 * 私有方法
	 * 获取固定位id
	 * @param array $applicationIds ID信息
	 * @return array
	 */
	function buildFixedIds($applicationIds) {
		if (! isset ( $applicationIds ['fixed'] ) || $applicationIds ['fixed'] == '')
			return array ();
		$tmp = explode ( ',', $applicationIds ['fixed'] );
		return (is_array ( $tmp ) && count ( $tmp ) > 0 && intval ( $tmp [0] ) >= 0) ? array (intval ( $tmp [0] ) ) : array ();
	}
	
	/**
	 * 私有方法
	 * 获取浮窗id
	 * @param array $applicationIds ID信息
	 * @return array
	 */
	function buildFloatIds($applicationIds) {
		$ids = array ();
		if (! isset ( $applicationIds ['float'] ) || $applicationIds ['float'] == '')
			return $ids;
		$tmp = explode ( ',', $applicationIds ['float'] );
		if (! is_array ( $tmp ))
			return $ids;
		foreach ( $tmp as $value ) {
			$value = intval ( $value );
			if ($value < 1)
				continue;
			$ids [] = $value;
		}
		return $ids;
	}
	
	/**
	 * 私有方法
	 * 组装应用地址
	 * @param array $applicationIds 应用ID等信息
	 * @return array
	 */
	function buildApplicationUrls($applicationIds) {
		list ( $urls, $urlTemplate ) = array (array (), AliyunRec_Common_Config_Service::getUrlTemplate () );
		foreach ( $applicationIds as $applicationId ) {
			$urls [] = $urlTemplate . $applicationId;
		}
		return $urls;
	}
	
	/**
	 * 私有方法
	 * 组装应用JS
	 * @param array $applicationUrls 应用地址数组
	 * @return string
	 */
	function buildApplicationJsString($applicationUrls) {
		$fixedRecommendId = AliyunRec_Common_Config_Service::getFixedApplicationIds ();
		$jsString = "<script>var aliyun_recommend_apps = new Array();";
		$jsString .= "if(document.getElementById('$fixedRecommendId')){";
		foreach ( $applicationUrls as $url ) {
			$jsString .= "aliyun_recommend_apps.push('$url');";
		}
		$jsString .= '}';
		$jsString .= str_replace ( '<<version>>', ALIYUNREC_VERSION, AliyunRec_Common_Config_Service::getConfig ( 'template.request' ) );
		return $jsString . '</script>';
	}
	
	/**
	 * 私有方法
	 * 过滤引号
	 * @param string $string 字符串
	 * @return string
	 */
	function filterString($string) {
		return str_replace ( array ('\'', '"' ), '', trim ( $string ) );
	}
}