<?php
include(dirname(__FILE__) . '/../../config.php');

$dao = new DAO('districts');
$dao_regions = new DAO('regions');
$dao->query("TRUNCATE districts;");
$dao_regions->query("truncate regions;");
$localities = json_decode(file_get_contents('https://api.trademe.co.nz/v1/Localities.json?with_counts=false'));
if($localities !== null) {
    foreach($localities as $location) {
        $lnglat = getLongLat($location->Name);
        $dataset = array(
            'id' => $location->LocalityId,
            'name' => $location->Name,
            '`long`' => $lnglat['longitude'],
            '`lat`' => $lnglat['latitude']
        );
        $dao_regions->insert($dataset);
        foreach($location->Districts as $district) {
            $name = $district->Name;
            $lnglat = getLongLat($name);
            $districId = $district->DistrictId;
            $dataset = array(
                'name'      => $name,
                'id'        => (int)$districId,
                'longitude' => $lnglat['longitude'],
                'latitude'  => $lnglat['latitude'],
                'region_id' => $location->LocalityId
            );
            $dao->insert($dataset);
            echo $dataset['name'] . "\n";
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
