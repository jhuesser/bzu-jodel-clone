<?php
/**
 *
 * @param string $apiURL The URL to make a get call to
 * @return mixed The result of the GET call
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.1
 */

function getCall($apiURL){
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $apiURL,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "postman-token: 8f1ce1db-22b8-ec0b-85f8-4e16d90e9abd"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;
}
}
/**
 *
 * @param string $apiURL The URL to make a get call to
 * @param string The string contains a JSON with the post body 
 * @return mixed The result of the POST call
 *
 * @author Jonas Hüsser
 *
 * @since 0.1
 */
function postCall($apiURL, $postfields){


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $apiURL,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $postfields,
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json",
    "postman-token: 5b18fafa-5213-b41c-a687-13c53884e557"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;
}
}
/**
 *
 * @param string $apiURL The URL to make a get call to
 * @param string The string contains a JSON with the post body 
 * @return mixed The result of the PUT call
 *
 * @author Jonas Hüsser
 *
 * @since 0.1
 */
function putCall($apiURL, $postfields) {
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $apiURL,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_POSTFIELDS => $postfields,
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json",
    "postman-token: 3ee2e0d0-109c-a4de-b1a7-94629adb36f8"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;
}}
