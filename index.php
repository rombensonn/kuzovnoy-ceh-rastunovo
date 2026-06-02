<?php
$defaultConfig = [
    'SITE_NAME' => 'Кузовной цех',
    'PRIMARY_PHONE' => '+7 (901) 762-88-47',
    'SECONDARY_PHONES' => ['+7 (916) 693-03-10', '+7 (999) 919-01-90'],
    'ADDRESS' => 'Центральная ул., 33, село Растуново',
    'WORKING_HOURS' => 'Ежедневно 10:00-19:00',
    'YANDEX_MAPS_URL' => 'https://yandex.ru/maps/-/CPTomSNV',
    'BASE_URL' => '',
];

$userConfig = [];
if (is_file(__DIR__ . '/config.php')) {
    $loadedConfig = require __DIR__ . '/config.php';
    if (is_array($loadedConfig)) {
        $userConfig = $loadedConfig;
    }
}

$config = array_replace($defaultConfig, $userConfig);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function phone_href(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (strlen($digits) === 11 && $digits[0] === '8') {
        $digits = '7' . substr($digits, 1);
    }

    return 'tel:+' . $digits;
}

$paintServices = [
    ['Покраска бампера', 'Окрас нового бампера', '20 000 ₽', '1 шт.'],
    ['Покраска переднего крыла', 'Окрас нового переднего крыла', '20 000 ₽', '1 шт.'],
    ['Окрас капота', 'Окрас нового капота с 2 сторон', '30 000 ₽', '1 шт.'],
    ['Окрас двери', 'Окрас новой двери с 2 сторон', '20 000 ₽', '1 шт.'],
    ['Окрас порога', 'Окрас порога без дверного проема', '15 000 ₽', '1 шт.'],
];

$weldingServices = [
    ['Переварка порогов', 'Внешний короб порога при условии целых усилителей', '20 000 ₽', '1 шт.'],
    ['Переварка задних арок', 'При условии целой внутренней арки', '10 000 ₽', '1 шт.'],
    ['Переварка задних крыльев', 'Отсверливание по заводским точкам и сварка нового крыла', '40 000 ₽', '1 шт.'],
    ['Сварка задней панели', 'Отсверливание задней панели по заводским точкам и сварка', '15 000 ₽', '1 шт.'],
    ['Сварка панели крыши', 'Отсверливание крыши по заводским точкам и сварка', '60 000 ₽', '1 шт.'],
];

$otherServices = [
    ['Восстановительная полировка кузова', 'Средний по размерам автомобиль', '15 000 ₽', '1 авто'],
    ['Полировка фар', 'Полировка оптики автомобиля', '3 000 ₽', '2 шт.'],
    ['Замена лобового стекла', 'Переклейка с учетом материала', '5 000 ₽', '1 шт.'],
    ['Заправка авто кондиционера', 'Фреон, краситель, масло', '3 000 ₽', ''],
    ['Химчистка', 'Химчистка салона', '15 000 ₽', ''],
];

$inspectionServices = [
    ['Антигравийная пленка', 'Стоимость зависит от зоны оклейки и материала'],
    ['Ремонт бамперов', 'Стоимость зависит от повреждения и необходимости покраски'],
    ['Подбор и замена деталей', 'Согласуется после осмотра и проверки скрытых повреждений'],
];

$serviceOptions = [
    'Осмотр после ДТП',
    'Покраска бампера',
    'Покраска крыла',
    'Покраска капота',
    'Покраска двери',
    'Ремонт порогов',
    'Ремонт арок',
    'Полировка кузова',
    'Полировка фар',
    'Замена лобового стекла',
    'Заправка кондиционера',
    'Химчистка',
    'Другое',
];

$reviews = [
    [
        'author' => 'Сергей Давыдов',
        'topic' => 'Ремонт бампера после снегопада',
        'text' => 'Клиент отметил, что мастера объяснили ситуацию, предложили несколько вариантов решения и учли пожелания. В отзыве отдельно упомянута гарантия на выполненную работу.',
    ],
    [
        'author' => 'Анастасия Ф.',
        'topic' => 'Покраска трех дверей',
        'text' => 'По отзыву, цвет подобрали точно, покрытие получилось ровным, без подтеков и заметных переходов.',
    ],
    [
        'author' => 'Альберт Аксенов',
        'topic' => 'Ремонт задних арок Nissan Patrol',
        'text' => 'Клиент долго искал исполнителей для сложной работы; в процессе ремонта получал фото- и видеоотчеты.',
    ],
    [
        'author' => 'Светлана Камышникова',
        'topic' => 'Замена и покраска деталей',
        'text' => 'В отзыве отмечены подбор деталей, согласование, фото повреждений и выдача готового авто через четыре дня.',
    ],
];

$photoSlots = [
    ['src' => 'assets/img/work-painting.png', 'alt' => 'Индустриальная визуальная сцена покраски кузовной детали', 'caption' => 'Покраска и подготовка детали'],
    ['src' => 'assets/img/work-welding.png', 'alt' => 'Индустриальная визуальная сцена сварочного ремонта кузова', 'caption' => 'Сварочные работы и коррозия'],
    ['src' => 'assets/img/work-polishing.png', 'alt' => 'Индустриальная визуальная сцена полировки кузова', 'caption' => 'Полировка и восстановление блеска'],
];

$availablePhotos = array_values(array_filter($photoSlots, static function (array $photo): bool {
    return is_file(__DIR__ . '/' . $photo['src']);
}));

$sent = isset($_GET['sent']);
$formError = isset($_GET['error']);
$baseUrl = rtrim((string) $config['BASE_URL'], '/');
$canonical = $baseUrl !== '' ? $baseUrl . '/' : '';
$primaryPhone = (string) $config['PRIMARY_PHONE'];
$secondaryPhones = is_array($config['SECONDARY_PHONES'] ?? null) ? $config['SECONDARY_PHONES'] : [];
$mapsUrl = (string) $config['YANDEX_MAPS_URL'];

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => ['AutoRepair', 'LocalBusiness'],
    '@id' => $mapsUrl,
    'name' => $config['SITE_NAME'],
    'description' => 'Кузовной ремонт, покраска авто, сварочные работы, полировка, замена лобового стекла и заправка кондиционера в селе Растуново.',
    'telephone' => [$primaryPhone, ...$secondaryPhones],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Центральная ул., 33',
        'addressLocality' => 'село Растуново',
        'addressRegion' => 'Московская область',
        'addressCountry' => 'RU',
    ],
    'openingHoursSpecification' => [[
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        'opens' => '10:00',
        'closes' => '19:00',
    ]],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '4.9',
        'ratingCount' => '64',
    ],
    'paymentAccepted' => ['Cash', 'BankTransfer', 'Prepayment'],
];
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Кузовной ремонт и покраска авто в Растуново | Кузовной цех</title>
    <meta name="description" content="Кузовной цех в Растуново: покраска авто, сварочные работы, ремонт порогов и арок, полировка, стекло, кондиционер. Рейтинг 4,9, запись на осмотр.">
    <meta name="keywords" content="кузовной ремонт Растуново, покраска авто Домодедово, ремонт бамперов, сварочные работы авто, покраска бампера">
    <?php if ($canonical !== ''): ?>
        <link rel="canonical" href="<?= h($canonical); ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:title" content="Кузовной ремонт и покраска авто в Растуново">
    <meta property="og:description" content="Понятная смета после осмотра, согласование работ и запись на ремонт кузова автомобиля.">
    <meta property="og:locale" content="ru_RU">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script>
</head>
<body>
    <a class="skip-link" href="#main">Перейти к содержанию</a>

    <header class="site-header" data-header>
        <div class="container header-inner">
            <a class="brand" href="#top" aria-label="Кузовной цех">
                <span class="brand-mark" aria-hidden="true">КЦ</span>
                <span>
                    <strong><?= h((string) $config['SITE_NAME']); ?></strong>
                    <small>Растуново, Центральная 33</small>
                </span>
            </a>
            <nav class="nav-links" aria-label="Основная навигация">
                <a href="#services">Услуги</a>
                <a href="#prices">Цены</a>
                <a href="#process">Процесс</a>
                <a href="#reviews">Отзывы</a>
                <a href="#contacts">Контакты</a>
            </nav>
            <a class="header-phone" href="<?= h(phone_href($primaryPhone)); ?>">
                <span>Позвонить</span>
                <strong><?= h($primaryPhone); ?></strong>
            </a>
        </div>
    </header>

    <main id="main">
        <section class="hero section-band" id="top">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <div class="eyebrow">Кузовной ремонт, покраска, сварочные работы</div>
                    <h1>Кузовной ремонт и покраска авто в Растуново</h1>
                    <p class="lead">
                        Осмотрят повреждение, объяснят варианты ремонта, согласуют перечень работ, стоимость и детали до начала.
                        Подходит для ремонта после ДТП, коррозии, покраски отдельных элементов и восстановления внешнего вида.
                    </p>

                    <div class="hero-actions" aria-label="Основные действия">
                        <a class="btn btn-primary" href="#lead">Записаться на осмотр</a>
                        <a class="btn btn-secondary" href="<?= h(phone_href($primaryPhone)); ?>">Позвонить</a>
                    </div>

                    <dl class="trust-strip" aria-label="Ключевая информация">
                        <div>
                            <dt>Рейтинг</dt>
                            <dd>4,9 из 5</dd>
                        </div>
                        <div>
                            <dt>Оценки</dt>
                            <dd>64 на Яндекс.Картах</dd>
                        </div>
                        <div>
                            <dt>График</dt>
                            <dd><?= h((string) $config['WORKING_HOURS']); ?></dd>
                        </div>
                    </dl>
                </div>

                <aside class="hero-lead-card" aria-label="Короткая форма записи">
                    <div class="quick-card-head">
                        <span class="rating-badge">4,9</span>
                        <div>
                            <strong>Быстрая запись</strong>
                            <p>Оставьте телефон и задачу. Для точной цены мастер пригласит на осмотр.</p>
                        </div>
                    </div>

                    <form class="quick-form" action="lead.php" method="post" data-lead-form data-short-form novalidate>
                        <div class="form-status" role="status" aria-live="polite" data-form-status></div>

                        <label>
                            <span>Телефон *</span>
                            <input type="tel" name="phone" autocomplete="tel" inputmode="tel" placeholder="+7 ___ ___-__-__" required>
                        </label>

                        <label>
                            <span>Задача</span>
                            <select name="service" data-service-select>
                                <?php foreach ($serviceOptions as $option): ?>
                                    <option value="<?= h($option); ?>"><?= h($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <input type="hidden" name="name" value="">
                        <input type="hidden" name="car" value="">
                        <input type="hidden" name="comment" value="Заявка из короткой формы первого экрана">
                        <input type="hidden" name="privacy" value="1">

                        <label class="trap-field" aria-hidden="true">
                            <span>Сайт</span>
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </label>

                        <button class="btn btn-primary btn-wide" type="submit">Жду звонка</button>
                        <p class="form-help">Нажимая кнопку, вы соглашаетесь на обратную связь по заявке.</p>
                    </form>

                    <a class="map-link" href="<?= h($mapsUrl); ?>" target="_blank" rel="noopener">Открыть карточку на Яндекс.Картах</a>
                </aside>
            </div>
        </section>

        <section class="section" id="services">
            <div class="container">
                <div class="section-heading">
                    <p class="section-kicker">Что можно сделать</p>
                    <h2>Выберите ситуацию, а не технический термин</h2>
                    <p>Блок помогает быстро понять, с какой задачей можно обратиться и когда потребуется осмотр машины.</p>
                </div>

                <div class="scenario-grid">
                    <article class="scenario-card">
                        <span class="scenario-icon" aria-hidden="true">01</span>
                        <h3>Нужно покрасить деталь</h3>
                        <p>Бампер, крыло, капот, дверь или порог. В прайсе указаны базовые цены для новых деталей и отдельных элементов.</p>
                        <button class="text-action" type="button" data-service-name="Покраска бампера">Записаться по покраске</button>
                    </article>
                    <article class="scenario-card">
                        <span class="scenario-icon" aria-hidden="true">02</span>
                        <h3>Есть коррозия или сгнили пороги</h3>
                        <p>Переварка порогов, арок, задних крыльев, панели крыши и задней панели. Итог зависит от состояния внутренних элементов.</p>
                        <button class="text-action" type="button" data-service-name="Ремонт порогов">Записаться на осмотр</button>
                    </article>
                    <article class="scenario-card">
                        <span class="scenario-icon" aria-hidden="true">03</span>
                        <h3>Машина потеряла внешний вид</h3>
                        <p>Восстановительная полировка кузова, полировка фар и химчистка помогают привести авто в порядок без кузовной замены деталей.</p>
                        <button class="text-action" type="button" data-service-name="Полировка кузова">Уточнить стоимость</button>
                    </article>
                    <article class="scenario-card">
                        <span class="scenario-icon" aria-hidden="true">04</span>
                        <h3>Нужен быстрый сервис</h3>
                        <p>Замена лобового стекла, заправка кондиционера, ремонт бамперов и подбор деталей после ДТП.</p>
                        <button class="text-action" type="button" data-service-name="Другое">Описать задачу</button>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-muted" id="prices">
            <div class="container">
                <div class="section-heading split-heading">
                    <div>
                        <p class="section-kicker">Прайс</p>
                        <h2>Цены из карточки, сгруппированные по понятным работам</h2>
                    </div>
                    <p class="price-note">
                        Если после разборки найдутся скрытые повреждения, новые работы и детали должны быть согласованы отдельно.
                    </p>
                </div>

                <div class="price-layout">
                    <article class="price-group">
                        <div class="price-group-head">
                            <h3>Покраска</h3>
                            <p>Окрас отдельных новых деталей и элементов кузова.</p>
                        </div>
                        <div class="price-table" role="table" aria-label="Цены на покраску">
                            <?php foreach ($paintServices as [$name, $desc, $price, $unit]): ?>
                                <div class="price-row" role="row">
                                    <div role="cell">
                                        <strong><?= h($name); ?></strong>
                                        <span><?= h($desc); ?></span>
                                    </div>
                                    <div class="price-value" role="cell">
                                        <b><?= h($price); ?></b>
                                        <?php if ($unit !== ''): ?><small><?= h($unit); ?></small><?php endif; ?>
                                    </div>
                                    <button class="mini-action" type="button" data-service-name="<?= h($name); ?>">Запись</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>

                    <article class="price-group">
                        <div class="price-group-head">
                            <h3>Сварочные работы</h3>
                            <p>Ремонт коррозии и замена кузовных панелей при понятных условиях.</p>
                        </div>
                        <div class="price-table" role="table" aria-label="Цены на сварочные работы">
                            <?php foreach ($weldingServices as [$name, $desc, $price, $unit]): ?>
                                <div class="price-row" role="row">
                                    <div role="cell">
                                        <strong><?= h($name); ?></strong>
                                        <span><?= h($desc); ?></span>
                                    </div>
                                    <div class="price-value" role="cell">
                                        <b><?= h($price); ?></b>
                                        <?php if ($unit !== ''): ?><small><?= h($unit); ?></small><?php endif; ?>
                                    </div>
                                    <button class="mini-action" type="button" data-service-name="<?= h($name); ?>">Запись</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>

                    <article class="price-group">
                        <div class="price-group-head">
                            <h3>Полировка и быстрые работы</h3>
                            <p>Внешний вид, стекло, кондиционер и химчистка.</p>
                        </div>
                        <div class="price-table" role="table" aria-label="Цены на полировку и прочие работы">
                            <?php foreach ($otherServices as [$name, $desc, $price, $unit]): ?>
                                <div class="price-row" role="row">
                                    <div role="cell">
                                        <strong><?= h($name); ?></strong>
                                        <span><?= h($desc); ?></span>
                                    </div>
                                    <div class="price-value" role="cell">
                                        <b><?= h($price); ?></b>
                                        <?php if ($unit !== ''): ?><small><?= h($unit); ?></small><?php endif; ?>
                                    </div>
                                    <button class="mini-action" type="button" data-service-name="<?= h($name); ?>">Запись</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </div>

                <div class="inspection-list" aria-label="Работы с расчетом после осмотра">
                    <?php foreach ($inspectionServices as [$name, $desc]): ?>
                        <article>
                            <strong><?= h($name); ?></strong>
                            <p><?= h($desc); ?></p>
                            <span>Стоимость после осмотра</span>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section" id="process">
            <div class="container">
                <div class="section-heading">
                    <p class="section-kicker">Как проходит ремонт</p>
                    <h2>Чтобы цена и срок не были сюрпризом</h2>
                    <p>Процесс собран вокруг главных рисков клиента: переплата, скрытые работы, непонятные сроки и отсутствие связи.</p>
                </div>

                <ol class="process-list">
                    <li>
                        <span>1</span>
                        <div>
                            <h3>Осмотр повреждения</h3>
                            <p>Мастер смотрит деталь, коррозию или последствия ДТП и объясняет, какие варианты ремонта возможны.</p>
                        </div>
                    </li>
                    <li>
                        <span>2</span>
                        <div>
                            <h3>Смета и ориентир по сроку</h3>
                            <p>До начала работ фиксируется перечень работ. Если случай сложный, итоговая стоимость считается после осмотра.</p>
                        </div>
                    </li>
                    <li>
                        <span>3</span>
                        <div>
                            <h3>Согласование деталей</h3>
                            <p>Запчасти, внутренние повреждения и дополнительные операции согласуются с владельцем автомобиля.</p>
                        </div>
                    </li>
                    <li>
                        <span>4</span>
                        <div>
                            <h3>Ремонт и выдача</h3>
                            <p>После кузовных работ, покраски или полировки автомобиль выдают с понятным составом выполненных операций.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <?php if ($availablePhotos !== []): ?>
            <section class="section visual-section" id="photos">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Визуальный язык</p>
                        <h2>Индустриальные сцены под реальные услуги сервиса</h2>
                        <p>Эти изображения задают атмосферу сайта: покраска, сварка и полировка. Реальные фото работ можно поставить поверх той же сетки без изменения UX.</p>
                    </div>
                    <div class="photo-grid">
                        <?php foreach ($availablePhotos as $photo): ?>
                            <figure class="photo-card">
                                <img src="<?= h($photo['src']); ?>" alt="<?= h($photo['alt']); ?>" loading="lazy">
                                <figcaption><?= h($photo['caption']); ?></figcaption>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="section section-trust" id="reviews">
            <div class="container trust-grid">
                <div class="section-heading">
                    <p class="section-kicker">Доверие</p>
                    <h2>Что клиенты уже отмечают в отзывах</h2>
                    <p>Рейтинг 4,9 и 64 оценки важны не сами по себе. Для клиента полезнее понять, какие риски люди уже проверяли на практике.</p>
                    <a class="btn btn-secondary" href="<?= h($mapsUrl); ?>" target="_blank" rel="noopener">Смотреть карточку на Яндекс.Картах</a>
                </div>

                <div class="review-list">
                    <?php foreach ($reviews as $review): ?>
                        <article class="review-card">
                            <div>
                                <strong><?= h($review['author']); ?></strong>
                                <span><?= h($review['topic']); ?></span>
                            </div>
                            <p><?= h($review['text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section section-muted" id="faq">
            <div class="container">
                <div class="section-heading">
                    <p class="section-kicker">FAQ</p>
                    <h2>Коротко о цене, сроках и согласовании</h2>
                </div>
                <div class="faq-list">
                    <details>
                        <summary>Когда цена может измениться?</summary>
                        <p>Если при осмотре или разборке обнаружены скрытые повреждения, необходимость других деталей или дополнительных операций. Такие работы нужно согласовать до выполнения.</p>
                    </details>
                    <details>
                        <summary>Как согласуются запчасти?</summary>
                        <p>По отзывам клиентов, сервис помогает с подбором и заказом деталей. Конкретные варианты и стоимость лучше согласовать до начала ремонта.</p>
                    </details>
                    <details>
                        <summary>Можно ли понять срок до ремонта?</summary>
                        <p>Ориентир по сроку дают после осмотра и понимания объема работ. Для кузовных работ срок зависит от повреждений, запчастей, подготовки и покраски.</p>
                    </details>
                    <details>
                        <summary>Какие автомобили принимаете?</summary>
                        <p>В карточке указаны отечественные, импортные, легковые и коммерческие автомобили.</p>
                    </details>
                    <details>
                        <summary>Как оплатить?</summary>
                        <p>В карточке указаны предоплата, наличные и банковский перевод.</p>
                    </details>
                </div>
            </div>
        </section>

        <section class="section lead-section" id="lead">
            <div class="container lead-grid">
                <div class="lead-copy">
                    <p class="section-kicker">Запись на осмотр</p>
                    <h2>Опишите повреждение, и с вами свяжутся</h2>
                    <p>
                        Достаточно оставить телефон и выбрать задачу. Для точной сметы по кузову лучше приехать на осмотр:
                        так меньше риск переплаты и неожиданных работ.
                    </p>
                    <div class="contact-short">
                        <a href="<?= h(phone_href($primaryPhone)); ?>"><?= h($primaryPhone); ?></a>
                        <span><?= h((string) $config['WORKING_HOURS']); ?></span>
                    </div>
                </div>

                <form class="lead-form" action="lead.php" method="post" data-lead-form novalidate>
                    <?php if ($sent): ?>
                        <div class="form-status is-success" role="status">Заявка отправлена. Если вопрос срочный, позвоните по основному номеру.</div>
                    <?php elseif ($formError): ?>
                        <div class="form-status is-error" role="status">Заявка не отправилась. Позвоните по телефону <?= h($primaryPhone); ?>.</div>
                    <?php else: ?>
                        <div class="form-status" role="status" aria-live="polite" data-form-status></div>
                    <?php endif; ?>

                    <div class="field-grid">
                        <label>
                            <span>Имя</span>
                            <input type="text" name="name" autocomplete="name" placeholder="Как к вам обращаться">
                        </label>
                        <label>
                            <span>Телефон *</span>
                            <input type="tel" name="phone" autocomplete="tel" inputmode="tel" placeholder="+7 ___ ___-__-__" required>
                        </label>
                    </div>

                    <label>
                        <span>Что нужно сделать</span>
                        <select name="service" data-service-select>
                            <?php foreach ($serviceOptions as $option): ?>
                                <option value="<?= h($option); ?>"><?= h($option); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        <span>Автомобиль</span>
                        <input type="text" name="car" placeholder="Например: Kia Rio, BMW E39, Skoda A5">
                    </label>

                    <label>
                        <span>Комментарий</span>
                        <textarea name="comment" rows="4" placeholder="Коротко опишите повреждение, деталь или удобное время для звонка"></textarea>
                    </label>

                    <label class="checkbox-line">
                        <input type="checkbox" name="privacy" value="1" required>
                        <span>Согласен(на) на обработку данных для обратной связи по заявке.</span>
                    </label>

                    <label class="trap-field" aria-hidden="true">
                        <span>Сайт</span>
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </label>

                    <button class="btn btn-primary btn-wide" type="submit">Отправить заявку</button>
                    <p class="form-help">Форма отправляет данные в Telegram. Срочные вопросы быстрее решить звонком.</p>
                </form>
            </div>
        </section>

        <section class="section contacts-section" id="contacts">
            <div class="container contacts-grid">
                <div>
                    <p class="section-kicker">Контакты</p>
                    <h2>Приехать на осмотр или позвонить</h2>
                    <div class="contact-list">
                        <div>
                            <span>Основной телефон</span>
                            <a href="<?= h(phone_href($primaryPhone)); ?>"><?= h($primaryPhone); ?></a>
                        </div>
                        <?php foreach ($secondaryPhones as $phone): ?>
                            <div>
                                <span>Дополнительный телефон</span>
                                <a href="<?= h(phone_href((string) $phone)); ?>"><?= h((string) $phone); ?></a>
                            </div>
                        <?php endforeach; ?>
                        <div>
                            <span>Адрес</span>
                            <p><?= h((string) $config['ADDRESS']); ?></p>
                        </div>
                        <div>
                            <span>График</span>
                            <p><?= h((string) $config['WORKING_HOURS']); ?></p>
                        </div>
                        <div>
                            <span>Оплата</span>
                            <p>Предоплата, наличные, банковский перевод</p>
                        </div>
                    </div>
                </div>

                <a class="map-card" href="<?= h($mapsUrl); ?>" target="_blank" rel="noopener" aria-label="Открыть маршрут до Кузовного цеха на Яндекс.Картах">
                    <span>Яндекс.Карты</span>
                    <strong><?= h((string) $config['ADDRESS']); ?></strong>
                    <em>Открыть маршрут</em>
                </a>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <span>© <?= date('Y'); ?> <?= h((string) $config['SITE_NAME']); ?></span>
            <a href="<?= h($mapsUrl); ?>" target="_blank" rel="noopener">Карточка бизнеса на Яндекс.Картах</a>
        </div>
    </footer>

    <div class="mobile-cta" aria-label="Быстрые действия">
        <a class="btn btn-secondary" href="<?= h(phone_href($primaryPhone)); ?>"><span class="desktop-label">Позвонить</span><span class="mobile-label">Звонок</span></a>
        <a class="btn btn-primary" href="#lead"><span class="desktop-label">Записаться</span><span class="mobile-label">Запись</span></a>
    </div>

    <script src="assets/js/main.js" defer></script>
</body>
</html>
