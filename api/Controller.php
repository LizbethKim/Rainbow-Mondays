<?php
class Controller {
    public function listAction () {
        $jobs = new Jobs();
        $jobs->setFilters($_POST);
        return $jobs->getJobs();
    }
    public function getCategoriesAction() {
        $daoCategories = new DAO('categories');
        $result = $daoCategories->query("select * from categories");
        $build = array();
        foreach($result as $category) {
            $build[] = array(
                'id'=> (int)$category['id'],
                'name'=> $category['categoryName'],
                'parentCategory' => (int)$category['parentCategoryId']
            );
        }
        return($build);
    }

    public function makeJobsAction(){
        $daoFakeJobs = new DAO('jobs');
        $fakeJobs = array ();
        $maxId = $daoFakeJobs->query("SELECT MAX(id) as maxId from districts")[0]["maxId"];
        $minId = $daoFakeJobs->query("SELECT MIN(id) as minId from districts")[0]["minId"];
        $batchId = $daoFakeJobs->query("SELECT MAX(batchId) as maxBatchId from jobs")[0]["maxBatchId"];
        $fakeJobId = $daoFakeJobs->query('SELECT max(id) as maxJobId from jobs')[0]['maxJobId'];
        //increment batch id somehow ?

        for($a = 0; $a < 15;$a++) {
            $fakeJobs[] = array(
                'id' => ++$fakeJobId,
                'batchId' => $batchId,
                'locationId' => rand($minId, $maxId),
                'categoryId' => -1,
                'listedTime' => time() + rand(10, 30) - (24*60*60)
            );
        }
        foreach($fakeJobs as $fakeJob) {
            $daoFakeJobs->insert($fakeJob);
        }
        return(array(
            'error' => false
        ));
    }

    public function getFeedAction() {
        $jobs = new Jobs();
        return $jobs->getFeed(5*60);

    }

    public function getRegionsAction(){
        $daoRegions = new DAO('regions');
        $result = $daoRegions->query("select * from districts");
        $build = array();
        foreach($result as $region) {
            $build[] = array(
                'id'=> (int)$region['id'],
                'name'=> $region['name'],
                'long'=> (float)$region['longitude'],
                'lat'=> (float)$region['latitude']
            );
        }
        return($build);
    }
}