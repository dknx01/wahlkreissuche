import { Controller } from 'stimulus';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet';
import 'leaflet.markercluster';
import 'leaflet.featuregroup.subgroup';
import {Place} from '../js/plakate/Place';
import '../css/app.css';
import iconPathWish from '../images/wish_place.png';
import {Translation} from "../js/translations/translation";
import u from 'umbrellajs';
import axios from 'axios';

export default class extends Controller {
    static values = {
        url: String
    };
    initialize() {
        this.translator = new Translation({'locale': 'de'});
        super.initialize();
    }

    connect() {
        axios.get(this.urlValue)
            .then(data=>this.showMap(data))
            .catch(err=> alert(this.translator.trans('error.no-data-load')));
    }
    showMap(data) {
        u('.loader').remove();
        let map = L.map('map').setView([52.516215, 13.3922187], 11);

        let places = data.data.posters;
        let wahllokaldata = typeof data.data.wahllokale !== 'undefined' ?  data.data.wahllokale : [];
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            minZoom: 1,
        }).addTo(map);

        let place = new Place(
            L.markerClusterGroup(
                {
                    maxClusterRadius: 40,
                    iconCreateFunction: function(cluster) {
                        return L.divIcon({ html: '<span>' + cluster.getChildCount() + '</span>', iconSize: [32, 32], className: 'mycluster' });
                    },
                    polygonOptions: {
                        color: "grey"
                    },
                }
            ),
            map,
            L.control.layers(null, null, { collapsed: true })
        );
        place.placesCollection = places;
        place.parteiIcon = L.icon({
            iconSize: [32, 32],
            iconUrl: '/favicon.png'
        });
        place.parteiIconWish = L.icon({
            iconSize: [32, 44],
            iconUrl: iconPathWish
        });
        if (wahllokaldata.length > 0) {
            place.wahllokaleCollection(wahllokaldata);
        }
        place.generate();

        u(".leaflet-control-layers-overlays").prepend(`<b>${this.translator.trans('map.districts')}</b>`);
        return true;
    };
}
