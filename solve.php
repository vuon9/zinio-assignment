<?php
function parseTextFile ($filename)
{
    $handle = fopen($filename, "r");
    while (!feof($handle)) {
        $line = explode(' ', trim(fgets($handle)));
        $cities[] = [
            'long' => array_pop($line),
            'lat' => array_pop($line),
            'city_name' => implode(' ', $line)
        ];
    }
    fclose($handle);

    return $cities;
}

function getDistanceBetweenTwoPoints($latitude1, $longitude1, $latitude2, $longitude2)
{
    $theta = $longitude1 - $longitude2;
    $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $distance = acos($distance);
    $distance = rad2deg($distance);

    return $distance;
}

function calculateShortestDistance($cities = array())
{
    // First city is Beijing
    $result[] = array_shift($cities);
    $totalCities = count($cities);

    // Temp vars
    $nextCity = null;
    $prevDistance = 0;

    for ($i = 0; $i < $totalCities; $i++) {
        $lastResult = end($result);

        foreach ($cities as $key => $city) {
            $distance = getDistanceBetweenTwoPoints($lastResult['lat'], $lastResult['long'], $city['lat'], $city['long']);

            $isFirstElement = is_null($nextCity);
            $isValidCity = isset($nextCity) && $prevDistance != 0 && $distance < $prevDistance;

            if ($isFirstElement or $isValidCity) {
                $nextCity = $city;
                $prevDistance = $distance;
            }
        }

        $result[] = $nextCity;
        unset($cities[array_search($nextCity, $cities)]);
        $nextCity = null;
        $prevDistance = 0;
    }

    return $result;
}

function printResult($result)
{
    foreach ($result as $city) {
        echo $city['city_name'] . "<br/>";
    }
}

$cities = parseTextFile('cities.txt');
$result = calculateShortestDistance($cities);
printResult($result);