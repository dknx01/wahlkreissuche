import $ from "jquery";
import {LocationUtils} from "../LocationUtils";
import {FieldDataExtractor} from "./FieldDataExtractor";

export class DistrictUtils extends FieldDataExtractor{
    static getDistrict(data) {
        const fields = ["borough", "suburb"];
        let district = FieldDataExtractor.getFieldData(fields, data);
        if (district === '') {
            let locationUtils = new LocationUtils(data);
            district = locationUtils.getCity();
        }
        return district;
    }

    static updateDistrict(district, selector) {
        if ($(`#${selector} option[value=${district}]`).length > 0) {
            $(`#${selector}`).val(district);
        } else {
            $(`#${selector}`)
                .append(`<option value="${district}" selected>${district}</option>`);
        }
    };
}