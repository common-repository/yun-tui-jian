<?php
/*
Plugin Name: 云推荐
Plugin URI: http://plugin.tui.cnzz.com
Description: 云推荐是CNZZ旗下一款面向站长的高速、稳定、易用、免费的站内个性化智能推荐系统。云推荐基于阿里云先进的云计算系统，支持海量网页数据和用户行为数据的分析计算，提供个性化的样式设定功能，同时，为站长提供多角度的数据报表，使站长有的放矢的运营网站。
Version: 1.0
Author: 云推荐
Author URI: http://plugin.tui.cnzz.com
*/

/**
 * entry of aliyun recommendation
 * @createtime 2012-10-11
 */
define ( 'ALIYUNREC', dirname ( __FILE__ ) );
require_once ALIYUNREC . '/common/aliyunrec.common.proxy.service.php';

class AliyunRec_Guide {
	
	/**
	 * init
	 * @access public
	 * @return boolean
	 */
	function init() {
		add_action ( 'wp_head', array ('AliyunRec_Guide', 'addHeadTags' ), 100 );
		add_filter ( 'the_content', array ('AliyunRec_Guide', 'addContentContainer' ), 100 );
		add_action ( 'get_footer', array ('AliyunRec_Guide', 'initFooter' ), 100 );
		add_action ( 'admin_menu', array ('AliyunRec_Guide', 'addAdminMenu' ), 100 );
		
		register_activation_hook ( __FILE__, array ('AliyunRec_Guide', 'activePlugin' ) );
		register_deactivation_hook ( __FILE__, array ('AliyunRec_Guide', 'deactivePlugin' ) );
		return true;
	}
	
	/**
	 * add content tags to head
	 * @access public
	 * @return null
	 */
	function addHeadTags() {
		global $post;
		if (! AliyunRec_Guide::getAliyunRecState () || ! isset ( $post ) || ! isset ( $post->post_title ) || ! AliyunRec_Guide::checkDisplayState ( $post ))
			return '';
		list ( $title, $thumb ) = array (isset ( $post->post_title ) ? $post->post_title : '', AliyunRec_Guide::getPostThumb ( $post->ID ) );
		list ( $url, $tags ) = array ((function_exists ( 'esc_url' ) ? esc_url ( get_permalink () ) : get_permalink ()), AliyunRec_Guide::getPostTags ( $post->ID ) );
		echo AliyunRec_Common_Proxy_Service::getRecommendOptions ( $title, $thumb, $url, $tags );
	}
	
	/**
	 * add container to content bottom
	 * @access public
	 * @return string
	 */
	function addContentContainer($content) {
		global $post;
		if (! AliyunRec_Guide::getAliyunRecState () || ! AliyunRec_Guide::checkDisplayState ( $post ))
			return $content;
		$aliyunRecommendDcId = AliyunRec_Common_Proxy_Service::getFixedApplicationIds ();
		return $content . "<div id=\"$aliyunRecommendDcId\"></div>";
	}
	
	/**
	 * init footer
	 * @access public
	 * @return boolean
	 */
	function initFooter($name) {
		if (! AliyunRec_Guide::getAliyunRecState ())
			return true;
		$templates = array ();
		$templates [] = isset ( $name ) ? "footer-{$name}.php" : 'footer.php';
		$templatePath = function_exists ( 'locate_template' ) ? locate_template ( $templates ) : AliyunRec_Guide::locateTemplate ( $templates );
		(AliyunRec_Guide::checkHookInTemplate ( $templatePath, 'wp_footer' )) ? add_action ( 'wp_footer', array ('AliyunRec_Guide', 'modifyFooter' ), 100 ) : AliyunRec_Guide::modifyFooter ();
		return true;
	}
	
	/**
	 * add send js and applications js to the footer of page
	 * @access public
	 * @return null
	 */
	function modifyFooter() {
		list ( $aliyunRecommendSend, $aliyunRecommendApps ) = AliyunRec_Guide::getAliyunRecState () ? array (AliyunRec_Common_Proxy_Service::getSendTemplate (), AliyunRec_Common_Proxy_Service::getApplicationUrls () ) : array ('', '' );
		echo $aliyunRecommendSend . $aliyunRecommendApps;
	}
	
	/**
	 * add admin menu under option
	 * @access public
	 * @return boolean
	 */
	function addAdminMenu() {
		$language = AliyunRec_Guide::getLocalLanguage ();
		add_options_page ( $language ['product_name'], $language ['product_name'], 'activate_plugins', basename ( __FILE__ ), array ('AliyunRec_Guide', 'manageSetting' ) );
		return true;
	}
	
	/**
	 * settings of aliyun recommendation
	 * @access public
	 * @return null
	 */
	function manageSetting() {
		if (! current_user_can ( 'activate_plugins' ))
			wp_die ( __ ( 'You do not have sufficient permissions to manage options for this site.' ) );
		if (isset ( $_POST ['state'] )) {
			$state = intval ( $_POST ['state'] ) > 0 ? 1 : 0;
			update_option ( ALIYUNREC_SETTING_NAME, $state );
		}
		list ( $aliyunRecState, $aliyunRecLanguage ) = array (AliyunRec_Guide::getAliyunRecState (), AliyunRec_Guide::getLocalLanguage () );
		$openCheck = $closeCheck = '';
		$aliyunRecState ? $openCheck = 'checked' : $closeCheck = 'checked';
		include ALIYUNREC . '/template/aliyunrec.htm';
	}
	
	/**
	 * actions done when active the plugin
	 * @access public
	 * @return null
	 */
	function activePlugin() {
		update_option ( ALIYUNREC_SETTING_NAME, 1 );
	}
	
	/**
	 * actions done when deactive the plugin
	 * @access public
	 * @return null
	 */
	function deactivePlugin() {
		delete_option ( ALIYUNREC_SETTING_NAME );
	}
	
	/**
	 * check whether a template contains the certain hook function
	 * @access private
	 * @return boolean
	 */
	function checkHookInTemplate($template, $hook) {
		if (! $template || ! file_exists ( $template ) || ! $hook)
			return false;
		$content = file_get_contents ( $template );
		return $content === false ? false : (preg_match ( "|$hook\s*\(\)|i", $content ) ? true : false);
	}
	
	/**
	 * get and build post thumb
	 * @access private
	 * @param int $postId
	 * @return string
	 */
	function getPostThumb($postId) {
		$path = function_exists ( 'get_post_thumbnail_id' ) ? get_post_thumbnail_id ( $postId ) : get_post_meta ( $postId, '_thumbnail_id', true );
		$thumb = wp_get_attachment_image_src ( $path, 'full' );
		return (is_array ( $thumb ) && $thumb [0]) ? $thumb [0] : '';
	}
	
	/**
	 * get and build post tages
	 * @access private
	 * @param int $postId
	 * @return string
	 */
	function getPostTags($postId) {
		$terms = get_the_terms ( $postId, 'post_tag' );
		if (is_wp_error ( $terms ) || empty ( $terms ))
			return '';
		$tags = '';
		foreach ( $terms as $term ) {
			$tags .= ($tags ? ',' : '') . $term->name;
		}
		return $tags;
	}
	
	/**
	 * get language package
	 * @access private
	 * @return array
	 */
	function getLocalLanguage() {
		return AliyunRec_Common_Proxy_Service::getLocalLanguage ();
	}
	
	/**
	 * get state of aliyun recommedation
	 * @access private
	 * @return int
	 */
	function getAliyunRecState() {
		return get_option ( ALIYUNREC_SETTING_NAME, 0 );
	}
	
	/**
	 * check whether display aliyun recommedation or not
	 * @access private
	 * @return int
	 */
	function checkDisplayState($post) {
		return is_single () && ! is_page () && (get_post_status ( $post->ID ) == 'publish') && (get_post_type () == 'post') && empty ( $post->post_password ) && ! is_preview () && ! is_feed ();
	}
	
	/**
	 * 
	 * get template
	 * @access private
	 * @return string
	 */
	function locateTemplate($template_names, $load = false, $require_once = true) {
		$located = '';
		foreach ( ( array ) $template_names as $template_name ) {
			if (! $template_name)
				continue;
			if (file_exists ( STYLESHEETPATH . '/' . $template_name )) {
				$located = STYLESHEETPATH . '/' . $template_name;
				break;
			} else if (file_exists ( TEMPLATEPATH . '/' . $template_name )) {
				$located = TEMPLATEPATH . '/' . $template_name;
				break;
			}
		}
		
		if ($load && '' != $located)
			load_template ( $located, $require_once );
		
		return $located;
	}
}
AliyunRec_Guide::init ();