import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet/dist/leaflet';
import 'leaflet.markercluster/dist/leaflet.markercluster';
import '../../css/app.css';

let map = L.map('map').setView([52.516215, 13.3922187], 11);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 20,
    minZoom: 1,
    // tileSize: 512,
    // zoomOffset: -1
}).addTo(map);

const getUrl = window.location;
const baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
let parteiIcon = L.icon({
    iconSize: [32, 32],
    iconUrl: 'favicon.png'
});

// places.forEach(function (place) {
//     L.marker([place.lat, place.long], {icon: parteiIcon})
//         .bindPopup(place.desc)
//         .addTo(map);
// });
// let marker = L.marker([52.5482578, 13.4126463], {icon: parteiIcon})
//     .bindPopup('Ein Plakat?<br><a href="">Ein Link</a>')
//     .addTo(map);

// clusters
let markers = L.markerClusterGroup(
    {
        maxClusterRadius: 40,
        iconCreateFunction: function(cluster) {
            return L.divIcon({ html: '<span>' + cluster.getChildCount() + '</span>', iconSize: [32, 32], className: 'mycluster' });
        },
        polygonOptions: {
            color: "grey"
        },
        // spiderfyDistanceMultiplier: 2
    }
);
places.forEach(function (place) {
    let html = '<div class="card" style="width: 18rem;">\n' +
        '  <div class="card-body">\n' +
        '    <h6 class="card-subtitle">' + place.district + '</h6>\n' +
        '    <p class="card-text">' + (place.desc.length === 0 ? 'keine Beschreibung': place.desc) +
        '  <h6>Erstellt am: <span class="badge badge-secondary">' + place.createdAt + '</span></h6>' +
        '</p>\n' +
        '  </div>\n' +
        '  <div class="card-footer">' +
        '    <a href="#" class="card-link">Card link</a>\n' +
        '    <a href="#" class="card-link">Another link</a>\n' +
        '    </div>';
        '</div>';
    markers.addLayer(
        L.marker([place.lat, place.long], {icon: parteiIcon, title: place.desc})
        .bindPopup(html)
    );
});
map.addLayer(markers);