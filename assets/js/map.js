import $ from "jquery";
import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";
import OSM from "ol/source/OSM";
import {fromLonLat} from 'ol/proj';

// import Map from 'ol/Map.js';
// import View from 'ol/View.js';
// import TileLayer from 'ol/layer/Tile.js';
// import OSM from 'ol/source/OSM.js';

// var osm = new TileLayer({
//     source: new OSM(),
//     visible: false
// });
// var map = new Map({
//     layers: [osm],
//     target: 'map',
//     view: new View({
//         center: [0, 0],
//         zoom: 2
//     })
// });
var map = new Map({
    target: 'map',
    layers: [
        new TileLayer({
            source: new OSM()
        })
    ],
    view: new View({
        center: fromLonLat([13.3922187, 52.516215]),
        maxZoom: 18,
        zoom: 11
    })
});
