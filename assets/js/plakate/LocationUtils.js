import {DistrictUtils} from "./utils/DistrictUtils";
import {CityUtils} from "./utils/CityUtils";
import {StateUtils} from "./utils/StateUtils";
import {StateLazyObject} from "./valueObjects/StateLazyObject";
import {CityLazyObject} from "./valueObjects/CityLazyObject";
import {DistrictLazyObject} from "./valueObjects/DistrictLazyObject";

export class LocationUtils {
    constructor(address) {
        this.state = new StateLazyObject(StateUtils.getState, address);
        this.city = new CityLazyObject(CityUtils.getCity, address);
        this.district = new DistrictLazyObject(DistrictUtils.getDistrict, address);
    }
    getDistrict() {
        return this.district.value();
    }

    updateDistrict(selector) {
        DistrictUtils.updateDistrict(this.getDistrict(), selector);
    }

    getCity() {
        return this.city.value();
    }

    updateCity(selector) {
        CityUtils.updateCity(this.getCity(), selector);
    }
    getState() {
        return this.state.value();
    }

    updateState(selector) {
        StateUtils.updateState(this.getState(), selector);
    }
}