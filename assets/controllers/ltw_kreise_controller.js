import { Controller } from 'stimulus';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet';
import {getStyle} from '../js/leaflet/wahlkreise/agh_styles';
import '../css/app.css';
import {Translation} from "../js/translations/translation";
import u from 'umbrellajs';
import axios from 'axios';

export default class extends Controller {
    static values = {
        url: String,
        mapconfig: String
    };
    initialize() {
        this.translator = new Translation({'locale': 'de'});
        super.initialize();
        this.mapConfig = JSON.parse(this.mapconfigValue);
    }

    connect() {
        axios.get(this.urlValue)
            .then(data=>this.showMap(data))
            .catch((err)=> {
                alert(this.translator.trans('error.no-data-load'));
                console.log(err);
            });
    }
    showMap(data) {
        let onEachFeature = function (feature, layer) {
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
        };
        u('.loader').remove();
        let map = L.map('map').setView([this.mapConfig.lat, this.mapConfig.lon], this.mapConfig.zoom);

        let kreise = data.data;
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
        return true;
    };
}
