// Theme handling
const themeSelect = document.getElementById('themeSelect');
const savedTheme = localStorage.getItem('theme') || 'light';
themeSelect.value = savedTheme;

themeSelect.addEventListener('change', (e) => {
    const theme = e.target.value;
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
});

// Profile update handling
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-update-form');
    const errorMessages = document.getElementById('error-messages');
    const successMessage = document.getElementById('success-message');
    const userType = '<?php echo $_SESSION["user_type"]; ?>'; // Get user type from session

    // Show/hide appropriate fields based on user type
    const residentFields = document.getElementById('resident_fields');
    const responderFields = document.getElementById('responder_fields');
    
    if (userType === 'resident') {
        residentFields.style.display = 'block';
        responderFields.style.display = 'none';
    } else if (userType === 'responder') {
        residentFields.style.display = 'none';
        responderFields.style.display = 'block';
    }

    // Load current user data
    fetch('../api/get_user_profile.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('email').value = data.user.email;
                
                if (data.user.user_type === 'resident') {
                    document.getElementById('address').value = data.user.address || '';
                    document.getElementById('contact_number').value = data.user.contact_number || '';
                } else if (data.user.user_type === 'responder') {
                    document.getElementById('specialization').value = data.user.specialization || 'medical';
                    document.getElementById('responder_contact').value = data.user.responder_contact || '';
                }
            }
        })
        .catch(error => {
            showError('Failed to load profile data');
        });

    // Handle form submission
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(profileForm);

        // Client-side validation
        const currentPassword = formData.get('current_password');
        if (!currentPassword) {
            showError('Current password is required to save changes');
            return;
        }

        // Show loading state
        const submitButton = profileForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        showLoading('Updating profile...');

        // Send update request
        fetch('../api/update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Profile updated successfully');
                if (data.requiresRelogin) {
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                }
            } else {
                throw new Error(data.message || 'Failed to update profile');
            }
        })
        .catch(error => {
            showError(error.message || 'An error occurred while updating profile');
        })
        .finally(() => {
            submitButton.disabled = false;
            hideLoading();
        });
    });

    // Helper functions for showing messages
    function showError(message) {
        errorMessages.textContent = message;
        errorMessages.style.display = 'block';
        successMessage.style.display = 'none';
        setTimeout(() => {
            errorMessages.style.display = 'none';
        }, 5000);
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.style.display = 'block';
        errorMessages.style.display = 'none';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }
});