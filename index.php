<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoLoc Attendance</title>
    <link rel ="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- // Google API key to get real time location data -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ-JTbsBf1D6yayoW3mGvNHo0aJja6ZFE&libraries=places"></script>
</head>
<body>

   
    <nav class="navbar">
        <img src="Group 34056.png" alt="Logo">
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">News</a></li>
        </ul>
        <div class="profile"><img src="Profile-PNG-File.png" alt="profile">
            <span>
                <?php if (isset($_SESSION['username'])): ?>
                <p><?php echo $_SESSION['username']; ?>!</p>
                 <?php else: ?>
                   <p>Welcome!</p>
                 <?php endif; ?>
            </span>
        </div>
       <a href="registration.php" style="list-style-type: none;"> <button style='font-size:18px '>Logout <i class='fas fa-power-off'> </i></button></a>
    </nav>
    <div class="container">
        <header>
            <img style="height: 150px;" src="Group 24.png" alt="Logo">
            <h1>GeoLoc Attendance</h1>
            <button id="checkOutBtn" onclick="checkOut()" class="hidden">Check Out</button>
        </header>

        <section id="userSection" class="hidden">
        <div id="userCard">
            <div class="user-avatar">
                <img src="Profile-PNG-File.png" alt="User Avatar">
            </div>
            <div class="user-info">
                <h2 id="userName">
                    <?php if (isset($_SESSION['username'])): ?>
                    <p><?php echo $_SESSION['username']; ?>!</p>
                    <?php else: ?>
                    <p>Welcome!</p>
                    <?php endif; ?>
                </h2>
                <p id="userEmail"><i class="fas fa-envelope"></i>
                    <?php if (isset($_SESSION['email'])): ?>
                    <?php echo $_SESSION['email']; ?>
                    <?php else: ?>
                    user@gmail.com
                    <?php endif; ?>
                </p>
                <p id="userLocation"><i class="fas fa-map-marker-alt"></i> Location: <span id="location"></span></p>
                <p id="sessionTime"><i class="fas fa-clock"></i> Session Time: <span id="sessionDuration"></span></p>
                <p id="activeStatus">
                    <i class="fas fa-toggle-on"></i> Active Status:
                    <button id="statusBtnYes" class="hidden">Yes</button>
                    <button id="statusBtnNo" class="hidden">No</button>
                </p>
            </div>
        </div>
    </section>


        <section id="actions">
            <button id="checkInBtn" onclick="checkIn()">Check In</button>
            <button style="display:none" id="notEmployeeBtn" onclick="notAnEmployee()">Contact Admin</button>
        </section>

        <section id="contacts">
            <h2>Contacts & Helpline</h2>
            <p><i class="fas fa-user-shield"></i> Contact Admin: <a href="mailto:admin@example.com">admin@GeoLoc.com</a></p>
            <p><i class="fas fa-phone-alt"></i> Helpline: <a href="tel:+1-234-567-890">+1-234-567-890</a></p>
        </section>

        <footer>
            <p>&copy; 2024 GeoLoc Attendance</p>
            <a href="#" style="list-style-type: none;">Terms & conditions</a>
        </footer>
    </div>
    <script>
    // Coordinates for the rectangular geofenced area (set by admin)
    const geoFence = {
        topLeft: { latitude: 12.0211, longitude: 77.5252 }, 
        bottomRight: { latitude: 11.2716, longitude: 77.6083 }
    };

    let sessionStartTime;
    let sessionTimer;

    function checkIn() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(validatePosition, showError, { enableHighAccuracy: true });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function validatePosition(position) {
        const { latitude, longitude } = position.coords;

        console.log(`User's latitude: ${latitude}`);
        console.log(`User's longitude: ${longitude}`);
        console.log(`Top Left corner: ${geoFence.topLeft.latitude}, ${geoFence.topLeft.longitude}`);
        console.log(`Bottom Right corner: ${geoFence.bottomRight.latitude}, ${geoFence.bottomRight.longitude}`);

        // Proceed with check-in  of location
        document.getElementById('userSection').classList.remove('hidden');
        document.getElementById('checkOutBtn').classList.remove('hidden');
        document.getElementById('checkInBtn').classList.add('hidden');
        document.getElementById('notEmployeeBtn').classList.add('hidden');
        document.getElementById('statusBtnYes').classList.remove('hidden');
        document.getElementById('statusBtnNo').classList.add('hidden');
        sessionStartTime = new Date();
        startTimer();

        document.getElementById('location').innerText = `Lat: ${latitude}, Lon: ${longitude}`;
    }
    // function isInsideRectangle(lat, lon, topLeft, bottomRight) {
    //     const isLatitudeInRange = lat >= bottomRight.latitude && lat <= topLeft.latitude;
    //     const isLongitudeInRange = lon >= topLeft.longitude && lon <= bottomRight.longitude;
    //     return isLatitudeInRange && isLongitudeInRange;
    // }
    // function validatePosition(position) {
    //     const { latitude, longitude } = position.coords;

    //     console.log(`User's latitude: ${latitude}`);
    //     console.log(`User's longitude: ${longitude}`);
    //     console.log(`Top Left corner: ${geoFence.topLeft.latitude}, ${geoFence.topLeft.longitude}`);
    //     console.log(`Bottom Right corner: ${geoFence.bottomRight.latitude}, ${geoFence.bottomRight.longitude}`);

    //     const isInside = isInsideRectangle(latitude, longitude, geoFence.topLeft, geoFence.bottomRight);
    //     console.log(`Is inside geofence: ${isInside}`);

    //     if (isInside) {
    //         // Proceed with check-in
    //     } else {
    //         alert("You are outside the allowed location. Please move within the designated area to check in.");
    //     }
    // }


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

    function endSession() {
        const sessionEndTime = new Date();
        const elapsed = Math.floor((sessionEndTime - sessionStartTime) / 1000);

        // Use toLocaleString() for Indian Standard Time (IST)
        const formattedSessionStartTime = sessionStartTime.toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });
        const formattedSessionEndTime = sessionEndTime.toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "end_session.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`duration=${elapsed}&sessionEndTime=${formattedSessionEndTime}&sessionStartTime=${formattedSessionStartTime}`);
    }




    function checkOut() {
        clearInterval(sessionTimer);
        endSession();  // Store the session data on check-out
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
</script>
</body>
</html>