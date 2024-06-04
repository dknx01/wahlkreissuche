export function getStyle(key) {
    switch (key) {
        case '01':
        case 75:
        case 'Mitte':
            return {
                color: "#cb0606",
                weight: 1,
                //fillColor: "#232222"
            };
        case '02':
        case 83:
        case 'Friedrichshain-Kreuzberg':
            return {
                color: "#7878c9",
                weight: 1,
            };
        case '03':
        case 76:
        case 'Pankow':
            return {
                color: "#d29208",
                weight: 1,
            };
        case '04':
        case 80:
        case 'Charlottenburg-Wilmersdorf':
            return {
                color: "#2faf03",
                weight: 1,
            };
        case '05':
        case 78:
        case 'Spandau':
            return {
                color: "#0aceb4",
                weight: 1,
            };
        case '06':
        case 79:
        case 'Steglitz-Zehlendorf':
            return {
                color: "#a6a6f5",
                weight: 1,
            };
        case '07':
        case 81:
        case 'Tempelhof-Schöneberg':
            return {
                color: "#595555",
                weight: 1,
            };
        case '08':
        case 82:
        case 'Neukölln':
            return {
                color: "#54a89b",
                weight: 1,
            };
        case '09':
        case 84:
        case 'Treptow-Köpenick':
            return {
                color: "#c675da",
                weight: 1,
            };
        case '10':
        case 85:
        case 'Marzahn-Hellersdorf':
            return {
                color: "#d0ca56",
                weight: 1,
            };
        case '11':
        case 86:
        case 'Lichtenberg':
            return {
                color: "#a3b279",
                weight: 1,
            };
        case '12':
        case 77:
        case 'Reinickendorf':
            return {
                color: "#80379d",
                weight: 1,
            };
        default:
            return {
                color: '#4d4d4d',
                fill: '#775e5e'
            };
    }
}