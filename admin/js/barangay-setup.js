var map = L.map('map',{}).setView([9.9833, 122.8167], 14);
var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);
  var drawnItems = new L.FeatureGroup();

$(document).ready(function () {
  loadPolygons();
}); 
function loadPolygons() {

  // fetch("inputConfig.php?fetch_polygons=true")
  //   .then(res => res.json())
  //   .then(data => {
  //     data.forEach(function (poly) {
  //       var layer = L.polygon(poly.coordinates).addTo(map);
  //       drawnItems.clearLayers();
  //       drawnItems.addLayer(layer);
  //       layer.options.dbId = poly.id; // Store the DB ID
  //       layer.bindPopup("Loaded polygon ID: " + poly.id);
  //     });
  //   });
    fncExecute("inputConfig.php?fetch_polygons=true", null, function (response) {
      var data = JSON.parse(response);
      data.forEach(function (poly) {
        var layer = L.polygon(poly.coordinates).addTo(map);
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);
        layer.options.dbId = poly.id; // Store the DB ID
        layer.bindPopup("Loaded polygon ID: " + poly.id);
      });
    },"GET");
    
  }
  var kabankalanBounds = L.latLngBounds([9.95, 122.78], [10.02, 122.85]);
  map.setMaxBounds(kabankalanBounds);
  map.setMinZoom(12);
  map.on('drag', function () {
    map.panInsideBounds(kabankalanBounds, { animate: true });
  });
var drawControl = new L.Control.Draw({
  position: 'topleft',
  draw: {
    polygon: true,
    polyline: false,
    rectangle: false,
    circle: false,
    marker: false,
  },
  edit: {
    featureGroup: drawnItems, // ✅ required for edit/delete
    remove: true
  }
});
  map.addControl(drawControl);
  map.addLayer(drawnItems);

  // When a polygon is created
  map.on('draw:created', function (e) {
    var formdata = new FormData();
    var layer = e.layer;

      // ✅ Check if a polygon already exists
  var existingPolygons = drawnItems.getLayers().filter(l => l instanceof L.Polygon);
  if (existingPolygons.length > 0) {
    Swal.fire({
      icon: "warning",
      text: "A polygon already exists. You can only save one."
    });
    return; // stop here
  }
     // Clear existing polygons
    if (e.layerType === 'polygon') {
      var coords = layer.getLatLngs()[0];
      var coordArray = coords.map(function (c) {
        return [c.lat, c.lng];
      });
      formdata.append("coordinates", JSON.stringify(coordArray));
      formdata.append("save_polygon", true);
      fncExecute("inputConfig.php", formdata, function (response, textStatus, jqXHR) {
        var response = JSON.parse(response);
        if (response.status == 200) {
          ClsAlert({ icon: "success", title: response.message });
           loadPolygons(); // Reload polygons to get the new one with its DB id
        } else {
          ClsAlert({ icon: "error", title: response.message });
        }

      }, "POST");
    }
  });

  map.on("draw:edited", function (evt) {
  evt.layers.eachLayer(function (editedLayer) {
    var newCoords = editedLayer.getLatLngs()[0];
    var coordArray = newCoords.map(c => [c.lat, c.lng]); 

    var formdata = new FormData();
    formdata.append("update_polygon", true);
    formdata.append("polygon_id", editedLayer.options.dbId);
    formdata.append("coordinates", JSON.stringify(coordArray));
    fncExecute("inputConfig.php", formdata, function (response) {
      var res = JSON.parse(response);
      if (res.status == 200) {
        ClsAlert({ icon: "success", title: res.message });
        loadPolygons()
      } else {
        ClsAlert({ icon: "error", title: res.message });
      }
    }, "POST");
  });
});

map.on("draw:deleted", function (evt) {
  evt.layers.eachLayer(function (deletedLayer) {
    var formdata = new FormData();
    formdata.append("delete_polygon", true);
    formdata.append("polygon_id", deletedLayer.options.dbId); // must be set when loading

    fncExecute("inputConfig.php", formdata, function (response) {
      var res = JSON.parse(response);
      if (res.status == 200) {
        ClsAlert({ icon: "success", title: res.message });
        $("#familyTable").DataTable().ajax.reload(); 
      } else {
        ClsAlert({ icon: "error", title: res.message });
      }
    }, "POST");
  });
});
