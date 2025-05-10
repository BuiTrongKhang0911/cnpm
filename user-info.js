const navbar = document.querySelector("nav");
window.addEventListener("scroll", () =>
    navbar.classList.toggle("sticky", window.scrollY > 0)
);

const menu = document.querySelector(".menu");
const toggleMenu = () => menu.classList.toggle("active");

document.querySelector(".menu-btn").addEventListener("click", toggleMenu);
document.querySelector(".close-btn"). addEventListener("click", toggleMenu);

document
    .querySelectorAll(".menu a")
    .forEach((link) => link.addEventListener("click", toggleMenu));

const sr = ScrollReveal({
    origin: "bottom",
    distance: "40px",
    duration: 1000,
    delay: 400,
    easing: "ease-in-out",
});

function closeAllForms() {
    closeLoginForm();
    closeRegisterForm();
}

function showSection(sectionId) {
    document.querySelectorAll('.form-section').forEach(function(section) {
        section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';
}

function logout() {
    window.location.href = 'logout.php';
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

function validateField(fieldName, value) {
    const errorElement = document.getElementById(`${fieldName}-error`);
    let isValid = true;
    let errorMessage = '';

    switch(fieldName) {
        case 'fullname':
            if (value.length < 5) {
                isValid = false;
                errorMessage = 'Username must be at least 5 characters long.';
            } else if (!/^[a-zA-Z0-9]+$/.test(value)) {
                isValid = false;
                errorMessage = 'Username can only contain letters and numbers.';
            }
            break;
        case 'email':
            if (!value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                isValid = false;
                errorMessage = 'Invalid email format.';
            }
            break;
        case 'phone':
            if (value.length !== 10) {
                isValid = false;
                errorMessage = 'Phone number must be 10 digits.';
            }
            break;
        case 'dob':
            const selectedDate = new Date(value);
            const today = new Date();
            if (selectedDate > today) {
                isValid = false;
                errorMessage = 'Date of birth cannot be in the future.';
            }
            break;
        case 'address':
            if (value.length == 0) {
                isValid = false;
                errorMessage = 'Address is required.';
            }
            break;
    }

    if (!isValid) {
        errorElement.textContent = errorMessage;
        errorElement.style.display = 'block';
    } else {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }

    return isValid;
}

// Add real-time validation for fields
if (document.querySelector('input[name="fullname"]')) {
    document.querySelector('input[name="fullname"]').addEventListener('blur', function() {
        const username = this.value;
        const errorElement = document.getElementById('fullname-error');
        
        // First validate format
        if (!validateField('fullname', username)) {
            return;
        }

        // Then check if username exists
        const formData = new FormData();
        formData.append('validate_username', '1');
        formData.append('fullname', username);

        fetch('change-info.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors && data.errors.fullname) {
                errorElement.textContent = data.errors.fullname;
                errorElement.style.display = 'block';
            } else {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
}

if (document.querySelector('input[name="email"]')) {
    document.querySelector('input[name="email"]').addEventListener('blur', function() {
        validateField('email', this.value);
    });
}

if (document.querySelector('input[name="phone"]')) {
    document.querySelector('input[name="phone"]').addEventListener('blur', function() {
        validateField('phone', this.value);
    });
}

if (document.querySelector('input[name="dob"]')) {
    document.querySelector('input[name="dob"]').addEventListener('change', function() {
        validateField('dob', this.value);
    });
}

if(document.getElementById('textarea-address')){
    document.getElementById('textarea-address').addEventListener('blur', function() {
        validateField('address', this.value);
    });
}

// Form submission
if (document.getElementById('changeUserInfo')) {
    document.getElementById('changeUserInfo').addEventListener('submit', function(e) {
        e.preventDefault();
        const errorElement = document.getElementById('update-error');
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        document.querySelectorAll('#changeUserInfo .error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        // Validate all fields before submission
        const fullname = document.querySelector('input[name="fullname"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const phone = document.querySelector('input[name="phone"]').value;
        const dob = document.querySelector('input[name="dob"]').value;
        const address = document.getElementById('textarea-address').value;

        const isFullnameValid = validateField('fullname', fullname);
        const isEmailValid = validateField('email', email);
        const isPhoneValid = validateField('phone', phone);
        const isDobValid = validateField('dob', dob);
        const isAddressValid = validateField('address', address);

        if (!isFullnameValid || !isEmailValid || !isPhoneValid || !isDobValid || !isAddressValid) {
            submitButton.disabled = false;
            submitButton.textContent = 'Save change';
            return;
        }

        const formData = new FormData(this);
        fetch('change-info.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.textContent = 'Save change';
            if (data.success) {
                errorElement.textContent = data.message || 'Information updated successfully!';
                errorElement.style.color = 'green';
                errorElement.style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'user-info.php?section=1';
                }, 1500);
            } else {
                if (data.errors.server) {
                    errorElement.textContent = data.errors.server;
                    errorElement.style.display = 'block';
                }
                if (data.errors.fullname) {
                    const errorField = document.getElementById('fullname-error');
                    errorField.textContent = data.errors.fullname;
                    errorField.style.display = 'block';
                }
            }
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.textContent = 'Save change';
            errorElement.textContent = 'An error occurred. Please try again.';
            errorElement.style.display = 'block';
            console.error('Error:', error);
        });
    });
}

if (document.querySelector('input[name="phone"]')) {
    document.querySelector('input[name="phone"]').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
}

if (document.getElementById('textarea-address')) {
    document.getElementById('textarea-address').addEventListener('input', () => {
        document.querySelector('input[name="address"]').value = document.getElementById('textarea-address').value;
    });
}

if (document.getElementById('changePassword')) {
    document.getElementById('changePassword').addEventListener('submit', function(e) {
        e.preventDefault();
        const errorElement = document.getElementById('error');
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        errorElement.textContent = '';
        errorElement.classList.remove('active');

        const formData = new FormData(this);
        fetch('change_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.textContent = 'Save change';
            if (data.success) {
                errorElement.textContent = data.message || 'Password updated successfully!';
                errorElement.style.color = 'green';
                errorElement.classList.add('active');
                window.location.href = 'user-info.php?section=1';
            } else {
                if (data.error) {
                    errorElement.textContent = data.error;
                    errorElement.classList.add('active');
                }
            }
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.textContent = 'Save change';
            errorElement.textContent = 'An error occurred. Please try again.';
            errorElement.classList.add('active');
            console.error('Error:', error);
        });
    });
}

window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('section') === '1') {
        showSection('user-info');
    }
    else if(urlParams.get('section') === '2'){
        showSection('booking-history'); 
    }
});