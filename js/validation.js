// Form validation and AJAX submission
const contactForm = document.getElementById('contact-form');
const formMessages = document.getElementById('form-messages');

contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    formMessages.textContent = '';
    let valid = true;

    // Simple validation
    ['firstName', 'lastName', 'email', 'comments'].forEach(id => {
        const el = document.getElementById(id);
        if (!el.value.trim()) {
            el.style.borderColor = 'red';
            valid = false;
        } else {
            el.style.borderColor = '#333';
        }
    });

    const email = document.getElementById('email').value.trim();
    if (email && !/^[^@]+@[^@]+\.[^@]+$/.test(email)) {
        document.getElementById('email').style.borderColor = 'red';
        valid = false;
        formMessages.textContent = 'Please enter a valid email address.';
    }

    if (!document.getElementById('agree').checked) {
        valid = false;
        formMessages.textContent = 'You must agree to the Terms & Conditions.';
    }

    if (!valid) {
        if (!formMessages.textContent) formMessages.textContent = 'Please fill all required fields.';
        return;
    }

    // AJAX submit
    const formData = new FormData(contactForm);
    formMessages.textContent = 'Sending...';
    fetch('php/contact.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            formMessages.style.color = 'green';
            formMessages.textContent = data.message;
            contactForm.reset();
        } else {
            formMessages.style.color = 'red';
            formMessages.textContent = data.message;
        }
    })
    .catch(() => {
        formMessages.style.color = 'red';
        formMessages.textContent = 'An error occurred. Please try again.';
    });
});
