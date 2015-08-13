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