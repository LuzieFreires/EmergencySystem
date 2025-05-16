document.addEventListener('DOMContentLoaded', function() {
    loadResidentsList();
    setupResidentForm();
});

async function loadResidentsList() {
    try {
        const response = await fetch('../api/residents.php');
        const residents = await response.json();
        displayResidents(residents);
    } catch (error) {
        showError('Failed to load residents');
    }
}

function displayResidents(residents) {
    const residentsList = document.querySelector('.residents-list');
    // Add your residents display logic here
}

function setupResidentForm() {
    const form = document.querySelector('#resident-form');
    if (form) {
        form.addEventListener('submit', handleResidentSubmit);
    }
}

async function handleResidentSubmit(event) {
    event.preventDefault();
    // Add form submission logic here
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('updateMedicalInfo');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('../api/update_medical_info.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message); // You can replace this with a styled alert box if needed
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error(error);
            });
        });
    }
});
