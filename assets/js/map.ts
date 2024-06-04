// import $ from "jquery";
import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";
import OSM from "ol/source/OSM";
import {ATTRIBUTION} from "ol/source/OSM";
import {fromLonLat} from 'ol/proj';
import {Attribution, defaults} from "ol/control";

const attribution = new Attribution({
    collapsible: false
});

const osm = new TileLayer({
    source: new OSM(
        {
            attributions: [ ATTRIBUTION, 'Tiles courtesy of <a href="https://geo6.be/">GEO-6</a>' ],
            maxZoom: 18,
           // url: "https://maps.wikimedia.org/osm-intl/${z}/${x}/${y}.png",
            crossOrigin: null
        }
    )
});

const map = new Map({
    controls: defaults({
        attribution: false,
    }).extend([attribution]),
    layers: [osm],
    target: 'map',
    view: new View({
        center: fromLonLat([13.3922187, 52.516215]),
        maxZoom: 18,
        zoom: 11
    })
});
places.
// map.addLayer(
//   new OSM(
//       {
//           attributions: "&copy; <a href='http://www.openstreetmap.org/'>OpenStreetMap</a> and contributors, under an <a href='http://www.openstreetmap.org/copyright' title='ODbL'>open license</a>. <a href='https://www.mediawiki.org/wiki/Maps'>Wikimedia's new style (beta)</a>",
//           crossOrigin: null,
//           url: "https://maps.wikimedia.org/osm-intl/${z}/${x}/${y}.png"
//       }
//   )
// );


