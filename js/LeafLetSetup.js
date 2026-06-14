export const LeafLets = () => {
    // Initialize the map
    var map = L.map('map').setView([9.9833, 122.8167], 13);
    // Google Roadmap
    var googleRoadmap = L.tileLayer(
        'https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
        attribution: '© Google',
        maxZoom: 20
        }
    );

    // Google Satellite
    var googleSat = L.tileLayer(
        'https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '© Google',
        maxZoom: 20
        }
    ).addTo(map);

    // Google Hybrid
    var googleHybrid = L.tileLayer(
        'https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        attribution: '© Google',
        maxZoom: 20
        }
    );

    var osm = L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors',
      maxZoom: 19
    }
  );

    // Layer control
    var baseMaps = {
        "Google Roadmap": googleRoadmap,
        "Google Hybrid": googleHybrid,
        "OpenStreetMap": osm,
        "Google Satellite": googleSat

    };
    L.control.layers(baseMaps).addTo(map);

    var kabankalanBounds = L.latLngBounds([9.95, 122.78], [10.02, 122.85]);
    map.setMaxBounds(kabankalanBounds);
    map.setMinZoom(12);
    map.on('drag', function () {
        map.panInsideBounds(kabankalanBounds, { animate: true });
    });
    return map;
}
export const LeafLetDrawnItems = () => {
    var drawnItems = new L.FeatureGroup();
    return drawnItems;  
}

export const LeafLetDrawControl = (map,drawnItems) => {

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
    return drawControl;
}

// Creates a small helper to capture marker coordinates when markers are created/dragged.
// Usage:
//   setupDraggableMarkerCoordCapture(map, drawnItems, (coords, marker) => { ... })
export const setupDraggableMarkerCoordCapture = (map, drawnItems, onCoords) => {
    map.on(L.Draw.Event.CREATED, function (e) {
        const layer = e.layer;

        // Only handle marker layers
        if (typeof layer.getLatLng !== 'function') return;

        // Enable draggable (Leaflet.Draw marker is often not draggable by default)
        layer.options = layer.options || {};
        layer.options.draggable = true;

        // Make draggable for marker layers.
        // Leaflet.Draw-created marker usually already supports dragging, so enabling handler is enough.
        if (layer.dragging && typeof layer.dragging.enable === 'function') {
            layer.dragging.enable();
        }

        // Initial placement coords
        const coords = layer.getLatLng();
        if (typeof onCoords === 'function') {
            onCoords({ lat: coords.lat, lng: coords.lng }, layer);
        }

        // Updated coords after drag
        layer.on('dragend', function (ev) {
            const ll = ev.target.getLatLng();
            if (typeof onCoords === 'function') {
                onCoords({ lat: ll.lat, lng: ll.lng }, ev.target);
            }
        });

        // Keep in editable layers
        if (drawnItems && typeof drawnItems.addLayer === 'function') {
            drawnItems.addLayer(layer);
        }
    });
};



export const LeafLetRemoveBackGround = () => {
var world = [
  [[-90, -180], [-90, 180], [90, 180], [90, -180]]
];
return world;

}

