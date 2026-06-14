import { LeafLets, LeafLetDrawnItems, LeafLetRemoveBackGround } from "../../js/LeafLetSetup.js";
var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var removeBackGround = LeafLetRemoveBackGround();

var toolTip = "";


$(document).ready(function(){
loadPolygons();
LoadCategoryListPolygon();
chartAgeCat();
chartPwdCat();
})

// Main polygon for the area setup
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
            addDummyMarkers(); // e.g. 30 random house markers

        });
    }, "GET");
}

function LoadCategoryListPolygon() {
    fncExecute(`inputConfig.php?getAreaSetupList=true&mainAreaID=2`, null, function (response) {
        var res = JSON.parse(response);
        if (res.status == 200) {
            drawnItems.clearLayers();
            res.data.forEach(function (area) {
                // Adding in drawn items in main layer
                var coords = JSON.parse(area.category_coordinates);
                var layer = L.polygon(coords.map(c => [c[1], c[0]])).addTo(map);
                layer.options.dbId = area.category_area_id; // Store the DB ID
                layer.options.categoryId = area.category_id; // Store the Category ID
                layer.options.categoryLevelId = area.category_level_id; // Store the Category Level ID
                layer.options.originalColor = area.category_level_color; // Store original color for later use
                drawnItems.addLayer(layer);
                // Color the polygon based on category level
                layer.setStyle({ color: area.category_level_color, weight: 3 });
                 // Compute centroid of polygon
                var centroid = layer.getBounds().getCenter();
                // Clear all tooltips first to prevent duplicates
                layer.unbindTooltip();

                // Add a permanent tooltip at centroid
                L.tooltip({
                    permanent: true,
                    direction: "center",
                    className: "polygon-label"
                })
                .setContent(area.category_name + " (" + area.category_level_name + ")") // e.g. "Flood Area (High Risk)"
                .setLatLng(centroid)
                .addTo(map);
                // attachPolygonClickHandler(layer); // Enable click-to-edit functionality
            })}


    },"GET");
}

// Define a house icon
var houseIcon = L.icon({
    iconUrl: '../assets/icons/house-30-32.png', // path to your house icon image
    iconSize: [24, 24],
    iconAnchor: [12, 24],
    popupAnchor: [0, -24]
});

// Utility: generate random lat/lng inside polygon bounds
function getRandomPointInBounds(bounds) {
    var lat = bounds.getSouth() + Math.random() * (bounds.getNorth() - bounds.getSouth());
    var lng = bounds.getWest() + Math.random() * (bounds.getEast() - bounds.getWest());
    return L.latLng(lat, lng);
}

function addDummyMarkers() {

    fncExecute("inputConfig.php?fetchingHouseholdCoords=true",null,function(response){
            var res = JSON.parse(response);
            if(res.status == 200){
                res.data.forEach(row => {

                var parts = row.household_coord.split(",");
                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);

                var m = L.marker([lat, lng], { icon: houseIcon }).addTo(map);

                m.bindPopup("Household Number : " + row.household_number);

                // Click marker => show alert (and popup)
                m.on('click', function () {
                    // map.fitBounds(m.getBounds ? m.getBounds() : L.latLngBounds([m.getLatLng()]));
                    
                });
                    })
                }

    },"GET");

}
