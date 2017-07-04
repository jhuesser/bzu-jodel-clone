<?php

/**
 *
 * @param int $user The user to manipulate
 * @param int $role The new role of the user
 * @param array $mycaps The caps of the admin
 * @return int The formated time interval
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.5
 */
 function manipulateUser($user, $role, $mycaps){
	 global $apiroot;
	 $execute = false;
	 switch($role){
		case 0:
		 if($mycaps['ban'] == true){
			 $execute = true;
		 }
		 break;
		case 1:
			 if($mycaps['promote_to_user'] == true){
			 $execute = true;
			}
			break;
		case 2:
			 if($mycaps['promote_to_mod'] == true ){
			 $execute = true;
			}
			break;
		case 3:
			 if($mycaps['promote_to_admin'] == true){
			 $execute = true;
			}
			break;
		case 4:
			 if($mycaps['promote_to_superadmin'] == true){
			 $execute = true;
			}
			break;
		default:
			$execute = false;
	 }
 
	if($execute == true){
		$callurl = $apiroot . "jodlers/" . $user;
		$postfields = "{\n  \n  \"account_state\": \"$role\"\n\n}";
		$changes = putCall($callurl, $postfields);
	} 
 
	if(is_numeric($changes)){
		return true;
	} else {
		return false;
	}
 
 }
 
 /**
 *
 * @param int $user2delete The ID of the user to delete.
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.5
 */
 function deleteUser($user2delete){
	global $apiroot, $config;

	//Get all posts of user
	$posts = json_decode(getCall($apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $user2delete), true);
	//Push all IDs to an array
	$postIDs = array();
	foreach($posts['jodels'] as $jodel){
		array_push($postIDs,$jodel['jodelID']);
	}
	//Save delete all posts of the user (images)
	foreach($postIDs as $post){
		deletePost($post);
	}
	deleteCall($apiroot . "jodlers/" . $user2delete);

 }