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
    );

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
  ).addTo(map);

    // Layer control
    var baseMaps = {
        "Google Roadmap": googleRoadmap,
        "Google Satellite": googleSat,
        "Google Hybrid": googleHybrid,
         "OpenStreetMap": osm
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


export const LeafLetRemoveBackGround = () => {
var world = [
  [[-90, -180], [-90, 180], [90, 180], [90, -180]]
];
return world;

}