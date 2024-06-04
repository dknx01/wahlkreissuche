import Polyglot from "node-polyglot";
export class Translation {
    constructor() {
        this.polyglot = new Polyglot();
        this.loadTranslations();
    }

    loadTranslations() {
        this.polyglot.extend({
                'generic': require('./data/generic.de.json'),
                'wahllokaltour': require('./data/wahllokaltour.de.json'),
            }
        );
    }

    trans(key, domain = 'generic', options) {
        return this.polyglot.t(`${domain}.${key}`, options || {} );
    }
}