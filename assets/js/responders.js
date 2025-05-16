document.addEventListener('DOMContentLoaded', function() {
    loadRespondersList();
    setupResponderForm();
});

async function loadRespondersList() {
    try {
        const response = await fetch('../api/responders.php');
        const responders = await response.json();
        displayResponders(responders);
    } catch (error) {
        showError('Failed to load responders');
    }
}

function displayResponders(responders) {
    const respondersList = document.querySelector('.responders-list');
    // Add your responders display logic here
}

function setupResponderForm() {
    const form = document.querySelector('#responder-form');
    if (form) {
        form.addEventListener('submit', handleResponderSubmit);
    }
}

async function handleResponderSubmit(event) {
    event.preventDefault();
    // Add form submission logic here
}

// Global variables
let map;
let emergencyMarkers = [];
let currentEmergencyId = null;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    initializeStatusUpdates();
    setupEventListeners();
    startRealTimeUpdates();
});

// Initialize Leaflet map
function initializeMap() {
    const defaultLocation = [14.5995, 120.9842]; // Default to Manila
    map = L.map('emergencyMap').setView(defaultLocation, 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Get responder's location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const { latitude, longitude } = position.coords;
                map.setView([latitude, longitude], 13);
                updateResponderLocation(latitude, longitude);
            },
            error => console.error('Location error:', error)
        );
    }
}

// Initialize status updates
function initializeStatusUpdates() {
    const statusSelect = document.getElementById('availabilityStatus');
    const updateBtn = document.getElementById('updateStatus');

    updateBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('../api/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: statusSelect.value })
            });

            const data = await response.json();
            if (data.success) {
                showToast('Status updated successfully', 'success');
            } else {
                showToast(data.error || 'Failed to update status', 'error');
            }
        } catch (error) {
            showToast('Failed to update status', 'error');
        }
    });
}

// Update responder's location
async function updateResponderLocation(latitude, longitude) {
    try {
        await fetch('../api/update_location.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ latitude, longitude })
        });
    } catch (error) {
        console.error('Error updating location:', error);
    }
}

// Start real-time updates
function startRealTimeUpdates() {
    updateEmergencies();
    setInterval(updateEmergencies, 30000); // Update every 30 seconds
}

// Fetch and display active emergencies
async function updateEmergencies() {
    try {
        const response = await fetch('../api/get_active_emergencies.php');
        const emergencies = await response.json();
        
        updateEmergencyMarkers(emergencies);
        updateEmergencyList(emergencies);
    } catch (error) {
        console.error('Error fetching emergencies:', error);
    }
}

// Update emergency markers on the map
function updateEmergencyMarkers(emergencies) {
    // Clear existing markers
    emergencyMarkers.forEach(marker => marker.remove());
    emergencyMarkers = [];

    // Add new markers
    emergencies.forEach(emergency => {
        const marker = L.marker([emergency.latitude, emergency.longitude])
            .bindPopup(createEmergencyPopup(emergency))
            .addTo(map);
        
        marker.on('click', () => showEmergencyDetails(emergency.id));
        emergencyMarkers.push(marker);
    });
}

// Update emergency list
function updateEmergencyList(emergencies) {
    const container = document.getElementById('activeEmergencies');
    container.innerHTML = emergencies.map(emergency => `
        <div class="emergency-item ${emergency.priority === 'high' ? 'high-priority' : ''}" 
             onclick="showEmergencyDetails(${emergency.id})">
            <h5>${emergency.type}</h5>
            <p>${emergency.description}</p>
            <small class="text-muted">${formatTime(emergency.created_at)}</small>
        </div>
    `).join('');
}

// Show emergency details in modal
async function showEmergencyDetails(emergencyId) {
    try {
        currentEmergencyId = emergencyId;
        const response = await fetch(`../api/get_emergency_details.php?id=${emergencyId}`);
        const emergency = await response.json();
        
        const medicalInfo = await fetch(`../api/get_medical_info.php?resident_id=${emergency.resident_id}`);
        const medical = await medicalInfo.json();

        document.getElementById('emergencyDetails').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h4>Emergency Information</h4>
                    <p><strong>Type:</strong> ${emergency.type}</p>
                    <p><strong>Description:</strong> ${emergency.description}</p>
                    <p><strong>Location:</strong> ${emergency.location}</p>
                    <p><strong>Reported:</strong> ${formatTime(emergency.created_at)}</p>
                </div>
                <div class="col-md-6">
                    <h4>Resident Information</h4>
                    <p><strong>Name:</strong> ${medical.name}</p>
                    <p><strong>Age:</strong> ${medical.age}</p>
                    <p><strong>Gender:</strong> ${medical.gender}</p>
                    <p><strong>Medical Conditions:</strong> ${medical.conditions}</p>
                </div>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('emergencyModal'));
        modal.show();

        // Update medical info tabs
        updateMedicalTabs(medical);
    } catch (error) {
        console.error('Error fetching emergency details:', error);
        showToast('Failed to load emergency details', 'error');
    }
}

// Update medical information tabs
function updateMedicalTabs(medical) {
    document.getElementById('generalInfo').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> ${medical.name}</p>
                <p><strong>Age:</strong> ${medical.age}</p>
                <p><strong>Gender:</strong> ${medical.gender}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Blood Type:</strong> ${medical.blood_type}</p>
                <p><strong>Emergency Contact:</strong> ${medical.emergency_contact}</p>
                <p><strong>Contact Number:</strong> ${medical.contact_number}</p>
            </div>
        </div>
    `;

    document.getElementById('conditions').innerHTML = `
        <div class="medical-conditions">
            <h5>Current Conditions</h5>
            ${medical.conditions.map(condition => `
                <div class="condition-item">
                    <p><strong>${condition.name}</strong></p>
                    <p>${condition.details}</p>
                </div>
            `).join('')}
        </div>
    `;

    document.getElementById('history').innerHTML = `
        <div class="medical-history">
            <h5>Past Incidents</h5>
            ${medical.history.map(incident => `
                <div class="history-item">
                    <p><strong>${incident.date}</strong> - ${incident.type}</p>
                    <p>${incident.description}</p>
                </div>
            `).join('')}
        </div>
    `;
}

// Handle emergency response actions
document.getElementById('markResolved').addEventListener('click', async () => {
    if (!currentEmergencyId) return;

    try {
        const response = await fetch('../api/close_emergency.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ emergency_id: currentEmergencyId })
        });

        const data = await response.json();
        if (data.success) {
            showToast('Emergency marked as resolved', 'success');
            bootstrap.Modal.getInstance(document.getElementById('emergencyModal')).hide();
            updateEmergencies();
        } else {
            showToast(data.error || 'Failed to resolve emergency', 'error');
        }
    } catch (error) {
        showToast('Failed to resolve emergency', 'error');
    }
});

document.getElementById('dispatchResponse').addEventListener('click', async () => {
    if (!currentEmergencyId) return;

    try {
        const response = await fetch('../api/respond_to_emergency.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ emergency_id: currentEmergencyId })
        });

        const data = await response.json();
        if (data.success) {
            showToast('Response dispatched successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('emergencyModal')).hide();
            updateEmergencies();
        } else {
            showToast(data.error || 'Failed to dispatch response', 'error');
        }
    } catch (error) {
        showToast('Failed to dispatch response', 'error');
    }
});

// Utility functions
function formatTime(timestamp) {
    return new Date(timestamp).toLocaleString();
}

function showToast(message, type = 'info') {
    // Implement your preferred toast notification system
    alert(message); // Temporary solution
}

// Tab functionality
const tabButtons = document.querySelectorAll('.tab-btn');
const tabPanels = document.querySelectorAll('.tab-panel');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons and panels
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabPanels.forEach(panel => panel.classList.remove('active'));
        
        // Add active class to clicked button and corresponding panel
        button.classList.add('active');
        const tabId = button.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});

// Load medical information data
function loadMedicalInfo() {
    fetch('../api/get_medical_info.php')
        .then(response => response.json())
        .then(data => {
            updateGeneralInfo(data.general);
            updateConditions(data.conditions);
            updateHistory(data.history);
        })
        .catch(error => console.error('Error loading medical information:', error));
}

function updateGeneralInfo(data) {
    const container = document.getElementById('generalInfo');
    // Update general info cards with data
}

function updateConditions(data) {
    const container = document.getElementById('conditions');
    // Update conditions cards with data
}

function updateHistory(data) {
    const container = document.getElementById('history');
    // Update history cards with data
}

// Load medical information when page loads
document.addEventListener('DOMContentLoaded', loadMedicalInfo);