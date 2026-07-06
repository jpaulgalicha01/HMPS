import { LeafLets, LeafLetDrawnItems, LeafLetRemoveBackGround } from "../../js/LeafLetSetup.js";
var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var removeBackGround = LeafLetRemoveBackGround();

var toolTip = "";
var mainBoundaryLayers = [];
window.loadmarker = {};

$(document).ready( function(){
    loadPolygons();
    chartAgeCat();
    chartPwdCat();
})

// Main polygon for the area setup
async function loadPolygons() {
    fncExecute("inputConfig.php?fetch_polygons=true", null, function (response) {
        var data = JSON.parse(response);
        data.forEach(function (poly) {
            var mainLayer = L.polygon(poly.coordinates).addTo(map);
            mainBoundaryLayers.push(mainLayer);

            mainLayer.options.dbId = poly.id; // Store the DB ID
            var mask = L.polygon([removeBackGround].concat([poly.coordinates]), {
                stroke: false,
                fillColor: "#000000",
                fillOpacity: .5
            }).addTo(map);
            map.fitBounds(mainLayer.getBounds());
            markers(); // e.g. 30 random house markers
            LoadCategoryListPolygon(poly.id);

        });
    }, "GET");
}

function LoadCategoryListPolygon(mainLayerID) {
    fncExecute(`inputConfig.php?getAreaSetupList=true&mainAreaID=${mainLayerID}`, null, function (response) {
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


function markers() {

    fncExecute("inputConfig.php?fetchingHouseholdCoords=true",null,function(response){
            var res = JSON.parse(response);

            if(res.status == 200){
                res.data.forEach(row => {
                var parts = row.household_coord.split(",");
                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);
                var m = L.marker([lat, lng], { icon: houseIcon }).addTo(map);
                    window.loadmarker[row.houshold_id] = m; // Store marker in global object
                    m.on('click', function () {
                        houseHoldInfo(row.houshold_id, m);
                    });
                })
            }

    },"GET");

}

$("#closeModal").click(function (){
    $(".leaflet-modal").css("display","none");

    // Restore map view
    //   var combinedBounds = L.latLngBounds([]);
    // mainBoundaryLayers.forEach(layer => combinedBounds.extend(layer.getBounds()));
    // map.fitBounds(combinedBounds);

    // Un-highlight marker
    if (window.__lastHouseholdMarker) {
        try {
            window.__lastHouseholdMarker.setOpacity(1);
        } catch (e) {}
    }
    window.__lastHouseholdMarker = null;
})


function houseHoldInfo(householdId, marker = null) {
 fncExecute(`inputConfig.php?fetchHousholdInfo=true&HouseHoldId=${householdId}`,null,function(response2){
        var res2 = JSON.parse(response2).data;
        var tableList = "";

        $(".leaflet-modal").css("display","block");
        map.fitBounds(marker.getBounds ? marker.getBounds() : L.latLngBounds([marker.getLatLng()]));
        
        res2.HouseHoldMemberList.sort((a, b) => a.family_order - b.family_order);
        // Highlight clicked marker
        if (window.__lastHouseholdMarker) {
            try {
                window.__lastHouseholdMarker.setOpacity(1);
            } catch (e) {}
        }
        window.__lastHouseholdMarker = marker;
        marker.setOpacity(0.7);

        res2.HouseHoldMemberList.forEach(row2 => {
            // Append records (do not overwrite)
            const label = row2.family_order == 1
                ? "Father Name :"
                : row2.family_order == 2
                    ? "Mother Name :"
                    : "";

            tableList += `
                <tr>
                    <td><b>${label}</b> ${row2.full_name}</td>
                </tr>
            `;
        });
        
        document.getElementById("sidebar").innerHTML = `
            <h4>Household Number : </br><u>${res2.household_number}</u></h4>
            <h6>Purok/Sitio: ${res2.purok_sitio_name}</h6>
            <table class="table table-sm">
                ${tableList}
            </table>
        `;
    },"GET")

}

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("view-btn")) {
        const householdId = e.target.dataset.id;
        viewHouseHoldInfo(householdId);
    }
});

function viewHouseHoldInfo(householdId) {
     var marker = window.loadmarker[householdId];
     if (marker) {
        houseHoldInfo(householdId, marker);
    }else {
        // fallback: load marker if not already created
        fncExecute(`inputConfig.php?loadMarkers=true&houseHoldId=${householdId}`, null, function(response) {
            var res = JSON.parse(response);
            if (res.status == 200) {
                res.data.forEach(row => {
                    var parts = row.household_coord.split(",");
                    var lat = parseFloat(parts[0]);
                    var lng = parseFloat(parts[1]);
                    var m = L.marker([lat, lng], { icon: houseIcon }).addTo(map);
                    window.householdMarkers[householdId] = m;
                    houseHoldInfo(householdId, m);
                });
            }
        },"GET");
    }
}

