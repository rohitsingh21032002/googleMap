<?php include 'config.php';
// If POST request with JSON data, save it to data.json
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jsonData = file_get_contents('php://input');
    // Validate JSON
    $decoded = json_decode($jsonData);
    if ($decoded !== null) {
        file_put_contents('data.json', $jsonData);
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'success', 'message' => 'Data saved'));
        exit;
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array('status' => 'error', 'message' => 'Invalid JSON'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Google Maps with Advanced Markers</title>
    <style>
      #map {
        height: 100vh;
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>

    <script>
      let map;

      async function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: 39.0997, lng: -94.5786 },
          zoom: 10
        });

        try {
          const response = await fetch("data.json");
          const locations = await response.json();

          const bounds = new google.maps.LatLngBounds();
          let markerCount = 0;

          locations.forEach((location, index) => {
            if (location.lat && location.lng) {
              const position = {
                lat: parseFloat(location.lat),
                lng: parseFloat(location.lng),
              };

              const marker = new google.maps.Marker({
                map: map,
                position: position,
                title: (location.name || '') + ' - ' + (location.item || ''),
              });

              bounds.extend(position);
              markerCount++;
            }
          });

          if (markerCount > 0) {
            map.fitBounds(bounds);
          } else {
            console.warn("No valid markers found.");
          }
        } catch (error) {
          console.error("Error loading or parsing data.json:", error);
        }
      }
    </script>

    <script
      src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&callback=initMap&libraries=marker"
      async defer>
    </script>
  </body>
</html>
