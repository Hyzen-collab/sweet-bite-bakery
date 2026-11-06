</main>

<footer class="site-footer pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="<?= asset('images/logo.png') ?>" alt="Sweet Bite Bakery logo" width="54" height="54">
                    <span class="h4 mb-0">Sweet Bite Bakery</span>
                </div>
                <p class="small"><?= e(setting('tagline')) ?>. Birthday cakes, wedding cakes, cupcakes, pastries and fresh bread.</p>
            </div>
            <div class="col-6 col-md-2">
                <h3 class="h6 text-uppercase">Explore</h3>
                <ul class="list-unstyled small footer-links">
                    <li><a href="<?= url('products.php') ?>">Products</a></li>
                    <li><a href="<?= url('gallery.php') ?>">Cake Designs</a></li>
                    <li><a href="<?= url('prices.php') ?>">Prices</a></li>
                    <li><a href="<?= url('order.php') ?>">Custom Cake Order</a></li>
                    <li><a href="<?= url('faq.php') ?>">FAQ</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="h6 text-uppercase">Visit us</h3>
                <p class="small mb-1"><?= e(setting('address')) ?></p>
                <p class="small mb-1">Phone: <a href="tel:<?= e(preg_replace('/\s/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
                <p class="small mb-1">Email: <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
            </div>
            <div class="col-md-3">
                <h3 class="h6 text-uppercase">Opening hours</h3>
                <p class="small mb-1"><?= e(setting('hours_weekdays')) ?></p>
                <p class="small mb-3"><?= e(setting('hours_sunday')) ?></p>
                <a class="btn btn-sm btn-light me-1" href="<?= e(setting('facebook')) ?>" target="_blank" rel="noopener">Facebook</a>
                <a class="btn btn-sm btn-success" href="<?= e(whatsapp_link('Hi, I would like to ask about a cake.')) ?>" target="_blank" rel="noopener">WhatsApp</a>
            </div>
        </div>
        <hr class="border-light opacity-25 mt-4">
        <p class="text-center small mb-0">&copy; <?= date('Y') ?> Sweet Bite Bakery, Colombo. All rights reserved.</p>
    </div>
</footer>

<!-- Floating WhatsApp button (shown on every page) -->
<a href="<?= e(whatsapp_link('Hi, I would like to ask about a cake.')) ?>" class="whatsapp-float"
   target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg viewBox="0 0 24 24" width="30" height="30" fill="#fff" aria-hidden="true"><path d="M12 2C6.5 2 2 6.2 2 11.4c0 2 .7 3.900 1.900 5.400L2.800 21l4.500-1.300c1.400.7 2.900 1 4.700 1 5.500 0 10-4.200 10-9.400S17.500 2 12 2z"/></svg>
</a>

<button type="button" id="backToTop" class="back-to-top" aria-label="Back to top">&uarr;</button>

<script src="<?= asset('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
