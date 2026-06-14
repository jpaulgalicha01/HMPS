import { LeafLets, LeafLetDrawnItems, LeafLetDrawControl, LeafLetRemoveBackGround } from "../../js/LeafLetSetup.js";

var map = LeafLets();
var drawnItems = LeafLetDrawnItems();
var removeBackGround = LeafLetRemoveBackGround();
// Keep a reference so we can remove/replace the draw control later.
var drawControl = null;

// Store main polygon boundary layers (for point-in-polygon validation)
var mainBoundaryLayers = [];

function setDrawControl() {
    if (drawControl) {
        map.removeControl(drawControl); // ✅ clears previous controller
    }

    drawControl = new L.Control.Draw({
        position: 'topleft',
        draw: {
            polygon: false,
            polyline: false,
            rectangle: false,
            circle: false,
            marker: true,
        },
        edit: {
            featureGroup: drawnItems,
            remove: true,
        }
    });

    map.addControl(drawControl);
    map.addLayer(drawnItems);
}

$(document).ready(function () {
    loadPolygons();
    setDrawControl();
    initializePropertyTable();

});

// Initialize table
const initializePropertyTable = () => {
   $("#householdTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getHousholdList": true},
        },
        columns: [
            { title: "Household Number", data: "household_number", className: "dt-body-center dt-head-center  px-2", width: "20%" },
            { title: "Purok Name", data: "purok_sitio_name", className: "dt-body-center dt-head-center  px-2", width: "15%" },
            { title: "Household Info", data: "FamilyMember", className: "dt-body-center dt-head-center  px-2", width: "40%" },
            {
                title: "Action", data: "houshold_id", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `
                     <div class="dropdown dropstart">
                      <button class="btn btn-success btn-sm" data-bs-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <button class="form-control btn btn-success btn-sm updateHouseHold"  id="${encodeURI(data)}"><i class="fas fa-edit"></i> Edit</button>
                        </li>
                        <li class="dropdown-item">
                          <button class="form-control btn btn-danger btn-sm deleteHouseHold" id="${encodeURI(data)}" number="${encodeURI(row.household_number)}"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};

// Main polygon for the area setup
function loadPolygons() {
    fncExecute("inputConfig.php?fetch_polygons=true", null, function (response) {
        var data = JSON.parse(response);
        data.forEach(function (poly) {
            var mainLayer = L.polygon(poly.coordinates).addTo(map);
            mainBoundaryLayers.push(mainLayer);

            // Keep existing mask logic (visual effect)
            L.polygon([removeBackGround].concat([poly.coordinates]), {
                stroke: false,
                fillColor: "#000000",
                fillOpacity: .9
            }).addTo(map);

            map.fitBounds(mainLayer.getBounds());
        });
    }, "GET");
}


function loadMarkers(ID) {
    fncExecute(`inputConfig.php?loadMarkers=true&houseHoldId=${encodeURIComponent(ID)}`, null, function(response) {
        var res = JSON.parse(response);
        if (res.status === 200) {
            drawnItems.clearLayers();
            console.log(res.data);

            res.data.forEach(function (item) {
                // Split "lat,lng" string into array
                var parts = item.household_coord.split(",");
                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);

                // Create marker
                var layer = L.marker([lat, lng]).addTo(map);
                drawnItems.addLayer(layer);
                map.fitBounds(layer.getBounds ? layer.getBounds() : L.latLngBounds([layer.getLatLng()]));
            });
        }
    }, "GET");
}



function isPointInsideMainPolygons(latlng) {
    if (!mainBoundaryLayers || mainBoundaryLayers.length === 0) return false;

    // Build a point GeoJSON
    const pointGeo = {
        type: 'Feature',
        geometry: {
            type: 'Point',
            coordinates: [latlng.lng, latlng.lat]
        }
    };

    // If turf is available, use booleanPointInPolygon.
    if (typeof turf !== 'undefined' && turf.booleanPointInPolygon) {
        return mainBoundaryLayers.some(function (polyLayer) {
            const latlngs = polyLayer.getLatLngs();
            const ringLatLngs = Array.isArray(latlngs) && Array.isArray(latlngs[0]) ? latlngs[0] : [];

            const coords = ringLatLngs.map(function (c) {   
                return [c.lng, c.lat];
            });

            if (coords.length === 0) return false;

            // Ensure ring is closed (avoid single-line ternary that breaks TS parsing)
            const first = coords[0];
            const last = coords[coords.length - 1];
            let ring;
            if (first[0] === last[0] && first[1] === last[1]) {
                ring = coords;
            } else {
                ring = coords.concat([first]);
            }

            const polyGeo = {
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [ring]
                }
            };

            return turf.booleanPointInPolygon(pointGeo, polyGeo);
        });
    }

    // Fallback: if turf is not loaded, approximate by bounds
    return mainBoundaryLayers.some(function (polyLayer) {
        return polyLayer.getBounds().contains(latlng);
    });
}

$("#AddHouseHoldMembers").click(function()
{
    fncAddHouseHoldMembersBtn({data:null})
})
function fncAddHouseHoldMembersBtn(args = {}) {
    // 1. Safe destructuring with default fallback
    const { data = null } = args; 

    // 2. Table Row Setup
    const tr = document.createElement('tr');
    tr.classList.add('text-center');
    tr.dataset.categoryLevelId = "";

    // Unique ID generation (Counter or Random String is safer than Date.now())
    const randomId = Math.random().toString(36).substr(2, 9);
    const personSelectId = `person_unique_id_${randomId}`;

    // -- Column 1: Full Name --
    const tdFullName = document.createElement('td');
    const selectPerson = document.createElement('select');
    
    selectPerson.id = personSelectId;
    selectPerson.name = 'person_unique_id[]';
    selectPerson.className = 'form-select form-control select-basic-single';
    selectPerson.required = true;
    selectPerson.style.width = '100%';

    tdFullName.appendChild(selectPerson);

    // -- Column 2: Family Order --
    const tdFamilyOrder = document.createElement('td');
    const inputFamilyOrder = document.createElement('input');
    inputFamilyOrder.type = 'number';
    inputFamilyOrder.className = 'form-control text-center';
    inputFamilyOrder.name = 'family_order[]';
    inputFamilyOrder.min = '1';
    inputFamilyOrder.required = true;
    inputFamilyOrder.placeholder = 'Order';
    if (data) {
        inputFamilyOrder.value = data.family_order;
    }
    tdFamilyOrder.appendChild(inputFamilyOrder);

    // -- Column 3: Action --
    const tdAction = document.createElement('td');
    const btn = document.createElement('button');
    btn.className = 'btn btn-danger btn-sm';
    btn.style.borderRadius = "100%";
    btn.innerHTML = '<i class="fa fa-trash"></i>';
    btn.onclick = () => tr.remove();
    tdAction.appendChild(btn);

    // Assemble Row
    tr.appendChild(tdFullName);
    tr.appendChild(tdFamilyOrder);
    tr.appendChild(tdAction);

    const tableBody = document.getElementById('HousholdMemberList'); // Ensure this ID matches your HTML
    if (!tableBody) {
        console.error("Table body 'HousholdMemberList' not found");
        return;
    }
    tableBody.appendChild(tr);

    // -- Initialize Select2 --
    const $sel = $(`#${personSelectId}`);

    // Logic to handle existing data (Edit Mode)
    if (data && data.person_unique_id) {
        // IMPORTANT: If editing, we must inject the option manually
        // so Select2 can render the Text, not just the Value.
        // We assume data.full_name is passed in the 'data' object. 
        // If you don't have full_name in the initial payload, 
        // you must fetch it separately or use a placeholder.
        const text = data.full_name || 'Selected User'; 
        const newOption = new Option(text, data.person_unique_id, true, true);
        $sel.append(newOption);
    }

    $sel.select2({
        placeholder: 'Search personal name...',
        width: '100%',
        minimumInputLength: 0,
        ajax: {
            url: 'inputConfig.php',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    getAllPersonalRecords: true,
                    term: params.term || ''
                };
            },
            processResults: function (data) {
                // Handle Array or {status:200, data:[]} format
                const rows = (data.data) ? data.data : (Array.isArray(data) ? data : []);
                return {    
                    results: rows.map(r => ({
                        id: r.person_unique_id,
                        text: r.full_name
                    }))
                };
            },
        }
    });
    
    // Trigger change to update UI if we manually added the option
    if (data && data.person_unique_id) {
        $sel.trigger('change');
    }
}
// When marker is created, validate it is inside the main polygon boundary.
map.on('draw:created', function (e) {
    const layer = e.layer;
    const HouseHold = $("#household_coord");
    // Only handle markers (defensive)
    if (!layer || typeof layer.getLatLng !== 'function') return;

    // Validate: marker must be inside any of the loaded main boundary polygons.
    const markerLatLng = layer.getLatLng();
    if (!isPointInsideMainPolygons(markerLatLng)) {
        ClsAlert({
            icon: "info",
            title: "Marker must be inside the main polygon boundary."
        });
        // Remove invalid marker from map
        map.removeLayer(layer);
        return;
    }

    if(!ClsUnidentified(HouseHold.val())){

        ClsAlert({
            icon: "info",
            title: "A marker already exists. You can only save one."
        });
        return;
    }
    const coordArray = [markerLatLng.lat, markerLatLng.lng];
    // Accept marker
    drawnItems.addLayer(layer);
    layer.options.dbId = layer.options.dbId || null; // placeholder for backend usage
    HouseHold.val(coordArray);
});

map.on('draw:edited', function (evt) {
    // Leaflet Draw fires edited when marker position changes
    // Validate and persist the updated marker position in the hidden field.

    try {
        const layer = evt.layer || (evt.layers && evt.layers.getLayers && evt.layers.getLayers()[0]);
        if (!layer || typeof layer.getLatLng !== 'function') return;

        const HouseHold = $("#household_coord");
        const markerLatLng = layer.getLatLng(); // ✅ marker => single latlng

        // If marker is moved outside the allowed main polygon, block it
        if (!isPointInsideMainPolygons(markerLatLng)) {
            ClsAlert({
                icon: "info",
                title: "Marker must be inside the main polygon boundary."
            });

            // Best effort rollback: move back to the previous position if available
            if (layer.options && layer.options.previousLatLng) {
                layer.setLatLng(layer.options.previousLatLng);
            }
            return;
        }

        // Store latest accepted coordinates for rollback + hidden field
        layer.options.previousLatLng = markerLatLng; // ✅ store LatLng object
        const coordArray = [markerLatLng.lat, markerLatLng.lng];
        HouseHold.val(coordArray);
    } catch (err) {
        console.error(err);
    }
});
map.on('draw:deleted', function () {
    // Clear marker hidden value when user deletes marker
    const HouseHold = $("#household_coord");
    HouseHold.val('');
});

$(document).on("submit","#frmHouseHoldList",function(e){
    e.preventDefault();
    var formData = new FormData();

    if(ClsUnidentified($("#household_coord").val())){
        ClsAlert({
                icon: "info",
                title: "Please mark the location first"
            });
            return;
    }



    const HouseHoldMember = [];
    document.querySelectorAll('#HousholdMemberList tr').forEach(tr => {
        const person_unique_id = tr.querySelector('td:nth-child(1) select').value;
        const family_order = tr.querySelector('td:nth-child(2) input').value;
        HouseHoldMember.push({person_unique_id, family_order });
    });
    formData.append("houshold_id",$("#houshold_id").val())
    formData.append("household_coord",$("#household_coord").val())
    formData.append("household_number",$("#household_number").val())
    formData.append("purok_sitio_id",$("#purok_sitio_id").val())
    formData.append("household_member",JSON.stringify(HouseHoldMember));
    formData.append("addHouseHoldMember",true);
    fncExecute("inputConfig.php",formData,function(response, textStatus, jqXHR){
        const res = JSON.parse(response);  
        if(res.status === 200) {
            ClsAlert({ icon: "success", title: res.message });
            $("#frmHouseHoldList")[0].reset();
            $("#householdTable").DataTable().ajax.reload();
        }
        else {
            ClsAlert({ icon: "error", title: res.message });
        }
    },"POST")
})

$(document).on(`reset`, `#frmHouseHoldList`, function() {
    document.getElementById('HousholdMemberList').innerHTML = '';
    $("#frmHouseHoldList #btnReset").html(`<i class="fas fa-redo"></i> Reset`);
    $("#frmHouseHoldList #btnSubmit").html(`<i class="fas fa-home"></i> Add Household`);
    var combinedBounds = L.latLngBounds([]);
    mainBoundaryLayers.forEach(layer => combinedBounds.extend(layer.getBounds()));
    map.fitBounds(combinedBounds);
    drawnItems.clearLayers();
    $("#houshold_id").val("");
    $("#household_coord").val("");
})
$(document).on('click', '.deleteHouseHold', function() {
    deleteHouseHold($(this).attr('id'), $(this).attr('number'));
});
$(document).on('click', '.updateHouseHold', function() {
    updateHouseHold($(this).attr('id'));
});


function updateHouseHold(ID) {
    $("#frmHouseHoldList #btnReset").html(`<i class="fas fa-times"></i> Cancel`);
    $("#frmHouseHoldList #btnSubmit").html(`<i class="fas fa-edit"></i> Update Household`);
    loadMarkers(ID);
    fncExecute(`inputConfig.php?fetchHousholdInfo=true&HouseHoldId=${encodeURI(ID)}`,null,function(response){
        var res = JSON.parse(response);
        if (res.status === 200) {
            const data = res.data;
            FillDataFormFromDB({ form:"#frmHouseHoldList", data:data })
            document.getElementById('HousholdMemberList').innerHTML = '';
            data.HouseHoldMemberList.forEach(HouseHoldMemberList => {
                fncAddHouseHoldMembersBtn({data:HouseHoldMemberList})
            })
        }
    },"GET")
}
function deleteHouseHold(ID, HouseholdNumber) {
    const formData = new FormData();
    formData.append("deleteHouseHold", true);
    formData.append("deleteHouseHoldId", ID);
    ClsConfirmAlert({
        icon: "error",
        title: `Are you sure you want to delete the Household Number "${HouseholdNumber}"?`,
        confirmText: "Delete",
        cancelText: "Cancel",
        onConfirm: function() {
            fncExecute('inputConfig.php', formData, function(response, textStatus, jqXHR) {
                const res = JSON.parse(response);
                if (res.status === 200) {
                    ClsAlert({
                        icon: "success",
                        title: res.message
                    });
                    // ✅ safer: reload instead of reinit
                    $("#householdTable").DataTable().ajax.reload();
                    $("#frmHouseHoldList")[0].reset();

                } else {
                    ClsAlert({
                        icon: "error",
                        title: res.message
                    });
                }
            }, 'POST');
        }
});
}