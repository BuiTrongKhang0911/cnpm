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

const taoDanhSachThoiGian = () => {
    const times = [];
    for (let i = 0; i < 48; i++) {
        const hour = Math.floor(i / 2);
        const minute = i % 2 === 0 ? "00" : "30";
        times.push(`${hour.toString().padStart(2, '0')}:${minute}`);
    }
    return times;
};

const now = new Date();
const currentHour = now.getHours();
const currentMinute = now.getMinutes();
const roundedHour = currentMinute <= 29 ? currentHour : (currentHour + 1) % 24;
const roundedMinute = currentMinute <= 29 ? "30" : "00";
const defaultStartTime = `${roundedHour.toString().padStart(2, '0')}:${roundedMinute}`;

const fromDateInput = document.querySelector("#from-date");
const toDateInput = document.querySelector("#to-date");
const fromTimeInput = document.querySelector("#from-time");
const toTimeInput = document.querySelector("#to-time");
const totalDisplay = document.querySelector("#total");
const tempPrice = document.querySelector("#temp");

let startDate = fromDateInput.value ? new Date(fromDateInput.value) : null;
let endDate = toDateInput.value ? new Date(toDateInput.value) : null;
let fromTimeValue = document.getElementById('start-time-hidden')?.value || defaultStartTime;
let toTimeValue = document.getElementById('end-time-hidden')?.value || taoDanhSachThoiGian()[taoDanhSachThoiGian().indexOf(fromTimeValue) + 1] || "20:00";

const fromDatePicker = flatpickr("#from-date", {
    enableTime: false,
    dateFormat: "Y-m-d",
    minDate: "today",
    locale: {
        firstDayOfWeek: 1 
    }
});

const toDatePicker = flatpickr("#to-date", {
    enableTime: false,
    dateFormat: "Y-m-d",
    minDate: "today",
    locale: {
        firstDayOfWeek: 1
    }
});

const fromDateValue = fromDateInput.value;
const toDateValue = toDateInput.value;

if (fromDateValue) {
    fromDatePicker.setDate(fromDateValue, true);
} else {
    fromDatePicker.setDate(new Date(), true);
}

if (toDateValue) {
    toDatePicker.setDate(toDateValue, true);
} else {
    toDatePicker.setDate(new Date(new Date().getTime() + 24 * 60 * 60 * 1000), true);
}

const fillHourSelect = (select) => {
    taoDanhSachThoiGian().forEach(time => select.add(new Option(time, time)));
};
fillHourSelect(fromTimeInput);
fillHourSelect(toTimeInput);
fromTimeInput.value = fromTimeValue;
toTimeInput.value = toTimeValue;

function updateToDateMin() {
    const fromDate = fromDateInput._flatpickr.selectedDates[0];
    if (fromDate) {
        const currentToDate = toDateInput._flatpickr.selectedDates[0];
        toDatePicker.set("minDate", fromDate);
        if (currentToDate && currentToDate < fromDate) {
            const newToDate = new Date(fromDate);
            newToDate.setDate(fromDate.getDate() + 1);
            toDatePicker.setDate(newToDate, true);
        }
    }
}

function capNhatGioBatDau() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const times = taoDanhSachThoiGian();
    fromTimeInput.innerHTML = '';

    const isToday = startDate && startDate.toDateString() === today.toDateString();
    times.forEach(time => {
        const option = document.createElement("option");
        option.value = option.text = time;
        if (isToday) {
            const minutes = time.split(':').map(Number);
            const timeInMinutes = minutes[0] * 60 + minutes[1];
            const defaultMinutes = defaultStartTime.split(':').map(Number);
            const defaultTimeInMinutes = defaultMinutes[0] * 60 + defaultMinutes[1];
            option.disabled = timeInMinutes < defaultTimeInMinutes;
        }
        option.selected = time === fromTimeValue || (!fromTimeValue && time === defaultStartTime);
        fromTimeInput.appendChild(option);
    });

    if (isToday && fromTimeValue) {
        const minutes = fromTimeValue.split(':').map(Number);
        const timeInMinutes = minutes[0] * 60 + minutes[1];
        const defaultMinutes = defaultStartTime.split(':').map(Number);
        const defaultTimeInMinutes = defaultMinutes[0] * 60 + defaultMinutes[1];
        if (timeInMinutes < defaultTimeInMinutes) {
            fromTimeValue = defaultStartTime;
            fromTimeInput.value = fromTimeValue;
        }
    }
}

function capNhatGioKetThuc() {
    const startTime = fromTimeInput.value;
    const times = taoDanhSachThoiGian();
    toTimeInput.innerHTML = '';

    const defaultEndTime = times[times.indexOf(startTime) + 1] || times[0];
    const sameDay = startDate && endDate && startDate.toDateString() === endDate.toDateString();

    times.forEach(time => {
        const option = document.createElement("option");
        option.value = option.text = time;
        if (sameDay && startTime) {
            const startMinutes = startTime.split(':').map(Number);
            const startTimeInMinutes = startMinutes[0] * 60 + startMinutes[1];
            const endMinutes = time.split(':').map(Number);
            const endTimeInMinutes = endMinutes[0] * 60 + endMinutes[1];
            option.disabled = endTimeInMinutes <= startTimeInMinutes;
        }
        option.selected = time === toTimeValue || (!toTimeValue && time === defaultEndTime);
        toTimeInput.appendChild(option);
    });

    if (sameDay && toTimeValue) {
        const endMinutes = toTimeValue.split(':').map(Number);
        const endTimeInMinutes = endMinutes[0] * 60 + endMinutes[1];
        const defaultEndMinutes = defaultEndTime.split(':').map(Number);
        const defaultEndTimeInMinutes = defaultEndMinutes[0] * 60 + defaultEndMinutes[1];
        if (endTimeInMinutes < defaultEndTimeInMinutes) {
            toTimeValue = defaultEndTime;
            toTimeInput.value = toTimeValue;
        }
    }
}

function updateTotal() {
    if (!startDate || !endDate) return;

    const startHour = fromTimeInput.value;
    const endHour = toTimeInput.value;
    
    const start = new Date(`${startDate.toISOString().split('T')[0]} ${startHour}`);
    const end = new Date(`${endDate.toISOString().split('T')[0]} ${endHour}`);
    const hours = (end - start) / (1000 * 60 * 60);

    if (hours <= 0) {
        totalDisplay.textContent = "0";
        return;
    }

    const day = Math.floor(hours / 24);
    const remain = hours % 24;
    const totalDays = day + (remain > 12 ? 1 : remain > 0 ? 0.5 : 0);
    
    const pricePerDay = parseFloat(tempPrice.textContent.replace('/Day', '')) || 0;
    const totalPrice = (pricePerDay * totalDays).toFixed(2);
    
    totalDisplay.textContent = `$${totalPrice}`;
}

fromDateInput._flatpickr.config.onChange.push(() => {
    startDate = fromDateInput._flatpickr.selectedDates[0];
    updateToDateMin();
    capNhatGioBatDau();
    capNhatGioKetThuc();
    updateTotal();
    if (document.getElementById('start-date-hidden')) {
        document.getElementById('start-date-hidden').value = startDate ? fromDateInput._flatpickr.formatDate(startDate, 'Y-m-d') : '';
        document.getElementById('login-fromDate').value=startDate ? fromDateInput._flatpickr.formatDate(startDate, 'Y-m-d') : '';
        document.getElementById('register-fromDate').value=startDate ? fromDateInput._flatpickr.formatDate(startDate, 'Y-m-d') : '';
    }
});

toDateInput._flatpickr.config.onChange.push(() => {
    endDate = toDateInput._flatpickr.selectedDates[0];
    capNhatGioKetThuc();
    updateTotal();
    if (document.getElementById('end-date-hidden')) {
        document.getElementById('end-date-hidden').value = endDate ? toDateInput._flatpickr.formatDate(endDate, 'Y-m-d') : '';
        document.getElementById('login-toDate').value = endDate ? toDateInput._flatpickr.formatDate(endDate, 'Y-m-d') : '';
        document.getElementById('register-toDate').value = endDate ? toDateInput._flatpickr.formatDate(endDate, 'Y-m-d') : '';
    }
});

fromTimeInput.addEventListener("change", () => {
    fromTimeValue = fromTimeInput.value;
    capNhatGioKetThuc();
    updateTotal();
    if (document.getElementById('start-time-hidden')) {
        document.getElementById('start-time-hidden').value = fromTimeValue;
        document.getElementById('login-fromTime').value = fromTimeValue;
        document.getElementById('register-fromTime').value = fromTimeValue;
    }
});

toTimeInput.addEventListener("change", () => {
    toTimeValue = toTimeInput.value;
    updateTotal();
    if (document.getElementById('end-time-hidden')) {
        document.getElementById('end-time-hidden').value = toTimeValue;
        document.getElementById('login-toTime').value = toTimeValue;
        document.getElementById('register-toTime').value = toTimeValue;
    }
});

capNhatGioBatDau();
capNhatGioKetThuc();
updateTotal();

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

    const pin = document.querySelector('input[name="pin"]').value;
    const newPassword = document.querySelector('input[name="new_password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
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
            document.querySelector('input[name="pin"]').value = '';
            document.getElementById('pin-error').style.display = 'none';
            document.getElementById('pin-error').textContent = '';
            document.querySelector('input[name="new_password"]').value='';
            document.querySelector('input[name="confirm_password"]').value='';
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
                        window.location.href = 'car-info.php';
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
                window.location.href = 'car-info.php';
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

// Đồng bộ message textarea với input hidden
if (document.getElementById('message') && document.getElementById('message-hidden')) {
    document.getElementById('message').addEventListener('input', () => {
        const temp = document.getElementById('message').value;
        document.getElementById('message-hidden').value = temp;
    });
}

// Xử lý popup ảnh zoom
const zoomableImages = document.querySelectorAll('.zoomable');
const popup = document.getElementById('imagePopup');
const popupImg = document.getElementById('popupImg');

if (zoomableImages && popup && popupImg) {
    zoomableImages.forEach(img => {
        img.addEventListener('click', () => {
            popupImg.src = img.src;
            popup.style.display = 'flex';
            setTimeout(() => {
                popup.classList.add('active');
            }, 10);
        });
    });

    popup.addEventListener('click', () => {
        popup.classList.remove('active');
        setTimeout(() => {
            popup.style.display = 'none';
        }, 300);
    });
}

// Xử lý booking form
if (document.getElementById('bookingForm')) {
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const errorElement = document.getElementById('renttime-error');
        errorElement.textContent = '';
        fetch('check-booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'user-info.php?section=2';
            } else {
                errorElement.textContent = data.error;
            }
        })
        .catch(error => {
            errorElement.textContent = 'An error occurred. Please try again.';
            console.error('Error:', error);
        });
    });
}