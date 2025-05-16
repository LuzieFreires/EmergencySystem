document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    const userTypeSelect = document.getElementById('user_type');
    const residentFields = document.getElementById('resident_fields');
    const responderFields = document.getElementById('responder_fields');

    // Toggle additional fields based on user type
    userTypeSelect.addEventListener('change', function() {
        if (this.value === 'resident') {
            residentFields.style.display = 'block';
            responderFields.style.display = 'none';
        } else {
            residentFields.style.display = 'none';
            responderFields.style.display = 'block';
        }
    });

    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        const formData = new FormData(registerForm);
        
        try {
            const response = await fetch('../api/register.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('Registration successful! Redirecting to login...');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showError(data.message || 'Registration failed');
            }
        } catch (error) {
            showError('An error occurred during registration');
        }
    });
});

function validateForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const userType = document.getElementById('user_type').value;
    const errors = [];

    // Validate password
    if (password.length < 6) {
        errors.push('Password must be at least 6 characters long');
    }

    if (password !== confirmPassword) {
        errors.push('Passwords do not match');
    }

    // Validate required fields based on user type
    if (userType === 'resident') {
        const address = document.getElementById('address').value;
        const contactNumber = document.getElementById('contact_number').value;

        if (!address.trim()) {
            errors.push('Address is required for residents');
        }
        if (!contactNumber.trim()) {
            errors.push('Contact number is required for residents');
        }
    } else if (userType === 'responder') {
        const responderContact = document.getElementById('responder_contact').value;

        if (!responderContact.trim()) {
            errors.push('Emergency contact number is required for responders');
        }
    }

    if (errors.length > 0) {
        showError(errors.join('<br>'));
        return false;
    }

    return true;
}

function showError(message) {
    const errorDiv = document.getElementById('error-messages');
    errorDiv.innerHTML = message;
    errorDiv.style.display = 'block';

    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

function showSuccess(message) {
    const errorDiv = document.getElementById('error-messages');
    errorDiv.className = 'alert alert-success';
    errorDiv.innerHTML = message;
    errorDiv.style.display = 'block';
}