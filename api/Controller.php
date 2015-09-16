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
        $ids = $daoFakeJobs->query("SELECT id from districts");
        $batchId = $daoFakeJobs->query("SELECT MAX(batchId) as maxBatchId from jobs")[0]["maxBatchId"];
        $fakeJobId = $daoFakeJobs->query('SELECT max(id) as maxJobId from jobs')[0]['maxJobId'];

        for($a = 0; $a < 15;$a++) {
            $fakeJobs[] = array(
                'id' => ++$fakeJobId,
                'batchId' => $batchId,
                'locationId' => (int)$ids[rand(0, count($ids))-1]['id'],
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

    public function getInfoAction(){
      $daoRegions = new DAO('regions');
      $result = $daoRegions->query("select * from districts");
      $lat = $_POST['lat'];
      $lng = $_POST['lng'];
      $currBest = "None";
      $currBestDist = 300;
      foreach($result as $region) {
        $dist = max((float)$region['longitude'], $lng) - min((float)$region['longitude'], $lng);
        $dist = $dist + max((float)$region['latitude'], $lat) - min((float)$region['latitude'], $lat);
        if ($dist < $currBestDist){
          $currBestDist = $dist;
          $currBest = $region['name'];
        }
      }
      return($currBest);
    }
}
