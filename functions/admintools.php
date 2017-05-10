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
	 $apiurl = $config->apiUrl;
	 $userid = $_SESSION['userid'];
	 if($content == "post"){
		 $postfields = "{\n  \n  \"abuseIDFK\": \"$reason\",\n  \"jodelDFK\": \"$contentID\"\n,\n  \"jodlerIDFK\": \"$userid\"\n}";
		 
		 $scores = json_decode(getCall($apiurl . "jodels/" . $contentID . "?transform=1", true));
		 foreach($scores['jodels'] as $jodelscore){
			 $score = $jodelscore['score'];
		 }
		 $score = $score - $config->postmeta['get_report'];
		 $callurl = $apiurl . "jodels/" . $contentID;
		 $putfields = "{\n  \n  \"score\": \"$score\"\n \n}";
		 $scoreupdate = putCall($callurl, $putfields);

	 } elseif($content == "comment"){
		 $postfields = "{\n\t\"abuseIDFK\": \"$reason\",\n\t\"commentIDFK\": \"$contentID\",\n\t\"jodlerIDFK\": \"$userid\"\n}";

		 $scores = json_decode(getCall($apiurl . "comments/" . $contentID . "?transform=1", true));
		 foreach($scores['comments'] as $commentscore){
			 $score = $scommentscore['score'];
		 }
		 $score = $score - $config->postmeta['get_report'];
		 $callurl = $apiurl . "comments/" . $contentID;
		 $putfields = "{\n  \n  \"score\": \"$score\"\n \n}";
		 $scoreupdate = putCall($callurl, $putfields);
	 }
	 $callurl = $apiurl . "reports";
	 $resp = postCall($callurl, $postfields);
	 return $resp;
 }