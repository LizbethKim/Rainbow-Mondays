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

    public function makeLiveJobsAction(){
        $daoFakeCacheJobs = new DAO('live_cache');
        $fakeJobs = array ();
        $ids = $daoFakeCacheJobs->query("SELECT id from districts");
        $fakeJobId = $daoFakeCacheJobs->query('SELECT max(id) as maxJobId from jobs')[0]['maxJobId'];
        $fakeIcons = $daoFakeCacheJobs->query("SELECT icon_url FROM live_cache");

        for($a = 0; $a < 5;$a++) {
            $fakeJobs[] = array(
                'id' => ++$fakeJobId,
                'jobTitle' => "Test Job",
                'icon_url' => (string)$fakeIcons[rand(0, count($fakeIcons))-1]['icon_url'],
                'locationId' => (int)$ids[rand(0, count($ids))-1]['id'],
                'listedTime' => time() + rand(10, 30) - (2*60)
            );
        }
        foreach($fakeJobs as $fakeJob) {
            $daoFakeCacheJobs->insert($fakeJob);
        }
        return(array(
            'error' => false
        ));
    }

    public function makeSearchesAction(){
        $daoFakeSearches = new DAO('searches');
        $fakeSearches = array();
        $searches = $daoFakeSearches->query('SELECT jobTitle FROM jobs');
        $ids = $daoFakeSearches->query("SELECT id from districts");
        $categories = $daoFakeSearches->query('SELECT categoryName FROM categories');

        for($a = 0; $a < 5; $a++){
            $fakeSearches[] = array(
                'serach_term' => (string) $searches[rand(0, count($searches))-1]['jobTitle'],
                'category' => (string) $categories[rand(0, count($categories))-1]['categoryName'],
                'sub_category' => (string) $categories[rand(0, count($categories))-1]['categoryName'],
                'time_searched' => time() + rand(10, 30) - (2*60),
                'locationId' => (int)$ids[rand(0, count($ids))]['id']
            );
        }

        foreach($fakeSearches as $fakeSearch){
            $daoFakeSearches->insert($fakeSearch);
        }

        return(array(
            'error' => false
        ));

    }

    public function getFeedAction() {
        $jobs = new Jobs();
        return $jobs->getFeed(5*60);

    }

    public function getLiveFeedAction() {
        $jobs = new Jobs();
        return $jobs->getLiveFeed();

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

    public function getOverallInfoAction(){
      $level = $_POST['level'];
      $lat = $_POST['lat'];
      $lng = $_POST['lng'];
      $cat = $_POST['category'];
      if ($level == 0){
        $daoJobz = new DAO('jobs');
        $query = $daoJobz->query("SELECT count(j.id), max(batchid), type from jobs j GROUP BY type");
        $partTime = 0;
        $fullTime = 0;
        $contract = 0;
        foreach($query as $res){
          if ($res['type'] == '1') $partTime = $res['count(j.id)'];
          if ($res['type'] == '0') $fullTime = $res['count(j.id)'];
          if ($res['type'] == '2') $contract = $res['count(j.id)'];
        }
        $averageAge = $daoJobz->query("SELECT avg(listedTime) from jobs j");
        $return = [];
        array_push($return, "Overall", $fullTime, $partTime, $contract, $averageAge[0]);
        return $return;
      } else {
        $daoJobz = new DAO('jobs');
        $daoRegions = new DAO('regions');
        $districts = $daoRegions->query("select * from regions");

        foreach($result as $region) {
          $dist = max((float)$region['long'], $lng) - min((float)$region['long'], $lng);
          $dist = $dist + max((float)$region['lat'], $lat) - min((float)$region['lat'], $lat);
          if ($dist < $currBestDist){
            $currBestDist = $dist;
            $currBest = $region['id'];
            $currRegion = $region['name'];
          }
        }

        $query = $daoJobz->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest GROUP BY type");
        $partTime = 0;
        $fullTime = 0;
        $contract = 0;
        foreach($query as $res){
          if ($res['type'] == '1') $partTime = $res['count(j.id)'];
          if ($res['type'] == '0') $fullTime = $res['count(j.id)'];
          if ($res['type'] == '2') $contract = $res['count(j.id)'];
        }
        $averageAge = $daoJobz->query("SELECT avg(listedTime) from jobs j");
        $return = [];
        array_push($return, "Overall", $fullTime, $partTime, $contract, $averageAge[0]);
        return $return;
      }
    }

    public function getInfoAction(){
      $daoRegions = new DAO('regions');
      $lat = $_POST['lat'];
      $lng = $_POST['lng'];
      $cat = $_POST['category'];
      $level = $_POST['level'];
      $result = [];
      if ($level == 0){
        $result = $daoRegions->query("select * from regions");
      } else {
        $result = $daoRegions->query("select * from districts");
      }
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
      if ($cat != 0 && $level == 0){
        $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest AND j.categoryId = $cat GROUP BY type");
        $result2 = array_filter($result2);
        if (empty($result2)){
          $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id JOIN categories c on j.categoryId = c.id WHERE d.region_id = $currBest AND c.parentCategoryId = $cat GROUP BY type");
        }
      } elseif ($cat == 0 && $level == 0) {
        $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id WHERE d.region_id = $currBest GROUP BY type");
      } elseif ($cat != 0 && $level != 0){
        $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d on j.locationId = d.id WHERE d.id = $currBest");
        $result2 = array_filter($result2);
        if (empty($result2)){
          $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id JOIN categories c on j.categoryId = c.id WHERE d.region_id = $currBest AND c.parentCategoryId = $cat GROUP BY type");
        }
      } else {
        $result2 = $daoJobs->query("SELECT count(j.id), max(batchid), type FROM jobs j JOIN districts d ON j.locationId = d.id WHERE d.id = $currBest GROUP BY type");
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
        $averageAge = array_filter($averageAge[0]);
        if (empty($averageAge[0])){
          $averageAge = $daoJobs->query("SELECT avg(listedTime) from jobs j JOIN districts d ON j.locationId = d.id JOIN categories c on j.categoryId = c.id WHERE d.region_id = $currBest AND c.parentCategoryId = $cat");
        }
      }
      $return = [];
      array_push($return, $partTime, $fullTime, $contract, $currRegion, $averageAge[0]);
      return($return);
    }
}
