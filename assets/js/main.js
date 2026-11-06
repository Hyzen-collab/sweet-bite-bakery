/* =========================================================
   Sweet Bite Bakery - main JavaScript
   1. Back to top button
   2. Cake design gallery (filter + big picture)
   3. Order form checks
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
    setupBackToTop();
    setupGallery();
    setupOrderForm();
});

/* ---------- 1. Back to top ---------- */
function setupBackToTop() {
    var button = document.getElementById('backToTop');
    if (!button) return;

    window.addEventListener('scroll', function () {
        button.classList.toggle('show', window.scrollY > 400);
    });
    button.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/* ---------- 2. Gallery ---------- */
function setupGallery() {
    var items = document.querySelectorAll('.gallery-item');
    if (!items.length) return;

    // Filter buttons
    var buttons = document.querySelectorAll('[data-filter]');
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var wanted = btn.getAttribute('data-filter');

            buttons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            items.forEach(function (item) {
                var show = wanted === 'all' || item.getAttribute('data-occasion') === wanted;
                item.parentElement.style.display = show ? '' : 'none';
            });
        });
    });

    // Click a picture to see it bigger
    var modalEl = document.getElementById('imageModal');
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);
    var bigImage = document.getElementById('imageModalPicture');
    var bigTitle = document.getElementById('imageModalTitle');

    items.forEach(function (item) {
        item.addEventListener('click', function () {
            bigImage.src = item.getAttribute('data-src');
            bigImage.alt = item.getAttribute('data-title');
            bigTitle.textContent = item.getAttribute('data-title');
            modal.show();
        });
    });
}

/* ---------- 3. Order form ---------- */
function setupOrderForm() {
    var form = document.getElementById('orderForm');
    if (!form) return;

    var dateInput = document.getElementById('required_date');
    var dateWarning = document.getElementById('dateWarning');
    var deliveryRadios = form.querySelectorAll('input[name="delivery_type"]');
    var addressBox = document.getElementById('addressBox');

    // Earliest date = today (a custom cake needs about 2 days)
    var today = new Date();
    dateInput.min = today.toISOString().split('T')[0];

    // Warn if the chosen date is very close
    dateInput.addEventListener('change', function () {
        var chosen = new Date(dateInput.value);
        var days = Math.ceil((chosen - today) / (1000 * 60 * 60 * 24));
        dateWarning.classList.toggle('d-none', !(dateInput.value && days < 2));
    });

    // Show the address box only for delivery
    function toggleAddress() {
        var delivery = form.querySelector('input[name="delivery_type"]:checked').value === 'delivery';
        addressBox.classList.toggle('d-none', !delivery);
        document.getElementById('address').required = delivery;
    }
    deliveryRadios.forEach(function (r) { r.addEventListener('change', toggleAddress); });
    toggleAddress();

    // Check the form before sending
    form.addEventListener('submit', function (event) {
        var phone = document.getElementById('phone');
        var cleaned = phone.value.replace(/[\s\-()]/g, '');
        var phoneOk = /^(\+94|0)\d{9}$/.test(cleaned);

        phone.setCustomValidity(phoneOk ? '' : 'Please enter a valid Sri Lankan phone number, for example 077 123 4567');

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
}
