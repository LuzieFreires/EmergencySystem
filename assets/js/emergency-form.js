// Global variables
let map;
let emergencyMarkers = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing emergency form...');
    updateAlertHistory();
});

function setupEmergencyForm() {
    const form = document.querySelector('#emergency-form');
    if (form) {
        form.addEventListener('submit', handleEmergencySubmit);
    }
}

function initializeMap() {
    console.log('Initializing emergency map...');
    try {
        const defaultLocation = [14.5995, 120.9842]; // Default to Manila
        map = L.map('emergencyMap').setView(defaultLocation, 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Get user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const { latitude, longitude } = position.coords;
                    map.setView([latitude, longitude], 13);
                    // Add a marker for the user's location
                    L.marker([latitude, longitude]).addTo(map)
                        .bindPopup('Your Location')
                        .openPopup();
                    // Update the location input
                    document.getElementById('location').value = `${latitude}, ${longitude}`;
                },
                error => console.error('Location error:', error)
            );
        }

        // Add click event to update location
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            // Clear existing markers
            map.eachLayer((layer) => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
            // Add new marker
            L.marker([lat, lng]).addTo(map)
                .bindPopup('Selected Location')
                .openPopup();
            // Update the location input
            document.getElementById('location').value = `${lat}, ${lng}`;
        });

        // Force a refresh of the map
        setTimeout(() => {
            map.invalidateSize();
        }, 100);

        console.log('Emergency map initialized successfully');
    } catch (error) {
        console.error('Error initializing emergency map:', error);
    }
}

async function handleEmergencySubmit(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Add location coordinates
    const marker = emergencyMarkers[0];
    if (marker) {
        formData.append('latitude', marker.getLatLng().lat);
        formData.append('longitude', marker.getLatLng().lng);
    }

    fetch('../api/save_alert.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Emergency reported successfully');
            window.location.href = 'dashboard.php';
        } else {
            alert(data.message || 'Failed to report emergency');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while reporting the emergency');
    });
}

function notifyResponders() {
    // Use BroadcastChannel for real-time updates
    const channel = new BroadcastChannel('emergency_alerts');
    channel.postMessage({
        type: 'new_emergency',
        timestamp: new Date().toISOString()
    });
}

function showSuccess(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = message;
    insertAlert(alertDiv);
}

function showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger';
    alertDiv.textContent = message;
    insertAlert(alertDiv);
}

function insertAlert(alertDiv) {
    const container = document.querySelector('.emergency-form');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        alertDiv.classList.add('fade-out');
        setTimeout(() => alertDiv.remove(), 500);
    }, 5000);
}

function refreshActiveEmergencies() {
    fetch('../api/get_active_emergencies.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('activeEmergencies');
                container.innerHTML = ''; // Clear current list
                
                data.emergencies.forEach(emergency => {
                    container.appendChild(createEmergencyCard(emergency));
                });
            }
        })
        .catch(error => console.error('Error fetching emergencies:', error));
}

function createEmergencyCard(emergency) {
    const card = document.createElement('div');
    card.className = `emergency-card severity-${emergency.severity}`;
    card.innerHTML = `
        <div class="emergency-header">
            <h3>${escapeHtml(emergency.type)}</h3>
            <span class="badge severity-badge">
                ${escapeHtml(emergency.severity.charAt(0).toUpperCase() + emergency.severity.slice(1))}
            </span>
        </div>
        <div class="emergency-details">
            <p><strong>Location:</strong> ${escapeHtml(emergency.location)}</p>
            <p><strong>Reported by:</strong> ${escapeHtml(emergency.reporter_name)}</p>
            <p><strong>Time:</strong> ${formatDate(emergency.created_at)}</p>
            <p class="emergency-description">${escapeHtml(emergency.description)}</p>
        </div>
        <div class="emergency-actions">
            <button class="btn btn-primary respond-btn" data-emergency-id="${emergency.id}">
                Respond
            </button>
        </div>
    `;
    return card;
}

// Helper functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

// Refresh every 30 seconds
setInterval(refreshActiveEmergencies, 30000);

// Initial load
document.addEventListener('DOMContentLoaded', refreshActiveEmergencies);

async function updateAlertHistory() {
    try {
        const response = await fetch('../api/get_alerts.php');
        if (!response.ok) throw new Error('Failed to fetch alerts');
        const alerts = await response.json();
        console.log('Fetched alerts:', alerts);
    } catch (error) {
        console.error('Error updating alert history:', error);
    }
}

async function updateAlertHistory() {
    try {
        const response = await fetch('../api/get_alerts.php');
        const alerts = await response.json();
        
        const historyContainer = document.querySelector('#alert-history');
        if (historyContainer) {
            historyContainer.innerHTML = alerts.map(alert => `
                <div class="alert-history-item alert-${alert.type}">
                    <span class="alert-time">${new Date(alert.created_at).toLocaleString()}</span>
                    <span class="alert-message">${alert.message}</span>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Failed to update alert history:', error);
    }
}

// Initialize alert history on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing emergency form...');
    setupEmergencyForm();
    initializeMap();
    updateAlertHistory();
});
