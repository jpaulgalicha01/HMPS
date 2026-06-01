import {LeafLets,LeafLetDrawnItems,LeafLetDrawControl} from "../../assets/leaflet/LeafLetSetup.js";
var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var drawControl = LeafLetDrawControl(map,drawnItems);

$(document).ready(function () {
  loadPolygons();
}); 
function loadPolygons() {
    fncExecute("inputConfig.php?fetch_polygons=true", null, function (response) {
      var data = JSON.parse(response);
      data.forEach(function (poly) {
        var layer = L.polygon(poly.coordinates).addTo(map);
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);
        layer.options.dbId = poly.id; // Store the DB ID
        layer.bindPopup("Loaded polygon ID: " + poly.id);
         map.fitBounds(layer.getBounds())
      });
    },"GET");
    
  }
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

  // When a polygon is edited
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
