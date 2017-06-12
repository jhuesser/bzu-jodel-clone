<?php

/**
 *
 * @param string $postdate The date a post was created
 * @return string The formated time interval
 *
 * @author Claudio Schmid
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.3
 */
function jodelage($postdate)
{
    $now = date('Y-m-d H:i:s');
    $now = date_create_from_format('Y-m-d H:i:s', $now);
    $postdate = date_create_from_format('Y-m-d H:i:s', $postdate);
    $interval = date_diff($postdate, $now);

    $tunit = array('%y','%m','%d','%H','%i','%s');
    $unit = 0;
    $x = FALSE;
    do {
        $timeago = $interval->format($tunit[$unit]);
        if ($timeago == 0) {
            $unit += 1;
        } else {
            switch ($unit) {
                case 0:
                    $timeago = $timeago . " Y";
                    $x = TRUE;
                    break;
                case 1:
                    $timeago = $timeago . " M";
                    $x = TRUE;
                    break;
                case 2:
                    $timeago = $timeago . " d";
                    $x = TRUE;
                    break;
                case 3:
                    $timeago = $timeago . " H";
                    $x = TRUE;
                    break;
                case 4:
                    $timeago = $timeago . " m";
                    $x = TRUE;
                    break;
                case 5:
                    $timeago = $timeago . " s";
                    $x = TRUE;
                    break;
                default:
                    $timeago = "just now";
                    $x = TRUE;
                    break;
            }

        }}while($x == FALSE);

return $timeago;

}

function getRandomColor(){
    global $apiroot;
    $allcolorsurl = $apiroot . "colors?transform=1";
	$allcolorsjson = getCall($allcolorsurl);
	$allcolors = json_decode($allcolorsjson, true);
    $resultcolor = array();
	//init array to store every colorID
	$colorIDs = array();
	foreach($allcolors['colors'] as $allcols){
		//add every colorID to array
		array_push($colorIDs,$allcols['colorID']);
	}
	//select random ID from array. This is the color to use
	$colornmb = $colorIDs[mt_rand(0, count($colorIDs) - 1)];
	//get details about the color
	$singlecolorurl = $apiroot . "colors?transform=1&filter=colorID,eq," . $colornmb;
	$colors = getCall($singlecolorurl);
	$color = json_decode($colors, true);
	foreach($color['colors'] as $col){
		//save color name and hex code in local values
        $resultcolor['colorID'] = $col['colorID'];
		$resultcolor['colorname'] = $col['colordesc'];
		$resultcolor['colorhex'] = $col['colorhex'];
	}
    return $resultcolor;
}

/**
 *
 * @param integer $postID ID of the post to get color
 * @return string The hex code of the post
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.3
 */
 function getColorOfPost( $postID){
     global $apiroot;
     $callurl = $apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $postID;
     $postjson = getCall($callurl);
     $posts = json_decode($postjson, true);
     $values = (object) array('hex' => "", 'name' => "", 'id' => "");
     foreach($posts['jodeldata'] as $post){
        $values->hex =  $post['colorhex'];
        $values->name = $post['colordesc'];
        $values->colid = $post['colorID'];
     }
     return $values;
 }
 /**
 *
 * @param string $postID The post to be deleted
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.7
 */
function deletePost($postID){
    global $apiroot, $baseurl, $config;
    //get uploaddir
    $uploaddir = $config->image_upload_dir;
    //get name of image
     $callurl = $apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $postID;
     $postjson = getCall($callurl);
     $posts = json_decode($postjson, true);
   foreach($posts['jodeldata'] as $post){
        $filename = $post["path"];
    }
    //if a path is set
   if(isset($filename)){
       //put path togheter
       $path = $uploaddir . $filename;
       //get imageID of image
       $images = json_decode(getCall($apiroot . "images?transform=1&filter=path,eq," . $filename), true);
       foreach($images['images'] as $image){
           $imageID = $image['imageID'];
       }
       //delete image from filesystem
       unlink($path);
       //delete image from DB. post is getting deleted to (CASCADE)
       deleteCall($apiroot . "images/" . $imageID);
   } else {
       //only delete post
    deleteCall($apiroot . "jodels/" . $postID);
   }
   header('Location: ' . $baseurl . 'jodels.php');
}

 /**
 *
 * @param string $commentID The comment to be deleted
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.7
 */
function deleteComment($commentID){
    global $apiroot, $config, $baseurl;
    //get uploaddir
    $uploaddir = $config->image_upload_dir;
    //get imageID of comment
    $callurl = $apiroot . "comments?transform=1&filter=commentID,eq," . $commentID;
    $commentjson = getCall($callurl);
    $comments = json_decode($commentjson, true);
    foreach($comments['comments'] as $comment){
        $imageID = $comment['imageIDFK'];
        $jodel = $comment['jodelIDFK'];
    }
    //get number of comments
    $orgpost = json_decode(getCall($apiroot . "jodeldata?transform=1&filter=jodelID,eq," . $jodel), true);
    foreach($orgpost['jodeldata'] as $pst){
        $cmnts = $pst['comments_cnt'];
    }
    $cmnts--;
    if(isset($imageID)){
        //get filename of image
        $image = json_decode(getCall($apiroot . "images?transform=1&filter=imageID,eq," . $imageID), true);
        foreach($image['images'] as $imageName){
            $filename = $imageName['path'];
        }
        //put togheter the path
        $path = $uploaddir . $filename;
 //delete image from filesystem
       unlink($path);
       //delete image from DB. comment is getting deleted to (CASCADE)
       deleteCall($apiroot . "images/" . $imageID);
   } else {
       //only delete comment
    deleteCall($apiroot . "comments/" . $commentID);
    $postfields = "{\n \n \"comments_cnt\": \"$cmnts\" \n \n }";
    putCall($apiroot . "jodels/" . $jodel, $postfields);
   }
   header('Location: ' . $baseurl . 'comments.php?showcomment=' . $jodel);
    }

?>