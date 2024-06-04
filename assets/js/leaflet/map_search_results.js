import $ from 'jquery/dist/jquery';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet/dist/leaflet';
// import 'leaflet.markercluster/dist/leaflet.markercluster';
import '../../css/app.css';

let map = L.map('map-rs').setView([52.516215, 13.3922187], 10);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 20,
    minZoom: 1,
    // tileSize: 512,
    // zoomOffset: -1
}).addTo(map);

const getUrl = window.location;
const baseUrl = getUrl.protocol + "//" + getUrl.host + "/";
let parteiIcon = L.icon({
    iconSize: [32, 32],
    iconUrl: baseUrl + 'marker.png'
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
// let markers = L.markerClusterGroup(
//     {
//         maxClusterRadius: 40,
//         iconCreateFunction: function(cluster) {
//             return L.divIcon({ html: '<span>' + cluster.getChildCount() + '</span>', iconSize: [32, 32], className: 'mycluster' });
//         },
//         polygonOptions: {
//             color: "grey"
//         },
//         // spiderfyDistanceMultiplier: 2
//     }
// );
searchResults.forEach(function (searchResult) {
    let html = '<div class="card" style="width: 18rem;">\n' +
        '  <div class="card-body">\n' +
        '    <h6 class="card-subtitle">' + searchResult.agh_bezirk + '</h6>\n' +
        '    <p class="card-text">' + searchResult.address +
        '<h6>Bezirk: <span class="badge badge-secondary">' + searchResult.agh_bezirk + '</span></h6>'+
        '<h6>Wahlkreis: <span class="badge badge-secondary">' + searchResult.agh_wk + '</span></h6>'+
        '</p>\n' +
        '</div>';
    let title = `${searchResult.address} ${searchResult.agh_bezirk}`;
        L.marker([searchResult.lat, searchResult.lon], {icon: parteiIcon, title: title})
        .bindPopup(html)
            .addTo(map);
});