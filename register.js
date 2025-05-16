document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');

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

    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return false;
    }

    if (password.length < 6) {
        showError('Password must be at least 6 characters long');
        return false;
    }

    return true;
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger';
    errorDiv.textContent = message;
    document.querySelector('.auth-box').prepend(errorDiv);

    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success';
    successDiv.textContent = message;
    document.querySelector('.auth-box').prepend(successDiv);
}