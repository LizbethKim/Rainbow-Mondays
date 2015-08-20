<?php
include(dirname(__FILE__) . '/../config.php');

$dao = new DAO('districts');
$localities = json_decode(file_get_contents('https://api.trademe.co.nz/v1/Localities.json?with_counts=false'));
if($localities !== null) {
    foreach($localities as $location) {
        foreach($location->Districts as $district) {
            $name = $district->Name;
            $location = getLongLat($name);
            $districId = $district->DistrictId;
            $dataset = array(
                'name'=>$name,
                'id'=> (int)$districId,
                'longitude'=> $location['longitude'],
                'latitude'=> $location['latitude']
            );
            $dao->insert($dataset);
            var_dump($dataset);
            echo "\n\n";
        }

    }
} else {
    throw new Exception("API call failed");
}
function getLongLat($placeName) {
    $placeName = $placeName . ', New Zealand';
    $geoCode = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($placeName);
    $result = json_decode(file_get_contents($geoCode));
    if($result !== null && $result->results) {
        $location = $result->results[0]->geometry->location;
        return(array(
            'longitude'=>$location->lng,
            'latitude'=>$location->lat
        ));
    } else {
        return(false);
    }
}
