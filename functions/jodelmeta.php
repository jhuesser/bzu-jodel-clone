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

function getRandomColor($apiroot){
    $allcolorsurl = $apiroot . "colors?transform=1";
	$allcolorsjson = getCall($allcolorsurl);
	$allcolors = json_decode($allcolorsjson, true);
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
		$colorname= $col['colordesc'];
		$colhex = $col['colorhex'];
	}
    return $colhex;
}

?>