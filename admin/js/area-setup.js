import { LeafLets, LeafLetDrawnItems, LeafLetDrawControl, LeafLetRemoveBackGround } from "../../assets/leaflet/LeafLetSetup.js";

var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var drawControl = LeafLetDrawControl(map, drawnItems);


var mainLayer = null;
var removeBackGround = LeafLetRemoveBackGround();
var selectedDrawnLayer = null; // Tracks the active/focused drawn layer
var isEditLocked = false;      // 🌟 Tracks if a layer has an uncompleted out-of-bounds error

$(document).ready(function() {
    LegendList();
    loadPolygons();  
});

function loadPolygons() {
    fncExecute("inputConfig.php?fetch_polygons=true", null, function (response) {
        var data = JSON.parse(response);
        data.forEach(function (poly) {
            mainLayer = L.polygon(poly.coordinates).addTo(map);
            mainLayer.options.dbId = poly.id; // Store the DB ID
            var mask = L.polygon([removeBackGround].concat([poly.coordinates]), {
                stroke: false,
                fillColor: "#000",
                fillOpacity: 0.5
            }).addTo(map);
            map.fitBounds(mainLayer.getBounds());
        });
    }, "GET");
}

function LegendList() {
    const categoryList = [];
    $.ajax({
        url: "inputConfig.php",
        type: "GET",
        data: { getCategoryList: true },
    }).done(function(response) {
        const res = JSON.parse(response);
        if (res.status === 200) {
            res.data.forEach(category => {
                if (!categoryList.some(cat => cat.CategoryID === category.CategoryID)) {
                    categoryList.push({
                        CategoryID: category.CategoryID,
                        CategoryName: category.CategoryName,
                        Levels: []
                    });
                }
                category.CategoryListLevel.forEach(CategoryListLevel => {
                    categoryList[categoryList.length - 1].Levels.push({
                        LevelName: CategoryListLevel.CatLevelName,
                        Color: CategoryListLevel.Color
                    });
                });
            });
            initializeLegendList(categoryList);
        } else {
            ClsAlert({ icon: "error", title: res.message });
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        ClsAlert({ icon: "error", title: "Failed to fetch category list." });
    });
}

function initializeLegendList(categoryList) {
    // Clear Existing Legend List
    $("#legendList").empty();
    // Populate Legend List
    categoryList.forEach(category => {
        const levelsHtml = category.Levels.map(level => 
            `<span class="badge bg-primary" style="background-color: ${level.Color} !important;">${level.LevelName}</span>`
        ).join(" ");
        $("#legendList").append(`
            <div class="card mb-2">
                <div class="card-body p-2">
                    <h5 class="card-title mb-1">${category.CategoryName}</h5>
                    <p class="card-text mb-0">${levelsHtml}</p>
                </div>
            </div>
        `);
    });
}
function addPolygonToSidebar(coords, mainLayerID) {
    $("#AreaSetup #sidebar").removeClass("d-none");
    $("#sidebar #mainLayer_id").val(mainLayerID);
    $("#sidebar #category_coords").val(coords);
}
// Helper utility function to handle deep-nested coordinate cloning structure correctly [[latlng, latlng]]
function clonePolygonLatLngs(layerInstance) {
    var nativeLatLngs = layerInstance.getLatLngs();
    return nativeLatLngs.map(function(ring) {
        return ring.map(function(latlng) {
            return L.latLng(latlng.lat, latlng.lng);
        });
    });
}

// 1. GLOBAL MAP CLICK (Focus Out / Stop Editing)
map.on('click', function (e) {
    if (selectedDrawnLayer) {
        // 🌟 SAFETY CHECK: Block closing focus out actions if the current layer is broken
        if (isEditLocked) {
            ClsAlert({
                icon: "warning",
                title: "Please fix the out-of-bounds issue before closing!"
            });
            return;
        }

        console.log("Focused out: Disabling edit mode.");
        
        if (selectedDrawnLayer.editing) {
            selectedDrawnLayer.editing.disable();
        }
        
        map.fire('draw:edited', {
            layers: L.layerGroup([selectedDrawnLayer])
        });
        
        selectedDrawnLayer.setStyle({ color: '#3388ff', weight: 3 }); 
        selectedDrawnLayer = null;
        $("#AreaSetup #sidebar").addClass("d-none");
    }
});

map.on('draw:created', function(e){
    var layer = e.layer;
    var createdGeoJSON = layer.toGeoJSON();
    var mainGeoJSON = mainLayer.toGeoJSON();
    var isInside = turf.booleanWithin(createdGeoJSON, mainGeoJSON);
    if (!isInside) {
        ClsAlert({ icon: "info", title: "Unable to save. Polygon is outside of boundary!" });
        return;
    } 
    if (isEditLocked) {
        ClsAlert({
            icon: "warning",
            title: "Please fix the out-of-bounds issue before closing!"
        });
        return;
    }

    drawnItems.addLayer(layer);
    layer.on('click', function(event) {
        L.DomEvent.stopPropagation(event); 

        // 🌟 SAFETY INTERCEPT: Block switching to another layer if the app status is locked
        if (isEditLocked && selectedDrawnLayer !== layer) {
            ClsAlert({
                icon: "warning",
                title: "Action Blocked. Please fix the out-of-bounds layer first!"
            });
            return;
        }

        if (selectedDrawnLayer && selectedDrawnLayer !== layer) {
            if (selectedDrawnLayer.editing) {
                selectedDrawnLayer.editing.disable();
            }
            selectedDrawnLayer.setStyle({ color: '#3388ff', weight: 3 });
        }

        selectedDrawnLayer = layer;
        console.log("Focused in: Enabling edit mode.");
        
        // Refresh the backup matrix upon clicking, breaking previous references
        layer.options.previousLatLng = clonePolygonLatLngs(layer);

        if (layer.editing) {
            layer.editing.enable();
        }
        layer.setStyle({ color: '#ff0000', weight: 5 });
        var coords = layer.getLatLngs()[0].map(c => [c.lng, c.lat]);
        addPolygonToSidebar(coords, mainLayer.options.dbId);
    });

    // Save initial structured backup array
    layer.options.previousLatLng = clonePolygonLatLngs(layer);
});

// When a polygon is edited
map.on("draw:edited", function (evt) {
    evt.layers.eachLayer(function (editedLayer) {
        var editedGeoJSON = editedLayer.toGeoJSON();
        var mainGeoJSON = mainLayer.toGeoJSON();
        var isInside = turf.booleanWithin(editedGeoJSON, mainGeoJSON);
        
        if (!isInside) {
            ClsAlert({
                icon: "info",
                title: "Unable to save. Polygon is outside of boundarys!" 
            });
            // 🌟 SET LOCK STATUS: Freeze access to other layout structures
            isEditLocked = true;

            if (editedLayer.options.previousLatLng) {
                // Wipe lingering ghost midpoints and markers cleanly
                if (editedLayer.editing && editedLayer.editing._markerGroup) {
                    map.removeLayer(editedLayer.editing._markerGroup);
                }
                if (editedLayer.editing) {
                    editedLayer.editing.disable();
                }

                // Snap the underlying coordinate data back to safe limits
                editedLayer.setLatLngs(editedLayer.options.previousLatLng);
                editedLayer.redraw();
                // Re-apply focus active style color to ensure it matches
                editedLayer.setStyle({ color: '#ff0000', weight: 5 });
            }
            return; // Exit out, preventing bad coordinates from saving to sidebar/DB
        }
        
        // 🌟 RELEASE LOCK STATUS: Path configuration is valid again
        isEditLocked = false;

        if (selectedDrawnLayer && selectedDrawnLayer !== editedLayer) {
            selectedDrawnLayer.setStyle({ color: '#3388ff', weight: 3 });
        }
        // Highlight the selected layer visually
        editedLayer.setStyle({ color: '#ff0000', weight: 5 });
        selectedDrawnLayer = editedLayer;
        var coords = editedLayer.getLatLngs()[0].map(c => [c.lng, c.lat]);
        addPolygonToSidebar(coords, mainLayer.options.dbId);
        // Keep tracking safe structured double nested coordinates
        editedLayer.options.previousLatLng = clonePolygonLatLngs(editedLayer);
    });
});

$("#CategoryName").change(function(){
    var value = $(this).val();
    fncExecute(`inputConfig.php?getCategoryLevel=true&categoryID=${value}`,'',function (response) {
        const res = JSON.parse(response);

    },'GET')
})