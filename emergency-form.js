document.addEventListener('DOMContentLoaded', function() {
    setupEmergencyForm();
});

function setupEmergencyForm() {
    const form = document.querySelector('#emergency-form');
    if (form) {
        form.addEventListener('submit', handleEmergencySubmit);
    }
}

async function handleEmergencySubmit(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('../api/emergency.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Emergency reported successfully');
            // Additional success handling
        } else {
            showError(result.message || 'Failed to submit emergency report');
        }
    } catch (error) {
        showError('Failed to submit emergency report');
    }
}