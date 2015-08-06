<?php
class ListController extends Controller {
    public function indexAction() {
<<<<<<< HEAD
=======


>>>>>>> UI
        $daoJobs = new DAO('jobs');
        $daoLocations = new DAO('districts');

        $result = $daoLocations->query("select * from districts");
        $districts = array();
        foreach($result as $district) {
<<<<<<< HEAD
            $districts[(int)$district['id']] = $district;
=======
        $districts[(int)$district['id']] = $district;
>>>>>>> UI
        }

        $jobs = $daoJobs->query("select * from jobs where batchId = (select max(batchId) from jobs)");
        $build = [];
        foreach($jobs as $job) {
<<<<<<< HEAD
            srand($job['jobId']);
            $r = (rand(-100000, 100000) / 100000) / 10;
            $t = rand(0, M_PI * 200) / 100;

            $build[] = array(
                'longitude'=> $districts[$job['locationId']]['longitude'] + (sin($t) * $r),
                'latitude'=> $districts[$job['locationId']]['latitude'] + (cos($t) * $r),
=======
            $build[] = array(
                'longitude'=> $districts[$job['locationId']]['longitude'],
                'latitude'=> $districts[$job['locationId']]['latitude'],
>>>>>>> UI
                'count' => 1
            );
        }
        return $build;
    }
}
