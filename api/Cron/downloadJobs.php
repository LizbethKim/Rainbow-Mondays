<?php
include(dirname(__FILE__) . '/../config.php');

$api = new TrademeJobsApi();
$api->setConsumerKey('6CEAB3585FFA4AEDB00EF2CFCEFABEF3');
$api->setSignature('70941C9DF7CF72EFD272387821C4982E');
$api->updateJobCategories();
$api->runQuery();

$daoJobs = new DAO('jobs');
$batchId = (int)$daoJobs->query("select max(batchId) as batchId from jobs")[0]['batchId'] + 1;
$daoBatch = new DAO('batches');

foreach($api as $listingId=>$job) {
    $dataset = $job->getDataset();
    $dataset['batchId'] = (int)$batchId;
    try {
        $daoJobs->insert($dataset);
        echo("Listing id: $listingId, title: " . $job->getTitle() . "\n");
    } catch (Exception $e) {
        echo "Duplicate!!\n";
    }
}

$daoBatch->insert(array(
    'id' => $batchId,
    'date' => time()
));
