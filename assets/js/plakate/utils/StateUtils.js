import $ from "jquery";
import {LocationUtils} from "../LocationUtils";
import {FieldDataExtractor} from "./FieldDataExtractor";

export class StateUtils extends FieldDataExtractor{
    static getState(data) {
        let fields = ["state"];
        let state = FieldDataExtractor.getFieldData(fields, data);
        if (state === '') {
            let iso_3166_2_code = data['ISO3166-2-lvl4'] || '';
            if (iso_3166_2_code !== '') {
                state = StateUtils.iso_3166_2_to_name(iso_3166_2_code);
            }
        }
        if (state === '') {
            fields = ["city", "town", "village"];
            state = FieldDataExtractor.getFieldData(fields, data);
        }
        return state;
    }

    static updateState (state, selector) {
        if ($(`#${selector} option[value=${state}]`).length > 0) {
            $(`#${selector}`).val(state);
        } else {
            $(`#${selector}`)
                .append(`<option value="${state}" selected>${state}</option>`);
        }
    }

    static iso_3166_2_to_name (code) {
        switch (code.toUpperCase()) {
            case 'DE-BW': return 'Baden-Württemberg';
            case 'DE-BY': return 'Bayern';
            case 'DE-BE': return 'Berlin';
            case 'DE-BB': return 'Brandenburg';
            case 'DE-HB': return 'Bremen';
            case 'DE-HH': return 'Hamburg';
            case 'DE-HE': return 'Hessen';
            case 'DE-MV': return 'Mecklenburg-Vorpommern';
            case 'DE-NI': return 'Niedersachsen';
            case 'DE-NW': return 'Nordrhein-Westfalen';
            case 'DE-RP': return 'Rheinland-Pfalz';
            case 'DE-SL': return 'Saarland';
            case 'DE-SN': return 'Sachsen';
            case 'DE-ST': return 'Sachsen-Anhalt';
            case 'DE-SH': return 'Schleswig-Holstein';
            case 'DE-TH': return 'Thüringen';
            default: return '';
        }
    }
}