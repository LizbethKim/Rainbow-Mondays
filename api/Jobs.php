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
    }

    function setFilters($filters) {
        if(is_array($filters)) {
            $this->filters = $filters;
        }
    }

    function getFeed($period = 300){
        $dao = self::$daoJobs;
        $startTime = time() - (60*60*24);
        $endTime = $startTime + $period;
        $result = $dao->query("SELECT listedTime, longitude, latitude FROM jobs JOIN districts ON locationId = districts.id WHERE listedTime > $startTime AND listedTime < $endTime ");
        return $result;
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
        $jobs = $daoJobs->query("select count(*) as 'count',jobs.* from jobs where batchId = (select max(batchId) from batches) {$conditions} group by locationId");

        $build = [];
        $maxJobs = (int)$jobs[0]['count'];
        foreach($jobs as $job) {
            if($job['count'] > $maxJobs) {
                $maxJobs = (int)$job['count'];
            }
        }
        foreach($jobs as $job) {
            $build[] = array(
                'longitude'=> $locations[$job['locationId']]['longitude'],
                'latitude'=> $locations[$job['locationId']]['latitude'],
                'count' => log(($job['count'] / $maxJobs) * 100 + 1)
            );
        }
        return($build);
    }
}