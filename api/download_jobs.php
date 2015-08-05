<?php
include('config.php');

$api = new TrademeJobsApi();
$api->setConsumerKey('6CEAB3585FFA4AEDB00EF2CFCEFABEF3');
$api->setSignature('70941C9DF7CF72EFD272387821C4982E');
$api->runQuery();
$daoJobs = new DAO('jobs');
$batchId = (int)$daoJobs->query("select max(batchid) as batchId from jobs")[0]['batchId'] + 1;

foreach($api as $listingId=>$job) {
    $dataset = $job->getDataset();
    $dataset['batchId'] = (int)$batchId;
    $daoJobs->insert($dataset);
    echo("Listing id: $listingId, title: " . $job->getTitle() . ", Location ID: " . $job->getLocationId() . "\n");
}