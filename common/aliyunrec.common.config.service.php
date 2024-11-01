<?php
/**
 * 读取配置信息
 * @createtime 2012-09-27
 */

! defined ( 'ALIYUNREC' ) && exit ( 'Forbidden' );

class AliyunRec_Common_Config_Service {
	
	/**
	 * 获取JS地址模版
	 * @return string
	 */
	function getUrlTemplate() {
		return ALIYUNREC_DOMAIN;
	}
	
	/**
	 * 获取固定位的ID名称
	 * @return array()
	 */
	function getFixedApplicationIds() {
		$applications = AliyunRec_Common_Config_Service::getConfig ( 'common.application' );
		$fixedRecommendIds = isset ( $applications ['fixed'] ) ? $applications ['fixed'] : '';
		$tmp = explode ( ',', $fixedRecommendIds );
		$fixedId = (is_array ( $tmp ) && count ( $tmp ) > 0) ? intval ( $tmp [0] ) : 0;
		return 'aliyun_cnzz_tui_' . $fixedId;
	}
	
	/**
	 * 获取config目录下配置文件内容
	 * @param string $configName 文件名称 如template.send
	 * @return mixed
	 */
	function getConfig($configName) {
		$configName = strtolower ( $configName );
		if (str_replace ( array ('://', "\0", '..' ), '', $configName ) != $configName)
			return false;
		$filePath = ALIYUNREC . '/config/' . $configName . '.php';
		if (! file_exists ( $filePath ))
			return false;
		return include $filePath;
	}
}