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
      $result = $daoRegions->query("select * from regions");
      $lat = $_POST['lat'];
      $lng = $_POST['lng'];
      $cat = $_POST['category'];
      $currBest = "None";
      $currRegion = "None";
      $currBestDist = 300;
      foreach($result as $region) {
        $dist = max((float)$region['long'], $lng) - min((float)$region['long'], $lng);
        $dist = $dist + max((float)$region['lat'], $lat) - min((float)$region['lat'], $lat);
        if ($dist < $currBestDist){
          $currBestDist = $dist;
          $currBest = $region['id'];
          $currRegion = $region['name'];
        }
      }
      $daoJobs = new DAO('jobs');
      $result2 = [];
      if ($cat != 0){
        $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest AND j.categoryId = $cat GROUP BY type;");
      } else {
        $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest GROUP BY type;");
      }
      $partTime = 0;
      $fullTime = 0;
      $contract = 0;
      foreach($result2 as $res){
        if ($res['type'] == '1') $partTime = $res['count(j.id)'];
        if ($res['type'] == '0') $fullTime = $res['count(j.id)'];
        if ($res['type'] == '2') $contract = $res['count(j.id)'];
      }
      if ($cat == 0){
        $averageAge = $daoJobs->query("SELECT avg(listedTime) from jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest");
      } else {
        $averageAge = $daoJobs->query("SELECT avg(listedTime) from jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest AND j.categoryId = $cat");
      }
      $return = [];
      array_push($return, $partTime, $fullTime, $contract, $currRegion, $averageAge[0]);
      return($return);
    }
}
