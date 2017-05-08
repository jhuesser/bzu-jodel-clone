<?php
/**
 *
 * @param string $config The config
 * @param int $acctype ID of user to check
 * @return string The account type
 *
 * @author Jonas H�sser
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
			$type = $config->app_vocabulary['baned'];
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
			$type = $config->app_vocabulary['baned'];
			
	 }
	 $usertype->typeID = $acctype;
	 $usertype->typedesc = $type;
	 return $usertype;
 }

 /**
 *
 * @param string $config The config
 * @param string $content post or comment
 * @param int $contentID ID of post or comment
 * @param int $reason Reason of reporting
 * @return mixed ID of report or null if failed
 *
 * @author Jonas H�sser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.5
 */
 function reportContent($config, $content, $contentID, $reason){
	 $apirurl = $config->apiUrl;
	 $userid = $_SESSION['userid'];
	 if($content == "post"){
		 $postfields = "{\n  \n  \"abuseIDFK\": $reason,\n  \"jodelIDFK\": $contentID\n,\n  \"jodlerIDFK\": $userid\n}";

	 } elseif($content == "comment"){
		 $postfields = "{\n  \n  \"abuseIDFK\": $reason,\n  \"commentIDFK\": $contentID\n,\n  \"jodlerIDFK\": $userid\n}";
	 }
	 $callurl = $apirurl . "/reports";
	 $resp = postCall($callurl, $postfields);
 }