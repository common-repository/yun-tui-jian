<?php
/**
 * 应用类型与id配置文件
 * 格式：
 * $aliyunRecApplications['类型'] = 'id';
 * 类型可以为fixed与float，其中fixed代表固定位，float代表浮窗，多个浮窗样式id之间用,(英文逗号)隔开，固定位只允许有一个id。
 * 
 * 例如：
 * $aliyunRecApplications ['fixed'] = '9999';
 * $aliyunRecApplications ['float'] = '8888,7777';
 * 表示的意思是有一个id为9999的固定位样式，两个id分别为8888,7777的浮窗样式。
 * 
 * 如果没有某一类型的样式，则值用''代替
 * 例如：
 * $aliyunRecApplications ['fixed'] = '9999';
 * $aliyunRecApplications ['float'] = '';
 * 表示的意思是有一个id为9999的固定位样式，没有浮窗样式。
 * 
 * $aliyunRecApplications ['fixed'] = '';
 * $aliyunRecApplications ['float'] = '8888';
 * 表示的意思是没有固定位样式，有一个id为8888的浮窗样式。
 * 
 * $aliyunRecApplications ['fixed'] = '';
 * $aliyunRecApplications ['float'] = '8888,7777';
 * 表示的意思是没有固定位样式，有两个id分别为8888,7777的浮窗样式。
 * 
 * @createtime 2012-09-26
 */

! defined ( 'ALIYUNREC' ) && exit ( 'Forbidden' );

$aliyunRecApplications = array ();
$aliyunRecApplications ['fixed'] = '0'; //固定位推荐
$aliyunRecApplications ['float'] = ''; //浮窗推荐

return $aliyunRecApplications;