let id, target, options;

function success(pos) {
        const crd = pos.coords;

        if (target.latitude === crd.latitude && target.longitude === crd.longitude) {
            navigator.geolocation.clearWatch(id);
        }
    }

function getLocation() {

        target = {
            latitude : 0,
            longitude: 0
        };

        options = {
            enableHighAccuracy: false,
            timeout: 5000,
            maximumAge: 0
        };

        id = navigator.geolocation.watchPosition(
            success,
            error, options
        );
    }

function error(err) {
            console.warn('ERROR(' + err.code + '): ' + err.message);
    }

getLocation();