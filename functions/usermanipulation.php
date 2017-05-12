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
			 if($mycaps['promote_to_mod'] == true){
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
		$callurl = $apiroot . "/jodlers/" . $user;
		$postfields = "{\n  \n  \"account_state\": \"$role\"\n\n}";
		$changes = putCall($callurl, $postfields);
	} 
 
	if(is_numeric($changes)){
		return true;
	} else {
		return false;
	}
 
 }
 