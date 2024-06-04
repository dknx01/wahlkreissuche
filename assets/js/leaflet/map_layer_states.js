import $ from 'jquery/dist/jquery';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet';
import 'leaflet.markercluster';
import 'leaflet.featuregroup.subgroup';
import {Place} from '../plakate/Place';
import '../../css/app.css';
import {Translation} from "../translations/translation";

let translator = new Translation({'locale': 'de'});

let map = L.map('map').setView([mapCenter.lat, mapCenter.lon], mapCenter.zoom);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 20,
    minZoom: 1,
}).addTo(map);

$.get(`/api/data/places/${state}`, function (data) {
    $('.loader').remove();
    let places = data;
    let wahllokaldata = [];
    let place = new Place(
        L.markerClusterGroup(
            {
                maxClusterRadius: 40,
                iconCreateFunction: function (cluster) {
                    return L.divIcon({
                        html: '<span>' + cluster.getChildCount() + '</span>',
                        iconSize: [32, 32],
                        className: 'mycluster'
                    });
                },
                polygonOptions: {
                    color: "grey"
                },
            }
        ),
        map,
        L.control.layers(null, null, {collapsed: true})
    );
    place.placesCollection = places;
    place.parteiIcon = L.icon({
        iconSize: [32, 32],
        iconUrl: '/favicon.png'
    });
    if (wahllokaldata.length > 0) {
        place.wahllokaleCollection(wahllokaldata);
    }
    place.generate();

    $(".leaflet-control-layers-overlays").prepend(`<b>${translator.trans('map.places')}</b>`);
}).fail(function () {
    alert(translator.trans('error.no-data-load'));
});