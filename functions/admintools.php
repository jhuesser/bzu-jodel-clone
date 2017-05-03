<?php
/**
 *
 * @param string $config The config
 * @param int $acctype ID of user to check
 * @return string The account type
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.4
 */
 function getAccountType($config, $acctype){
	 //init array
	 $usertype = (object) array();
	 switch($acctype){
		case 0:
			$type = $config->app_vocabulary['banned'];
			break;
		case 1:
			$type = $config->app_vocabulary['jodler'];
			break;
		case 2:
			$type = $config->app_vocabulary['mod'];
			break;
		case 3:
			$type = $config->app_vocabulary['admin'];
			break;
		case 4:
			$type = $config->app_vocabulary['superadmin'];
			break;
		default:
			$type = $config->app_vocabulary['banned'];
			
	 }
	 $usertype->typeID = $acctype;
	 $usertype->typedesc = $type;
	 return $usertype;
 }