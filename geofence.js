let map, geocoder;

function initMap() {
    geocoder = new google.maps.Geocoder();
}

function checkIn() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;
            validatePosition(latitude, longitude);
            getLocationName(latitude, longitude);
        }, showError, { enableHighAccuracy: true });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function getLocationName(lat, lon) {
    const latLng = { lat, lng: lon };

    geocoder.geocode({ location: latLng }, (results, status) => {
        if (status === "OK") {
            if (results[0]) {
                document.getElementById('location').innerText = results[0].formatted_address;

                // Get geofencing coordinates based on the location name (results[0].formatted_address)
                getGeoFenceCoordinates(results[0].formatted_address);
            } else {
                alert("No results found");
            }
        } else {
            alert("Geocoder failed due to: " + status);
        }
    });
}

function getGeoFenceCoordinates(locationName) {
    // Use geocoding API to fetch topLeft and bottomRight coordinates based on locationName
    geocoder.geocode({ address: locationName }, (results, status) => {
        if (status === "OK") {
            const bounds = results[0].geometry.bounds;

            if (bounds) {
                geoFence.topLeft = {
                    latitude: bounds.getNorthEast().lat(),
                    longitude: bounds.getSouthWest().lng(),
                };
                geoFence.bottomRight = {
                    latitude: bounds.getSouthWest().lat(),
                    longitude: bounds.getNorthEast().lng(),
                };

                console.log(`Top Left: ${geoFence.topLeft.latitude}, ${geoFence.topLeft.longitude}`);
                console.log(`Bottom Right: ${geoFence.bottomRight.latitude}, ${geoFence.bottomRight.longitude}`);
            } else {
                alert("Bounds not available for the location.");
            }
        } else {
            alert("Geocoder failed due to: " + status);
        }
    });
}

function validatePosition(lat, lon) {
    if (isInsideRectangle(lat, lon, geoFence.topLeft, geoFence.bottomRight)) {
        document.getElementById('userSection').classList.remove('hidden');
        document.getElementById('checkOutBtn').classList.remove('hidden');
        document.getElementById('checkInBtn').classList.add('hidden');
        document.getElementById('notEmployeeBtn').classList.add('hidden');
        document.getElementById('statusBtnYes').classList.remove('hidden');
        document.getElementById('statusBtnNo').classList.add('hidden');

        sessionStartTime = new Date();
        startTimer();
    } else {
        alert("You are outside the allowed location. Please move within the designated area to check in.");
    }
}

function startTimer() {
    sessionTimer = setInterval(() => {
        const now = new Date();
        const elapsed = Math.floor((now - sessionStartTime) / 1000);
        document.getElementById('sessionDuration').innerText = formatTime(elapsed);
    }, 1000);
}

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}m ${secs}s`;
}

function checkOut() {
    clearInterval(sessionTimer);
    document.getElementById('checkInBtn').classList.remove('hidden');
    document.getElementById('checkOutBtn').classList.add('hidden');
    document.getElementById('statusBtnYes').classList.add('hidden');
    document.getElementById('statusBtnNo').classList.remove('hidden');
    document.getElementById('sessionDuration').innerText += ' (Session Ended)';
}

function notAnEmployee() {
    alert('Please contact admin.');
}

function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            alert("Permission to access location was denied. Please enable location permissions and try again.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable. Please ensure your device's location services are enabled.");
            break;
    }
}

// Initialize the map and geocoder when the page loads
document.addEventListener("DOMContentLoaded", initMap);
