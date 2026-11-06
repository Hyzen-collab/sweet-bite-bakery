<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'FAQ';
$activePage      = 'faq';
$metaDescription = 'Answers to common questions about ordering cakes, delivery and payment at Sweet Bite Bakery.';

// Question => answer
$faqs = [
    'How early should I order a cake?' =>
        'Please order at least 2 days before for birthday cakes and 2 to 3 weeks before for wedding cakes. During Avurudu, Vesak and Christmas, please book even earlier.',
    'Do you deliver?' =>
        'Yes. We deliver in ' . setting('delivery_areas') . '. The delivery charge depends on the distance and is confirmed when you order.',
    'How can I pay?' =>
        'Online payment is not available yet. You can pay cash when you collect or receive the cake, or by bank transfer. For custom and wedding cakes we ask for an advance payment to confirm the order.',
    'Can I get a cake with my own design?' =>
        'Of course. Send us a photo or describe your idea in the custom order form, and we will tell you if it is possible and how much it will cost.',
    'Do you make eggless or vegetarian cakes?' =>
        'Yes, we can make eggless cakes on request. Please tell us when you order because they need to be prepared separately.',
    'What about allergies?' =>
        'Our kitchen uses nuts, milk, eggs and wheat. If someone has an allergy, please tell us before ordering so we can advise you honestly.',
    'How should I store the cake?' =>
        'Keep cream cakes in the fridge and take them out about 30 minutes before serving. Keep the cake away from direct sunlight and heat, especially in hot weather.',
    'What are your opening hours?' =>
        setting('hours_weekdays') . '. ' . setting('hours_sunday') . '. ' . setting('hours_note'),
    'Can I change or cancel my order?' =>
        'Please contact us as early as possible. Changes are easy before we start baking. Cancelling a custom cake after baking has started may not be possible.',
];

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <p>Quick answers to what customers ask us the most</p>
    </div>
</div>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="accordion" id="faqList">
                <?php $i = 0; foreach ($faqs as $question => $answer): $i++; ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i > 1 ? 'collapsed' : '' ?>" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                                <?= e($question) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 1 ? 'show' : '' ?>" data-bs-parent="#faqList">
                            <div class="accordion-body"><?= e($answer) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <p class="text-center mt-4">
                Still have a question? <a href="<?= url('contact.php') ?>">Contact us</a> or
                <a href="<?= e(whatsapp_link('Hi, I have a question.')) ?>" target="_blank" rel="noopener">message us on WhatsApp</a>.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
