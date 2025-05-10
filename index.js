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

function showLoginForm() {
    document.getElementById('registerForm').classList.remove('active');
    document.getElementById('registerForm').style.display = 'none';

    const loginForm = document.getElementById('loginForm');
    loginForm.style.display = 'block';
    setTimeout(() => {
        loginForm.classList.add('active');
    }, 10);

    document.getElementById('overlay').style.display = 'block';
}

function showRegisterForm() {
    document.getElementById('loginForm').classList.remove('active');
    document.getElementById('loginForm').style.display = 'none';

    const registerForm = document.getElementById('registerForm');
    registerForm.style.display = 'block';
    document.getElementById('register-step').style.display = 'block';
    document.getElementById('verify-step').style.display = 'none';
    setTimeout(() => {
        registerForm.classList.add('active');
    }, 10);

    document.getElementById('overlay').style.display = 'block';
}

function closeLoginForm() {
    const loginForm = document.getElementById('loginForm');
    loginForm.classList.remove('active');
    setTimeout(() => {
        loginForm.style.display = 'none';
    }, 300);
    document.getElementById('overlay').style.display = 'none';
}

function closeRegisterForm() {
    const registerForm = document.getElementById('registerForm');
    registerForm.classList.remove('active');
    setTimeout(() => {
        registerForm.style.display = 'none';
    }, 300);
    document.getElementById('overlay').style.display = 'none';
}

function showForgotForm() {
    document.getElementById('loginForm').classList.remove('active');
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').classList.remove('active');
    document.getElementById('registerForm').style.display = 'none';

    const forgotForm = document.getElementById('forgotForm');
    forgotForm.style.display = 'block';
    if(document.getElementById('pinStep').style.display !== 'none'){
        document.getElementById('emailStep').style.display = 'block';
        document.getElementById('pinStep').style.display = 'none';
    }
    setTimeout(() => {
        forgotForm.classList.add('active');
    }, 10);

    document.getElementById('overlay').style.display = 'block';
}

function closeForgotForm() {
    const forgotForm = document.getElementById('forgotForm');
    forgotForm.classList.remove('active');
    setTimeout(() => {
        forgotForm.style.display = 'none';
    }, 300);
    document.getElementById('overlay').style.display = 'none';
}

function showVerifyForm() {
    document.getElementById('register-step').style.display = 'none';
    document.getElementById('verify-step').style.display = 'block';
}

function closeAllForms() {
    closeLoginForm();
    closeRegisterForm();
    closeForgotForm();
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px'; 
}

function sendResetEmail() {
    const email = document.querySelector('#forgotPasswordForm input[name="email"]').value;
    const button = document.querySelector('#emailStep button');
    button.textContent = 'Sending...';
    button.disabled = true;
    document.getElementById('email-error').style.display = 'none';
    document.getElementById('email-error').textContent = '';
    if (!email) {
        document.getElementById('email-error').style.display = 'block';
        document.getElementById('email-error').textContent = 'Please enter your email address';
        button.textContent = 'Send Reset Code';
        button.disabled = false;
        return;
    }

    // Send AJAX request to send reset email
    fetch('send-reset-email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show PIN step
            document.getElementById('emailStep').style.display = 'none';
            document.getElementById('pinStep').style.display = 'block';
            button.textContent = 'Send Reset Code';
            button.disabled = false;
        } else {
            document.getElementById('email-error').style.display = 'block';
            document.getElementById('email-error').textContent = data.message;
            button.textContent = 'Send Reset Code';
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('email-error').style.display = 'block';
        document.getElementById('email-error').textContent = 'An error occurred. Please try again.';
    });
}

// Add password reset form submission handler
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const pin = document.querySelector('#forgotPasswordForm input[name="pin"]').value;
    const newPassword = document.querySelector('#forgotPasswordForm input[name="new_password"]').value;
    const confirmPassword = document.querySelector('#forgotPasswordForm input[name="confirm_password"]').value;
    document.getElementById('pin-error').style.display = 'none';
    document.getElementById('pin-error').textContent = '';
    
    // Validate PIN
    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        document.getElementById('pin-error').style.display = 'block';
        document.getElementById('pin-error').textContent = 'Please enter a valid 6-digit PIN';
        return;
    }
    
    // Validate password
    if (newPassword.length < 6) {
        document.getElementById('pin-error').style.display = 'block';
        document.getElementById('pin-error').textContent = 'Password must be at least 6 characters long';
        return;
    }
    
    // Validate password confirmation
    if (newPassword !== confirmPassword) {
        document.getElementById('pin-error').style.display = 'block';
        document.getElementById('pin-error').textContent = 'Passwords do not match';
        return;
    }
    
    // Send reset password request
    const formData = new FormData(this);
    fetch('process-reset-password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeForgotForm();
            showLoginForm();
            document.querySelector('#forgotPasswordForm input[name="email"]').value = '';
            document.getElementById('email-error').style.display = 'none';
            document.getElementById('email-error').textContent = '';
            document.querySelector('#forgotPasswordForm input[name="pin"]').value = '';
            document.getElementById('pin-error').style.display = 'none';
            document.getElementById('pin-error').textContent = '';
            document.querySelector('#forgotPasswordForm input[name="new_password"]').value='';
            document.querySelector('#forgotPasswordForm input[name="confirm_password"]').value='';
        } else {
            document.getElementById('pin-error').style.display = 'block';
            document.getElementById('pin-error').textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('pin-error').style.display = 'block';
        document.getElementById('pin-error').textContent = 'An error occurred. Please try again.';
    });
});

function validateField(fieldName, value, password = '') {
    const errorElement = document.querySelector(`#register-step #${fieldName}-error`);
    let isValid = true;
    let errorMessage = '';

    // Kiểm tra input rỗng
    if (!value || value.trim() === '') {
        isValid = false;
        errorMessage = 'Please fill out this field.';
    } else {
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
            case 'phonenumber':
                if (value.length !== 10) {
                    isValid = false;
                    errorMessage = 'Phone number must be 10 digits.';
                }
                break;
            case 'password':
                if (value.length < 6) {
                    isValid = false;
                    errorMessage = 'Password must be at least 6 characters long.';
                }
                break;
            case 'confirm-password':
                if (value !== password) {
                    isValid = false;
                    errorMessage = 'Passwords do not match.';
                }
                break;
            case 'dateofbirth':
                const selectedDate = new Date(value);
                const today = new Date();
                if (selectedDate > today) {
                    isValid = false;
                    errorMessage = 'Date of birth cannot be in the future.';
                }
                break;
        }
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

// Add real-time validation for username
if (document.querySelector('#registerFormSubmit input[name="fullname"]')) {
    document.querySelector('#registerFormSubmit input[name="fullname"]').addEventListener('blur', function() {
        const username = this.value;
        const errorElement = document.getElementById('fullname-error');
        if (!validateField('fullname', username)) {
            return;
        }
        const formData = new FormData();
        formData.append('validate_username', '1');
        formData.append('fullname', username);
        fetch('register.php', {
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

// Password validation
const passwordField = document.querySelector('#registerFormSubmit input[name="password"]');
const confirmPasswordField = document.querySelector('#registerFormSubmit input[name="confirm-password"]');
if (passwordField && confirmPasswordField) {
    passwordField.addEventListener('input', function() {
        validateField('password', this.value);
        if (confirmPasswordField.value) {
            validateField('confirm-password', confirmPasswordField.value, this.value);
        }
    });
    passwordField.addEventListener('blur', function() {
        validateField('password', this.value);
        if (confirmPasswordField.value) {
            validateField('confirm-password', confirmPasswordField.value, this.value);
        }
    });
    confirmPasswordField.addEventListener('blur', function() {
        const password = passwordField.value;
        validateField('confirm-password', this.value, password);
    });
}

// Other field validations
if (document.querySelector('#registerFormSubmit input[name="email"]')) {
    document.querySelector('#registerFormSubmit input[name="email"]').addEventListener('blur', function() {
        validateField('email', this.value);
    });
}
if (document.querySelector('#registerFormSubmit input[name="phonenumber"]')) {
    document.querySelector('#registerFormSubmit input[name="phonenumber"]').addEventListener('blur', function() {
        validateField('phonenumber', this.value);
    });
    document.querySelector('#registerFormSubmit input[name="phonenumber"]').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
}
if (document.querySelector('#registerFormSubmit input[name="dateofbirth"]')) {
    document.querySelector('#registerFormSubmit input[name="dateofbirth"]').addEventListener('change', function() {
        validateField('dateofbirth', this.value);
    });
}
if (document.querySelector('#registerFormSubmit input[name="address"]')) {
    document.querySelector('#registerFormSubmit input[name="address"]').addEventListener('blur', function() {
        validateField('address', this.value);
    });
}

// Update the form submission code
if (document.getElementById('register-button')) {
    document.getElementById('register-button').addEventListener('click', () =>{
        document.getElementById('register-error').style.display = 'none';
        document.getElementById('register-error').textContent = '';
        const submitButton = document.getElementById('register-button');
        submitButton.textContent = 'Processing...';

        const fullname = document.querySelector('#registerFormSubmit input[name="fullname"]').value;
        const email = document.querySelector('#registerFormSubmit input[name="email"]').value;
        const phonenumber = document.querySelector('#registerFormSubmit input[name="phonenumber"]').value;
        const password = document.querySelector('#registerFormSubmit input[name="password"]').value;
        const confirmPassword = document.querySelector('#registerFormSubmit input[name="confirm-password"]').value;
        const dateofbirth = document.querySelector('#registerFormSubmit input[name="dateofbirth"]').value;
        const address = document.querySelector('#registerFormSubmit input[name="address"]').value;

        const isFullnameValid = validateField('fullname', fullname);
        const isEmailValid = validateField('email', email);
        const isPhoneValid = validateField('phonenumber', phonenumber);
        const isPasswordValid = validateField('password', password);
        const isConfirmPasswordValid = validateField('confirm-password', confirmPassword, password);
        const isDateValid = validateField('dateofbirth', dateofbirth);
        const isAddressValid = validateField('address', address);

        if (!isFullnameValid || !isEmailValid || !isPhoneValid || !isPasswordValid || !isConfirmPasswordValid || !isDateValid || !isAddressValid) {
            submitButton.disabled = false;
            submitButton.textContent = 'Register';
            return;
        }
        else{
            const email=document.querySelector('#registerFormSubmit input[name="email"]').value;
            showVerifyForm();
            fetch('send-verify-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    submitButton.textContent = 'Register';
                } else {
                    submitButton.textContent = 'Register';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('register-error').style.display = 'block';
                document.getElementById('register-error').textContent = 'An error occurred. Please try again.';
            });
        }
    });
}

document.getElementById('registerFormSubmit').addEventListener('submit', function(e) {
    e.preventDefault();
    const errorElement=document.getElementById('verify-error');
    errorElement.style.display = 'none';
    errorElement.textContent = '';
    if(!document.querySelector('#verify-step input[name="verify-code"]').value){
        errorElement.style.display = 'block';
        errorElement.textContent = 'Please enter the 6-digit code sent to your email';
        return;
    }
    const formData = new FormData(this);
    fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showRegisterForm();
                    document.getElementById('register-error').textContent = data.message || 'Registration successful!';
                    document.getElementById('register-error').style.color = 'green';
                    document.getElementById('register-error').style.display = 'block';
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    errorElement.textContent = data.message;
                    errorElement.style.display = 'block';
                }
            })
            .catch(error => {
                errorElement.textContent = 'An error occurred. Please try again.';
                errorElement.style.display = 'block';
                console.error('Error:', error);
            });
})

// Xử lý login form
if (document.getElementById('loginFormSubmit')) {
    document.getElementById('loginFormSubmit').addEventListener('submit', function(e) {
        e.preventDefault();
        const errorElement = document.getElementById('login-error');
        errorElement.style.display = 'none';

        const formData = new FormData(this);
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php';
            } else {
                errorElement.textContent = data.message;
                errorElement.style.display = 'block';
            }
        })
        .catch(error => {
            errorElement.textContent = 'An error occurred. Please try again.';
            errorElement.style.display = 'block';
            console.error('Error:', error);
        });
    });
}