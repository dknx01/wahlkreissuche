import $ from 'jquery';
import Cookies from 'js-cookie';

class CookieHandler {
    static ensureCookie() {
        if (Cookies.get('wlt_Name') !== undefined) {
            let selector = 'tour_points_name';
            let name = Cookies.get('wlt_Name');
            if ($(`#${selector} option[value=${name}]`).length > 0) {
                $(`#${selector}`).val(name);
            }
            Cookies.set('wlt_Name', name, { expires: 7 });
        }
    }

    static newValue = function () {
        let selector = 'tour_points_name';
        let name = $(`#${selector} option:selected`).val();
        Cookies.set('wlt_Name', name, { expires: 7 });
    };
}

$(document).ready(function() {
    CookieHandler.ensureCookie();
    $("#tour_points_name").on('change', CookieHandler.newValue);
});