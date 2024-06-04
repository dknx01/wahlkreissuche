import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet';
import '../../../css/app.css';
import iconPath from '../../../images/Bottle-Caps-grey.png';
import iconSBahn from '../../../images/S-Bahn-Logo.svg';
import iconUBahn from '../../../images/U-Bahn_Berlin_logo.svg';
import iconTram from '../../../images/Tram-Logo.svg';
import iconBus from '../../../images/BUS-Logo-BVG.svg';
import iconPartei from '../../../images/favicon.png';
import iconParteiGrey from '../../../images/favicon-grey.png';
import Toastify from 'toastify-js/src/toastify';
import {Translation} from "../../translations/translation";

let translator = new Translation({'locale': 'de'});
let map = L.map('map_tour').setView([52.516215, 13.3922187], 11);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19,
    minZoom: 1,
}).addTo(map);

for (let place of places) {
    let html = `<div class="card" style="width: 18rem;">
              <div class="card-header"><strong>${place.name}</strong><br>${place.address}</div>`;
    html += `<div class="card-body"><h6 class="card-subtitle">${translator.trans('connection', 'wahllokaltour')}</h6><p class="card-text">`;
    for (let i in place.connections) {
        let conn = place.connections[i];
        html += `<span class="badge badge-secondary wahllokaltour_badge">${conn.name}</span>`;
        if (conn.sbahn.length > 0) {
            html += ` <img src="${iconSBahn}" alt="${translator.trans('s-train', 'wahllokaltour')}" class="wahllokaltour_oepnv">${conn.sbahn.join(', ')}<br>`;
        }
        if (conn.ubahn.length > 0) {
            html += ` <img src="${iconUBahn}" alt="${translator.trans('subway', 'wahllokaltour')}" class="wahllokaltour_oepnv">${conn.ubahn.join(', ')}<br>`;
        }
        if (conn.bus.length > 0) {
            html += ` <img src="${iconBus}" alt="${translator.trans('bus', 'wahllokaltour')}" class="wahllokaltour_oepnv">${conn.bus.join(', ')}<br>`;
        }
        if (conn.tram.length > 0) {
            html += ` <img src="${iconTram}" alt="${translator.trans('tram', 'wahllokaltour')}" class="wahllokaltour_oepnv">${conn.tram.join(', ')}<br>`;
        }
    }
    html += `<hr><i class="far fa-clock text-red"></i>${translator.trans('opening_hours', 'wahllokaltour', {data:place.opening_hours})}<br>`;
    html += `<hr>${translator.trans('meeting_point', 'wahllokaltour')} ${place.meeting_point ? `<img src="${iconFavIcon}" alt="ja">` : `<img src="${iconFavIconGrey}" alt="nein">`}`;
    html += '</p></div>';

    html += `<div class="card-footer">
        Punkte: <span class="badge btn-partei wahllokaltour_badge">${place.points}</span> <a href="${place.link}">${translator.trans('collecting_points', 'wahllokaltour')}</a>
    </div>
</div>`;
    let iconClass = 'wahllokaltour_place';
    if (place.points === 0) {
        iconClass = 'wahllokaltour_place_red';
    } else if (place.points < 10) {
        iconClass = 'wahllokaltour_place_white';
    }
    if (place.points === 10) {
        iconClass = 'wahllokaltour_place_bronce';
    }
    if (place.points === 15) {
        iconClass = 'wahllokaltour_place_silver';
    }
    if (place.points >= 25) {
        iconClass = 'wahllokaltour_place_gold';
    }
    L.marker([place.lat, place.lon], {title: place.name, icon: iconPath})
        .setIcon(
            L.divIcon({html: `<span>${place.points}</span>`, iconSize: [32, 32], className: iconClass}
            )
        )
        .bindPopup(html)
        .addTo(map);
}


for (let key in successToasts) {
    Toastify({
        text: `${successToasts[key]}`,
        duration: 5000,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "center", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "green",
        },
        escapeMarkup: false,
        onClick: function(){} // Callback after click
    }).showToast();
}

for (let key in errorToasts) {
    Toastify({
        text: `${errorToasts[key]}`,
        duration: 5000,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "center", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "red",
        },
        escapeMarkup: false,
        onClick: function(){} // Callback after click
    }).showToast();
}