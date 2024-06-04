import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet/dist/leaflet';
import {getStyle} from './wahlkreise/agh_styles';
import '../../css/app.css';
import $ from "jquery";
import {Translation} from "../translations/translation";
let translator = new Translation({'locale': 'de'});


let map = L.map('map').setView([52.516215, 13.3922187], 11);

$.get('/api/data/agh_kreise', function (data) {
    $('.loader').remove();
    let kreise = JSON.parse(data);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 20,
        minZoom: 1,
        // tileSize: 512,
        // zoomOffset: -1
    }).addTo(map);
    L.geoJSON(
        kreise,
        {
            onEachFeature: onEachFeature,
            style: function (feature) {
                return getStyle(feature.properties.BEZ);
            }
        }
    )
        .addTo(map);

}).fail(function () {
    alert(translator.trans('error.no-data-load'));
});

function onEachFeature(feature, layer) {
    layer.on('mouseover mousemove', function (e) {
        e.target.closePopup();
        L.popup({closeButton: false})
            .setContent(`${feature.properties.description}`)
            .setLatLng(e.latlng)
            .openOn(map);
    });

    layer.on('mouseout', function(e) {
        e.target.closePopup();
    });
}
