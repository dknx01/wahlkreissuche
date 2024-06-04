import $ from 'jquery';
export class MiniMap {
    load(minimapData) {
        let element = $(minimapData.div);
        if (element.is(':empty')) {
            element.load(minimapData.path);
        }

    }
}