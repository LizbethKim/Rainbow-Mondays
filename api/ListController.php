<?php
class ListController extends Controller {
    public function indexAction() {


        $daoJobs = new DAO('jobs');
        $daoLocations = new DAO('districts');

        $result = $daoLocations->query("select * from districts");
        $districts = array();
        foreach($result as $district) {
        $districts[(int)$district['id']] = $district;
        }

        $jobs = $daoJobs->query("select * from jobs where batchId = (select max(batchId) from jobs)");
        $build = [];
        foreach($jobs as $job) {
            $build[] = array(
                'longitude'=> $districts[$job['locationId']]['longitude'],
                'latitude'=> $districts[$job['locationId']]['latitude'],
                'count' => 1
            );
        }
        return json_encode($build);
    }
}
