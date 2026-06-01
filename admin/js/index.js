import { LeafLets, LeafLetDrawnItems, LeafLetRemoveBackGround } from "../../assets/leaflet/LeafLetSetup.js";
var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var removeBackGround = LeafLetRemoveBackGround();
  
$(document).ready(function(){
loadPolygons();

})


function loadPolygons() {
    fncExecute("inputConfig.php?fetch_polygons=true", null, function (response) {
        var data = JSON.parse(response);
        data.forEach(function (poly) {
            var mainLayer = L.polygon(poly.coordinates).addTo(map);
            mainLayer.options.dbId = poly.id; // Store the DB ID
            var mask = L.polygon([removeBackGround].concat([poly.coordinates]), {
                stroke: false,
                fillColor: "#000000",
                fillOpacity: .9
            }).addTo(map);
            map.fitBounds(mainLayer.getBounds());
        });
    }, "GET");
}
