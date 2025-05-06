<!DOCTYPE html>
<html>
  <head>
    <title>Google Maps with Advanced Markers</title>
    <style>
      #map {
        height: 90vh;
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
          zoom: 10, 
          mapId: 'YOUR_MAP_ID'

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

              // Log marker position
              console.log(`Placing marker ${index + 1}:`, position);

              new google.maps.marker.AdvancedMarkerElement({
                map,
                position,
                title: `${location.name} - ${location.item}`,
              });

              bounds.extend(position);
              markerCount++;
            } else {
              console.warn(`Skipping location ${index + 1}: missing lat/lng`);
            }
          });

          if (markerCount > 0) {
            map.fitBounds(bounds);
          } else {
            console.warn("No valid markers placed.");
          }
        } catch (error) {
          console.error("Error loading or parsing data.json:", error);
        }
      }
    </script>

    <script
      src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap&libraries=marker"
      async defer>
    </script>
  </body>
</html>
