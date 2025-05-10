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

const elements = {
    openTimeModalBtn: document.getElementById('open-time-modal'),
    rentalTimeInput: document.getElementById('rental-time-input'),
    rentalTimeContainer: document.getElementById('rental-time'),
    startHourSelect: document.getElementById('start-hour'),
    endHourSelect: document.getElementById('end-hour'),
    summaryTime: document.getElementById('summary-time'),
    durationDisplay: document.getElementById('duration'),
    modal: document.getElementById('time-modal'),
    closeBtn: document.getElementById('close-time-modal'),
    continueBtn: document.querySelector('.continue-btn'),
    form: document.querySelector('#submitIndex'),
    vehicleList: document.getElementById('vehicle-list')
};

let startDate = null, endDate = null, startTimeValue = defaultStartTime, endTimeValue = null;

const fillHourSelect = (select) => {
    taoDanhSachThoiGian().forEach(time => select.add(new Option(time, time)));
};
fillHourSelect(elements.startHourSelect);
fillHourSelect(elements.endHourSelect);
elements.startHourSelect.value = startTimeValue;
elements.endHourSelect.value = taoDanhSachThoiGian()[taoDanhSachThoiGian().indexOf(startTimeValue) + 1] || "20:00";

const capNhatGioBatDau = () => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const times = taoDanhSachThoiGian();
    elements.startHourSelect.innerHTML = '';

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
        option.selected = time === startTimeValue || (!startTimeValue && time === defaultStartTime);
        elements.startHourSelect.appendChild(option);
    });

    if (isToday && startTimeValue) {
        const minutes = startTimeValue.split(':').map(Number);
        const timeInMinutes = minutes[0] * 60 + minutes[1];
        const defaultMinutes = defaultStartTime.split(':').map(Number);
        const defaultTimeInMinutes = defaultMinutes[0] * 60 + defaultMinutes[1];
        if (timeInMinutes < defaultTimeInMinutes) {
            startTimeValue = defaultStartTime;
            elements.startHourSelect.value = startTimeValue;
        }
    }
};

const capNhatGioKetThuc = () => {
    const startTime = elements.startHourSelect.value;
    const times = taoDanhSachThoiGian();
    elements.endHourSelect.innerHTML = '';

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
        option.selected = time === endTimeValue || (!endTimeValue && time === defaultEndTime);
        elements.endHourSelect.appendChild(option);
    });

    if (sameDay && endTimeValue) {
        const endMinutes = endTimeValue.split(':').map(Number);
        const endTimeInMinutes = endMinutes[0] * 60 + endMinutes[1];
        const defaultEndMinutes = defaultEndTime.split(':').map(Number);
        const defaultEndTimeInMinutes = defaultEndMinutes[0] * 60 + defaultEndMinutes[1];
        if (endTimeInMinutes < defaultEndTimeInMinutes) {
            endTimeValue = defaultEndTime;
            elements.endHourSelect.value = endTimeValue;
        }
    }
};

const updateSummary = () => {
    if (!startDate || !endDate) return;

    const startHour = elements.startHourSelect.value;
    const endHour = elements.endHourSelect.value;
    elements.summaryTime.textContent = `${startHour}, ${startDate.format('DD/MM/YYYY')} - ${endHour}, ${endDate.format('DD/MM/YYYY')}`;

    const start = new Date(`${startDate.format('YYYY-MM-DD')} ${startHour}`);
    const end = new Date(`${endDate.format('YYYY-MM-DD')} ${endHour}`);
    const hours = (end - start) / (1000 * 60 * 60);

    if (hours <= 0) {
        elements.durationDisplay.textContent = "0 Day";
        return;
    }

    const day = Math.floor(hours / 24);
    const remain = hours % 24;
    elements.durationDisplay.textContent = `${day + (remain > 12 ? 1 : remain > 0 ? 0.5 : 0)} Days`;
};

const renderVehicleList = (vehicles) => {
    elements.vehicleList.innerHTML = '';
    vehicles.forEach(vehicle => {
        const carItem = document.createElement('div');
        carItem.className = 'collection-car-item';
        carItem.id = 'collection-car-item-page';

        const img = document.createElement('img');
        img.src = vehicle.image ? `/Project/admin/${vehicle.image}` : '/Project/admin/default-car-image.jpg';
        img.alt = vehicle.name || 'Car Image';
        img.id = 'collection-car-item-img';
        carItem.appendChild(img);

        const carInfoContainer = document.createElement('div');
        carInfoContainer.className = 'car-info-container';

        const h2 = document.createElement('h2');
        h2.textContent = vehicle.name || 'Unknown Vehicle';
        carInfoContainer.appendChild(h2);

        const carInfo1 = document.createElement('div');
        carInfo1.className = 'car-info';

        const carPrice = document.createElement('div');
        carPrice.className = 'car-price';
        carPrice.innerHTML = `<h5>$${vehicle.price || 'N/A'}</h5><h6>/Day</h6>`;
        carInfo1.appendChild(carPrice);

        const carFlue = document.createElement('div');
        carFlue.className = 'car-flue';
        carFlue.innerHTML = `<i class="fa-solid fa-gas-pump"></i><h6>${vehicle.fuel || 'N/A'}</h6>`;
        carInfo1.appendChild(carFlue);
        carInfoContainer.appendChild(carInfo1);

        const carInfo2 = document.createElement('div');
        carInfo2.className = 'car-info';

        const carCapacity = document.createElement('div');
        carCapacity.className = 'car-capacity';
        carCapacity.innerHTML = `<i class="fa-solid fa-person-seat"></i><h6>${vehicle.seat || 'N/A'} seats</h6>`;
        carInfo2.appendChild(carCapacity);
        carInfoContainer.appendChild(carInfo2);

        const form = document.createElement('form');
        form.action = 'car-info.php';
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="vehicleId" value="${vehicle.id || ''}">
            <input type="hidden" name="fromDate" value="${startDate ? startDate.format('YYYY-MM-DD') : ''}">
            <input type="hidden" name="toDate" value="${endDate ? endDate.format('YYYY-MM-DD') : ''}">
            <input type="hidden" name="fromTime" value="${elements.startHourSelect.value}">
            <input type="hidden" name="toTime" value="${elements.endHourSelect.value}">
            <button class="btn-2 btn-car btn-collection-page">Book Now</button>
        `;
        carInfoContainer.appendChild(form);
        carItem.appendChild(carInfoContainer);
        elements.vehicleList.appendChild(carItem);
    });
};

const fetchVehicles = () => {
    const formData = new FormData(elements.form);
    fetch(window.location.href, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            console.log('Danh sách xe khả dụng:', data);
            renderVehicleList(data);
        })
        .catch(error => {
            console.error('Lỗi:', error);
            elements.vehicleList.innerHTML = '<p>Lỗi khi tải danh sách xe. Vui lòng thử lại.</p>';
        });
};

window.addEventListener('load', () => {
    const minDate = new Date();
    minDate.setHours(0, 0, 0, 0);

    const initPicker = () => {
        const screenWidth = window.innerWidth;
        let monthsToShow = 2; 
        let columnsToShow = 2;

        if (screenWidth < 768) {
            monthsToShow = 3;
            columnsToShow = 1;
        }

        // Khởi tạo Litepicker
        const picker = new Litepicker({
            element: elements.rentalTimeInput,
            parentEl: elements.rentalTimeContainer,
            inlineMode: true,
            singleMode: false,
            numberOfMonths: monthsToShow,
            numberOfColumns: columnsToShow,
            format: 'YYYY-MM-DD',
            lang: 'vi-VN',
            minDate: minDate,
            splitView: false,
            setup: picker => {
                picker.on('selected', (start, end) => {
                    startDate = start;
                    endDate = end;
                    capNhatGioBatDau();
                    capNhatGioKetThuc();
                    updateSummary();
                    document.getElementById('start-date-hidden').value = startDate ? startDate.format('YYYY-MM-DD') : '';
                    document.getElementById('end-date-hidden').value = endDate ? endDate.format('YYYY-MM-DD') : '';
                    document.getElementById('start-time-hidden').value = elements.startHourSelect.value;
                    document.getElementById('end-time-hidden').value = elements.endHourSelect.value;
                });
            }
        });

        picker.setDateRange(new Date(), new Date(Date.now() + 24 * 60 * 60 * 1000));
        document.getElementById('time-range').textContent = elements.summaryTime.textContent;
        fetchVehicles();
    };

    initPicker();

    window.addEventListener('resize', () => {
        document.querySelector('.litepicker')?.remove();
        initPicker();
    });
});

elements.openTimeModalBtn.addEventListener('click', () => elements.modal.style.display = 'flex');
elements.closeBtn.addEventListener('click', () => elements.modal.style.display = 'none');
elements.continueBtn.addEventListener('click', () => {
    if (elements.summaryTime.textContent !== 'Chưa chọn') {
        elements.rentalTimeInput.value = elements.summaryTime.textContent;
        document.getElementById('time-range').textContent = elements.summaryTime.textContent;
        document.getElementById('start-date-hidden').value = startDate ? startDate.format('YYYY-MM-DD') : '';
        document.getElementById('end-date-hidden').value = endDate ? endDate.format('YYYY-MM-DD') : '';
        document.getElementById('start-time-hidden').value = elements.startHourSelect.value;
        document.getElementById('end-time-hidden').value = elements.endHourSelect.value;
    }
    elements.modal.style.display = 'none';
});

elements.startHourSelect.addEventListener('change', () => {
    startTimeValue = elements.startHourSelect.value;
    capNhatGioKetThuc();
    updateSummary();
    document.getElementById('start-time-hidden').value = startTimeValue;
});

elements.endHourSelect.addEventListener('change', () => {
    endTimeValue = elements.endHourSelect.value;
    updateSummary();
    document.getElementById('end-time-hidden').value = endTimeValue;
});

elements.form.addEventListener('submit', (event) => {
    event.preventDefault();
    fetchVehicles();
});

capNhatGioBatDau();
capNhatGioKetThuc();

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
                        window.location.href = 'collection.php';
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
                window.location.href = 'collection.php';
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