<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/08/15
 * Time: 2:07 PM
 */

class Jobs {
    /** @var DAO */
    private static $daoJobs;
    /** @var DAO */
    private static $daoLocations;
    /** @var DAO */
    private static $daoCategories;
    /** @var DAO */
    private static $daoLiveCache;
    /** @var DAO */
    private static $daoCacheLog;
    /** @var DAO */
    private static $daoSearches;
    /**  @var array */
    private $filters;

    function __construct() {
        if(self::$daoJobs == null) {
            self::$daoJobs = new DAO('jobs');
        }
        if(self::$daoLocations == null) {
            self::$daoLocations = new DAO('districts');
        }
        if(self::$daoCategories == null) {
            self::$daoCategories = new DAO('categories');
        }
        if(self::$daoLiveCache == null){
            self::$daoLiveCache = new DAO('live_cache');
        }
        if(self::$daoCacheLog == null){
            self::$daoCacheLog = new DAO('cache_log');
        }
        if(self::$daoSearches == null){
            self::$daoSearches = new DAO('searches');
        }
    }

    function setFilters($filters) {
        if(is_array($filters)) {
            $this->filters = $filters;
        }
    }

    function getFeed($period = 300){
        $dao = self::$daoJobs;
        $maxBatchId = $dao->query("SELECT MAX(id) AS batchId FROM batches")[0]["batchId"];
        $startTime = time() - (60*60*24);
        $endTime = $startTime + $period;
        return $dao->query("SELECT
                                listedTime, longitude, latitude, jobs.id as id, jobTitle AS title
                              FROM jobs
                              JOIN districts
                              ON locationId = districts.id
                              WHERE listedTime > $startTime
                              AND listedTime < $endTime
                              AND batchId = $maxBatchId");

    }

    function getLiveFeed(){

        $daoCacheLog = self::$daoCacheLog;
        $time = time();

        //If the cache_log table is empty add the initially else check if it's been more than 5 minutes
        $count = $daoCacheLog->query("SELECT count(*) from cache_log")[0]["count(*)"];
        $latestCacheTime = $daoCacheLog->query("SELECT time FROM cache_log WHERE id = (SELECT MAX(id) FROM cache_log)")[0]["time"];
       if($count == 0) {
            $daoCacheLog->query("INSERT INTO cache_log (time) VALUES ($time)");
            $this->updateCache();
        }elseif(($latestCacheTime + 1 * 60) <= time()){
           $daoCacheLog->query("INSERT INTO cache_log (time) VALUES ($time)");
           $this->updateCache();
       }

        $date = new DateTime();
        $startTime = $date->getTimestamp();
        $endTime = $startTime - 2 * 60;

        $daoLiveCache = self::$daoLiveCache;
        $daoSearches = self::$daoSearches;


        return [$daoLiveCache->query("SELECT listedTime, longitude, latitude, live_cache.jobTitle AS title, icon_url As icon
                                 FROM  live_cache
                                 JOIN  districts
                                 ON    live_cache.locationId = districts.id
                                 WHERE listedTime < $startTime
                                 AND   listedTime > $endTime"),


                $daoSearches->query("SELECT serach_term, longitude, latitude, category, sub_category, time_searched
                                 FROM  searches
                                 JOIN  districts
                                 ON    searches.locationId = districts.id
                                 WHERE time_searched < $startTime
                                 AND   time_searched > $endTime")
        ];

    }

    public function getSearchFeed(){

    }

    /**
     * @return array
     * @throws Exception
     */
    private function getAllDistricts() {
        $daoLocations = self::$daoLocations;
        $result = $daoLocations->query("select * from districts");
        $districts = array();
        foreach($result as $district) {
            $districts[(int)$district['id']] = $district;
        }
        return($districts);
    }

    /**
     * @param $categoryId - Trademe category id
     * @return array<int>
     */
    private function getSubCategories($categoryId) {
        $build = array();
        $dao = self::$daoCategories;
        $result = $dao->query("SELECT id from categories where parentCategoryId = {$categoryId}");
        if(is_array($result) && count($result)) {
            foreach($result as $category) {
                $build[] = (int)$category['id'];
                $build = array_merge($build, $this->getSubCategories((int)$category['id']));
                //Not great but will work for now.
            }
            return($build);
        } else {
            return(array());
        }
    }

    /**
     * @return string
     */
    private function getSelectCriteria() {
        $categories = array();
        if(isset($this->filters['category']) && (int)$this->filters['category'] > 0) {
            $category = (int)$this->filters['category'];
            $categories = array_merge($categories, $this->getSubCategories($category));
            $categories[] = $category;
        }
        if(count($categories)) {
            return('categoryId in (' . implode(',', $categories) . ')');
        } else {
            return('');
        }
    }
    /**
     * @throws Exception
     */
    function getJobs() {
        $daoJobs = self::$daoJobs;
        $locations = $this->getAllDistricts();
        $conditions = $this->getSelectCriteria();
        if(strlen($conditions) > 0) {
            $conditions = 'and ' . $conditions;
        }

        if (isset($this->filters["time"])){
            $time = (int)$this->filters["time"];
            $query = "SELECT min(abs(date - $time)) as timeDelta, id as batchId from batches";
        } else {
            $query = "select max(id) as batchId from batches;";
        }
        $batchId = (int)$daoJobs->query($query)[0]['batchId'];

        $jobs = $daoJobs->query("select count(*) as 'count',jobs.* from jobs where batchId = $batchId {$conditions} group by locationId");

        $build = [];
        $maxJobs = (int)$jobs[0]['count'];
        foreach($jobs as $job) {
            if($job['count'] > $maxJobs) {
                $maxJobs = (int)$job['count'];
            }
        }
        foreach($jobs as $job) {
            if(!isset($locations[$job['locationId']])) {
                continue;
            }
            $build[] = array(
                'longitude'=> $locations[$job['locationId']]['longitude'],
                'latitude'=> $locations[$job['locationId']]['latitude'],
                'count' => log(($job['count'] / $maxJobs) * 100 + 1)
            );
        }
        return($build);
    }


    private function updateCache() {

        $dao_liveCache = new DAO("live_cache");
        //$dao_cache_log = new DAO('cache_log');
        //$dao_cache_log->insert(array('date' => time()));
        $previousLatest = (int)$dao_liveCache->query("select max(listedTime) as latest from live_cache")[0]["latest"];

        $api = new TrademeJobsApi();
        $api->setConsumerKey('6CEAB3585FFA4AEDB00EF2CFCEFABEF3');
        $api->setSignature('70941C9DF7CF72EFD272387821C4982E');
        $api->updateJobCategories();
        $api->runQuery();

        foreach ($api as $job) {
            if ($job->getListedTime() <= $previousLatest) {
                break;
            }

            $dataset = $job->getLiveDataset();
            $dao_liveCache->insert($dataset);

        }
    }

}
