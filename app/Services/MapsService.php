<?php namespace App\Services;

use GuzzleHttp\Client;
use Log;
use Exception;

class MapsService
{
    public function getLocation($address) {
        // Get the latitude and longitude from Google Geocode
        $client = new Client();
        try {
            $geocode = $client->get('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address));
            if ($geocode->getStatusCode() != 200) {
                // return an error response
                Log::error('Received a non-200 response from Google API.');
                throw new Exception('Unable to locate warehouse at this time');
            }
        } catch (Exception $e) {
            Log::error('Unable to get the geocode information from google with error: ' . $e->getMessage());
            throw new Exception('Unable to locate warehouse at this time');
        }
        $geoJson = json_decode($geocode->getBody()->getContents(), true);


        return [
            'address'    => $geoJson['results'][0]['formatted_address'],
            'latitude'   => $geoJson['results'][0]['geometry']['location']['lat'],
            'longitude'  => $geoJson['results'][0]['geometry']['location']['lng']
        ];
    }

    /**
     * Get the distance using the Haversine formula
     * Formula taken from Rosetta Code: http://rosettacode.org/wiki/Haversine_formula#PHP
     * @param  array    $destination    The location to send to with keys returned from getLocation method
     * @param  stdClass $warehouse      The warehouse to send the package from
     * @return int                      The distance between the points in meters
     */
    public function getDistance($destination, $warehouse) {
        $radiusOfEarth = 6371000;// Earth's radius in meters.
        $diffLatitude = $destination['latitude'] - $warehouse->latitude;
        $diffLongitude = $destination['longitude'] - $warehouse->longitude;
        $a = sin($diffLatitude / 2) * sin($diffLatitude / 2) +
            cos($warehouse->latitude) * cos($destination['latitude']) *
            sin($diffLongitude / 2) * sin($diffLongitude / 2);
        $c = 2 * asin(sqrt($a));
        $distance = $radiusOfEarth * $c;
        return $distance;
    }
}
