<?php
include 'config.php';
$apiKey = GOOGLE_API_KEY; // Replace with your dev API key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv']) && $_FILES['csv']['error'] === UPLOAD_ERR_OK) {
        $csvFile = $_FILES['csv']['tmp_name'];
        $handle = fopen($csvFile, 'r');
        $headers = fgetcsv($handle); // Read header

        $locations = array(); // ✅ PHP 5.3 compatible

        while (($data = fgetcsv($handle)) !== FALSE) {
            $row = array_combine($headers, $data);

            if (!isset($row['address']) || trim($row['address']) == '') {
                continue; // Skip if address is missing or empty
            }

            $address = urlencode($row['address']);

            // Google Maps Geocoding API call
            $geoUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$apiKey";
            $geoData = json_decode(file_get_contents($geoUrl), true);

            if ($geoData['status'] === 'OK') {
                $location = $geoData['results'][0]['geometry']['location'];
                $row['lat'] = $location['lat'];
                $row['lng'] = $location['lng'];
            } else {
                $row['lat'] = null;
                $row['lng'] = null;
            }

            $locations[] = $row; // ✅ PHP 5.3 compatible
            sleep(1); // Respect rate limits
        }

        fclose($handle);
        file_put_contents('data.json', json_encode($locations));
        echo json_encode(array('status' => 'success'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Upload failed'));
    }
}
