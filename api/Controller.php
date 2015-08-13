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
}