import $ from "jquery";
import {LocationUtils} from "../LocationUtils";
import {FieldDataExtractor} from "./FieldDataExtractor";

export class CityUtils extends FieldDataExtractor{
    static getCity(data) {
        const fields = ["city", "town", "village", "state"];
        return FieldDataExtractor.getFieldData(fields, data);
    }

    static updateCity (city, selector) {
        if (city === '') {
            return;
        }

        if ($(`#${selector} option[value=${city}]`).length > 0) {
            $(`#${selector}`).val(city);
        } else {
            $(`#${selector}`)
                .append(`<option value="${city}" selected>${city}</option>`);
        }
    }
}