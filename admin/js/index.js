var map = '';

(function () {
  // Kabankalan City center
  map = L.map('map').setView([9.9833, 122.8167], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);

  var kabankalanBounds = L.latLngBounds(
    [9.95, 122.78], // Southwest corner
    [10.02, 122.85], // Northeast corner
  );

  // Restrict the map to these bounds
  map.setMaxBounds(kabankalanBounds);

  // Optional: prevent zooming out too far
  map.setMinZoom(12);

  // Optional: bounce back if user tries to drag outside
  map.on('drag', function () {
    map.panInsideBounds(kabankalanBounds, { animate: true });
  });
})();
