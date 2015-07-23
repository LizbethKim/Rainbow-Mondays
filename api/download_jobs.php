<?php
include('config.php');

$daoJobs = new DAO('jobs');
$daoDistricts = new DAO('districts');

var_dump(getJobs(0));





function getJobs($offset) {

    $parms = array(
        'rows' => 500
    );
    $build = array();
    foreach($parms as $k=>$v) {
        $build[] = "$k=$v";
    }
    $build = implode('&', $build);
    $curl = curl_init('https://api.tmsandbox.co.nz/v1/Search/Jobs.json' . $build);
    $accesstoken = 'Authorization: OAuth realm="https://api.tmsandbox.co.nz/v1/Search/Jobs.json",oauth_consumer_key="0E0A1C7A01EDC942F2DFDBF4D8BC5886",oauth_token="33840C149A8B38261A178099A0AE578F",oauth_signature_method="PLAINTEXT",oauth_timestamp="' . time() . '",oauth_nonce="' . substr(uniqid(),0,6) . '",oauth_version="1.0",oauth_signature="18907B0803011F36989E8812F265525F%264E4969D3EB33980A248593E4B48F4220"';
    $header = array('Authorization: OAuth ' . $accesstoken);
    curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $rest = curl_exec($curl);
    curl_close($curl);
    return(json_decode($rest));
}