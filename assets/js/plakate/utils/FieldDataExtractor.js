export class FieldDataExtractor {
    static getFieldData(fields, data, defaultValue = '') {
        let value = defaultValue;
        let key = 0;
        while (value === '' && key < fields.length) {
            let field = fields[key];
            value = data[field] || '';
            key++;
        }
        return value;
    }
}
