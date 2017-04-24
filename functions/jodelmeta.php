<?php

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

?>
