document.addEventListener('DOMContentLoaded', function() {
    // Update responder status
    const updateStatusBtn = document.getElementById('updateStatus');
    const statusSelect = document.getElementById('availabilityStatus');

    if (updateStatusBtn) {
        updateStatusBtn.addEventListener('click', function() {
            const status = statusSelect.value;
            updateResponderStatus(status);
        });
    }

    // Location tracking
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(updateLocation, handleLocationError, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    }

    // Refresh active emergencies periodically
    setInterval(refreshEmergencies, 30000);
});

function updateResponderStatus(status) {
    fetch('../api/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status updated successfully', 'success');
        } else {
            showNotification('Failed to update status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating status', 'error');
    });
}

function updateLocation(position) {
    const location = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };

    fetch('../api/update_location.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(location)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to update location');
        }
    })
    .catch(error => console.error('Error:', error));
}

function handleLocationError(error) {
    console.error('Error getting location:', error);
}

function respondToEmergency(emergencyId) {
    fetch('../api/respond_to_emergency.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ emergency_id: emergencyId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Successfully responded to emergency', 'success');
            refreshEmergencies();
        } else {
            showNotification('Failed to respond to emergency', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error responding to emergency', 'error');
    });
}

function refreshEmergencies() {
    fetch('../api/get_active_emergencies.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateEmergencyList(data.emergencies);
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateEmergencyList(emergencies) {
    const container = document.querySelector('.emergency-list');
    if (!container) return;

    container.innerHTML = emergencies.length === 0 
        ? '<li>No active emergencies at the moment.</li>'
        : emergencies.map(emergency => createEmergencyCard(emergency)).join('');
}

function createEmergencyCard(emergency) {
    return `
        <li class="emergency-item severity-${emergency.severity}">
            <strong>Type:</strong> ${escapeHtml(emergency.type)}<br>
            <strong>Location:</strong> ${escapeHtml(emergency.location)}<br>
            <strong>Severity:</strong> ${escapeHtml(emergency.severity)}<br>
            <strong>Description:</strong> ${escapeHtml(emergency.description)}<br>
            <strong>Reported by:</strong> ${escapeHtml(emergency.reporter_name)}<br>
            <strong>Reported at:</strong> ${formatDate(emergency.created_at)}<br>
            <button onclick="respondToEmergency(${emergency.id})" class="respond-btn">Respond</button>
        </li>
    `;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

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
