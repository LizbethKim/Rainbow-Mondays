<?php
include(dirname(__FILE__) . '/../../config/global_config.php');

$api = new TrademeJobsApi();
$api->setConsumerKey(CONSUMER_KEY);
$api->setSignature(SIGNATURE);
$api->updateJobCategories();
$api->runQuery();

$daoJobs = new DAO('jobs');

$result = $daoJobs->query("select max(id) as batchId from batches");
if(count($result) && isset($result[0]['batchId'])) {
    $batchId = (int)$result[0]['batchId'] + 1;
} else {
    $batchId = 1;
}

$daoBatch = new DAO('batches');
foreach($api as $listingId=>$job) {
    $dataset = $job->getDataset();
    $dataset['batchId'] = (int)$batchId;

    try {
        $daoJobs->insert($dataset);
        echo("Listing id: $listingId, title: " . $job->getTitle() . "\n");
    } catch (Exception $e) {
        echo "Duplicate!! " . $e->getMessage() . "\n";
    }
}

$daoBatch->insert(array(
    'id' => $batchId,
    'date' => time()
));
