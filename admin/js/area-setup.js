import { LeafLets, LeafLetDrawnItems, LeafLetDrawControl, LeafLetRemoveBackGround } from "../../js/LeafLetSetup.js"
var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var drawControl = LeafLetDrawControl(map, drawnItems);
var CoordinateArray = [];

var mainLayer = null;
var removeBackGround = LeafLetRemoveBackGround();
var selectedDrawnLayer = null; // Tracks the active/focused drawn layer
var isEditLocked = false;      // 🌟 Tracks if a layer has an uncompleted out-of-bounds error




$(document).ready(async function() {
    await LegendList();
    await loadPolygons();  
    await setDrawControl();

   
});


function setDrawControl() {
    if (drawControl) {
        map.removeControl(drawControl); // ✅ clears previous controller
    }

    drawControl = new L.Control.Draw({
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
}

// Adding Polygon from databse to map
async function LoadCategoryListPolygon() {
    fncExecute(`inputConfig.php?getAreaSetupList=true&mainAreaID=${mainLayer.options.dbId}`, null, function (response) {
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
                // Clear existing tooltip from this layer
                layer.unbindTooltip();

                // Add a permanent tooltip at centroid
               layer.bindTooltip(
                    area.category_name + " (" + area.category_level_name + ")",
                    {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }
                ).openTooltip()
                // .setContent(area.category_name + " (" + area.category_level_name + ")") // e.g. "Flood Area (High Risk)"
                // .setLatLng(centroid)
                .addTo(map);
                attachPolygonClickHandler(layer); // Enable click-to-edit functionality
            })}


    },"GET");
}

async function loadPolygons() {
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
                LoadCategoryListPolygon(); // Load category polygons after main polygon is ready
        });
    }, "GET");
}

async function LegendList() {
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
async function addPolygonToSidebar(coords, mainLayerID,layer) {
    ResetForm()
    $("#AreaSetup #sidebar").removeClass("d-none");
    $("#sidebar #polygon_id").val(mainLayerID);
    $("#sidebar #category_area_id").val(layer.options.dbId || ""); // Set category_area_id for existing polygons, empty for new ones
    CoordinateArray.length = 0; // Clear existing contents while keeping the reference intact
    CoordinateArray = coords; // Store the current coordinates in a global variable for later use
    if(!ClsUnidentified(layer.options.dbId)){
         // need to wait for category name to load before setting category level
            await $("#categoryID").val(layer.options.categoryId).trigger("change");
        // after loading the category name and its levels, we can set the category level dropdown to the correct value
        setTimeout(function() {
            $("#CategoryLevel").val(layer.options.categoryLevelId);
        }, 200); // Adjust the timeout duration as needed based on your data loading time
    }
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

function attachPolygonClickHandler(layer) {
    layer.on('click', function(event) {
        L.DomEvent.stopPropagation(event);

        // 🌟 SAFETY INTERCEPT
        if (isEditLocked && selectedDrawnLayer !== layer) {
            ClsAlert({
                icon: "warning",
                title: "Action Blocked. Please fix the out-of-bounds layer first!"
            });
            return;
        }

        // Reset previous selection
        if (selectedDrawnLayer && selectedDrawnLayer !== layer) {
            if (selectedDrawnLayer.editing) {
                selectedDrawnLayer.editing.disable();
            }
            selectedDrawnLayer.setStyle({ color: selectedDrawnLayer.options.originalColor, weight: 3 });
        }

        selectedDrawnLayer = layer;
        console.log("Focused in: Enabling edit mode.");

        // Backup coordinates
        layer.options.previousLatLng = clonePolygonLatLngs(layer);

        if (layer.editing) {
            layer.editing.enable();
        }
        layer.setStyle({ color: '#ff0000', weight: 5 });

        // Update sidebar
        var coords = layer.getLatLngs()[0].map(c => [c.lng, c.lat]);
        addPolygonToSidebar(coords, mainLayer.options.dbId,layer);
    });

      // ✅ Live update while editing
    layer.on("edit", function() {
         var createdGeoJSON = layer.toGeoJSON();
        var mainGeoJSON = mainLayer.toGeoJSON();
        var isInside = turf.booleanWithin(createdGeoJSON, mainGeoJSON);
        if (!isInside) {
            ClsAlert({ icon: "info", title: "Unable to save. Polygon is outside of boundary!" });
            isEditLocked = true;
            return;
        } 
        isEditLocked = false; // Clear lock if edit is valid
        var coords = this.getLatLngs()[0].map(c => [c.lng, c.lat]);
        addPolygonToSidebar(coords, mainLayer.options.dbId, this);
    });
}  
map.on('click', function (e) {
    if (selectedDrawnLayer) {
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
        // Restore original DB color
        selectedDrawnLayer.setStyle({ 
            color: selectedDrawnLayer.options.originalColor, 
            weight: 3 
        });
        // Hide sidebar
        $("#AreaSetup #sidebar").addClass("d-none");
        selectedDrawnLayer = null;
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
     layer.options.originalColor = "#3388ff"; 
     attachPolygonClickHandler(layer);
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

        // Highlight the selected layer visually
        editedLayer.setStyle({ color: '#ff0000', weight: 5 });
        selectedDrawnLayer = editedLayer;
        var coords = editedLayer.getLatLngs()[0].map(c => [c.lng, c.lat]);
        addPolygonToSidebar(coords, mainLayer.options.dbId,editedLayer);
        // Keep tracking safe structured double nested coordinates
        editedLayer.options.previousLatLng = clonePolygonLatLngs(editedLayer);
        editedLayer.options.originalColor = area.category_level_color;
    });
});


map.on("draw:deleted", function (evt) {
  evt.layers.eachLayer(function (deletedLayer) {
    var formdata = new FormData();
    formdata.append("delete_area_polygon", true);
    formdata.append("category_area_id", deletedLayer.options.dbId); // must be set when loading
    console.log("Deleting polygon with ID:", deletedLayer.options.dbId);
    fncExecute("inputConfig.php", formdata, function (response) {
      var res = JSON.parse(response);
      if (res.status == 200) {
        ClsAlert({ icon: "success", title: res.message });
        LoadCategoryListPolygon();

      } else {
        ClsAlert({ icon: "error", title: res.message });
      }
    }, "POST");
  });
});


$("#categoryID").change(function(){
    var value = $(this).val();
    fncExecute(`inputConfig.php?getCategoryLevel=true&categoryID=${value}`,'',function (response) {
        const res = JSON.parse(response);
        if(res.status == 200){
            const drpDown = document.getElementById("CategoryLevel");
            drpDown.innerHTML = '<option disabled selected>Select Category Level</option>';
            res.data.forEach(level => {
                const option = document.createElement("option");
                option.value = level.category_level_id;
                option.textContent = level.category_level_name;
                option.style.backgroundColor = level.category_level_color;
                drpDown.appendChild(option);
            });
        }

    },'GET')
})
// submitting form
$(document).on("submit","#SubmitCategoryArea",function(e){
    e.preventDefault();

    if(isEditLocked){
        ClsAlert({
            icon: "warning",
            title: "Please fix the out-of-bounds issue before submitting!"
        });
        return;
    }

    var formData = new FormData(this);
    formData.append("submit_area_setup",true);
    formData.set("category_coords",JSON.stringify(CoordinateArray));
    fncExecute("inputConfig.php",formData,function(response, textStatus, jqXHR){
        var res = JSON.parse(response);
        if (res.status == 200) {
        ClsAlert({icon: "success", title: res.message});
        LoadCategoryListPolygon();
        ResetForm()
         $("#AreaSetup #sidebar").addClass("d-none");
        }else{
            ClsAlert({icon: "error", title: res.message});
        }
    },"POST")
})
function ResetForm(){
    $("#AreaSetup #SubmitCategoryArea")[0].reset(); // Reset form fields    
    $("#AreaSetup #CategoryLevel").html('<option disabled selected>Select Category Level</option>'); // Reset category level dropdown
}