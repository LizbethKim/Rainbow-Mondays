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
     * @param $categoryId
     * @return array<int>
     */
    private function getSubCategories($categoryId) {
        $build = array();
        $dao = self::$daoCategories;
        $tmCategoryId = (int)$dao->query("select categoryId from categories where id = {$categoryId}")[0]['categoryId'];
        $result = $dao->query("SELECT id from categories where parentCategoryId = {$tmCategoryId}");
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
     * @return array
     */
    private function getSelectCriteria() {
        $conditions = array();
        if(isset($this->filters['category']) && (int)$this->filters['category'] > 0) {
            $category = (int)$this->filters['category'];
            $categories = $this->getSubCategories($category);
            $categories[] = $category;
            $conditions[] = 'categoryId in (' . implode(',', $categories) . ')';
        }

        return($conditions);
    }
    /**
     * @throws Exception
     */
    function getJobs() {
        $daoJobs = self::$daoJobs;
        $daoJobs->query("Select * from jobs");
        $locations = $this->getAllDistricts();
        $conditions = implode(' and ', $this->getSelectCriteria());
        if(strlen($conditions) > 0) {
            $conditions = 'and ' . $conditions;
        }
        $jobs = $daoJobs->query("select * from jobs where batchId = (select max(batchId) from batches) {$conditions}");


        $build = [];
        foreach($jobs as $job) {
            srand($job['jobId']);
            $r = (rand(-100000, 100000) / 100000) / 10;
            $t = rand(0, M_PI * 200) / 100;

            $build[] = array(
                'longitude'=> $locations[$job['locationId']]['longitude'], // + (sin($t) * $r),
                'latitude'=> $locations[$job['locationId']]['latitude'], // + (cos($t) * $r),
                'count' => 1
            );
        }
        return($build);
    }
}