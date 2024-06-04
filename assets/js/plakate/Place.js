import 'leaflet.markercluster';
import 'leaflet.featuregroup.subgroup';
import wishIcon from '../../images/wish_place.png';

export class Place {
    districts = {};
    wahllokale = [];
    parteiIcon;
    parteiIconWish;

    constructor(markers, map, control) {
        this.markers = markers;
        this.map = map;
        this.control = control;
        this.wahllokaleFeatureGroup = L.featureGroup.subGroup();
    }

    set parteiIcon(icon) {
        this.parteiIcon = icon;
    }

    set parteiIconWish(icon) {
        this.parteiIconWish = icon;
    }

    set placesCollection(places) {
        this.places = places;
    }
    wahllokaleCollection(wahllokale) {
        this.wahllokale = wahllokale;
    }
    *getPlaces() {
        for (const place of Object.entries(this.places)) {
            yield place;
        }
    }
    *getWahllokale() {
        for (const wahllokal of Object.entries(this.wahllokale)) {
            yield wahllokal;
        }
    }
    *getDistricts() {
        for (const district of Object.entries(this.districts)) {
            yield district;
        }
    }

    generate() {
        this.markers.addTo(this.map);
        this.processDistricts();
        this.processDistrictEntries();
        if (this.wahllokale.length > 0) {
            this.processWahllokale();
        }
        this.control.addTo(this.map);
    }

    processDistricts() {
        for (let [key, value] of this.getPlaces()) {
            this.districts['group'+key] = {
                'name': key === '' ? 'keine Angabe' : key,
                'featureGroup': L.featureGroup.subGroup(this.markers),
                'positions': value
            };
        }
    }

    processDistrictEntries() {
        for (let [key, district] of this.getDistricts()) {
            for (let i in district.positions) {
                let place = district.positions[i];
                let html = this.html(place);
                let icon = this.parteiIcon;
                if (district.name === "Wunschorte") {
                    icon = this.parteiIconWish;
                }
                let marker = L.marker([place.lat, place.long], {icon: icon, title: place.desc})
                    .bindPopup(html);
                marker.addTo(district.featureGroup);
            }
            this.control.addOverlay(district.featureGroup, district.name);
            district.featureGroup.addTo(this.map);
        }
    }

    html(place) {
        if (Object.hasOwn(place, 'thumbnail') && (place.thumbnail.length > 0)) {
            let image = `<img src="data:image/jpg;base64, ${place.thumbnail}" alt="thumbnail of the poster" class="thumbnail-map"/>`;

            return `<div class="card" style="width: 18rem;">
               <div class="row g-0">
                  <div class="col-md-4">
                    ${image}
                  </div>
                  <div class="col-md-8">
                      <div class="card-body">
                        <h6 class="card-subtitle">${place.district}</h6>
                        <p class="card-text">${place.desc.length === 0 ? 'keine Beschreibung' : place.desc}
                          <h6><i class="far fa-calendar-alt" title="Erstellt am"></i> <span class="badge badge-secondary">${place.createdAt}</span></h6>
                        </p>
                      </div>
                  </div>
               </div>
               <div class="row g-0"><div class="col-md-12"><div class="card-footer">
                       <a href="${place.edit}" class="card-link">Bearbeiten</a>
                     </div></div>
               </div>
`;
        }
        return `<div class="card" style="width: 18rem;">
              <div class="card-body">
                <h6 class="card-subtitle">${place.district}</h6>
                <p class="card-text">${place.desc.length === 0 ? 'keine Beschreibung' : place.desc}
                  <h6><i class="far fa-calendar-alt" title="Erstellt am"></i> <span class="badge badge-secondary">${place.createdAt}</span></h6>
                </p>
              </div>
             <div class="card-footer">
               <a href="${place.edit}" class="card-link">Bearbeiten</a>
                </div>
        </div>`;
    }

    processWahllokale() {
        for (let [key, wahllokal] of this.getWahllokale()) {
            this.addWahllokal(wahllokal);
        }

        this.control.addOverlay(this.wahllokaleFeatureGroup, 'Wahllokale');
        this.wahllokaleFeatureGroup.addTo(this.map);
    }

    addWahllokal (wahllokal) {
        let lat = typeof wahllokal.lat !== 'undefined' ? wahllokal.lat : wahllokal.latitude;
        let long = typeof wahllokal.long !== 'undefined' ? wahllokal.long : wahllokal.longitude;
        let circle = L.circle([lat, long], {
            radius: wahllokal.radius,
            fillColor: 'rgba(255,42,0,0.63)',
            fillOpacity: 0.5,
            color: 'rgb(166,166,166)'
        });
        circle.bindTooltip(wahllokal.description);
        circle.addTo(this.wahllokaleFeatureGroup);
    }
}