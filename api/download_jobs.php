<?php
include('config.php');

$api = new TrademeJobsApi();
$api->setConsumerKey('xxxxxxxxxxxxxxxxxxxxxxxxxx');
$api->setSignature('xxxxxxxxxxxxxxxxxxxxxxxxxx');
$api->updateJobCategories();
$api->runQuery();

$daoJobs = new DAO('jobs');
$batchId = (int)$daoJobs->query("select max(batchid) as batchId from jobs")[0]['batchId'] + 1;

foreach($api as $listingId=>$job) {
    $dataset = $job->getDataset();
    $dataset['batchId'] = (int)$batchId;
    $daoJobs->insert($dataset);
    echo("Listing id: $listingId, title: " . $job->getTitle()
        . ", Location ID: " . $job->getLocationId()
        . ", Category: " . $job->getCategory()
        . "\n");
}