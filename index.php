<?php
require_once 'config.php';

$db = getDB();

// Получаем активные офферы
$offers = $db->query("SELECT * FROM offers WHERE is_active = 1 ORDER BY position ASC, id ASC")->fetchAll();

// Получаем рекомендации
$recommendations = $db->query("SELECT * FROM recommendations WHERE is_active = 1 ORDER BY position ASC")->fetchAll();

// Получаем отзывы
$reviews = $db->query("SELECT * FROM reviews WHERE is_active = 1 ORDER BY position ASC LIMIT 5")->fetchAll();

// Получаем информацию о кредиторах
$creditors = $db->query("SELECT * FROM creditors_info WHERE is_active = 1 ORDER BY position ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(SITE_NAME) ?> - Быстрые займы без отказа</title>
    <meta name="description" content="Лучшие предложения по займам онлайн. Моментальное одобрение, перевод на карту за 15 минут.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?= e(SITE_NAME) ?></h1>
            <p class="text-gray-600 mt-2">Подберите займ онлайн за 2 минуты</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Offers Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Лучшие предложения</h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($offers as $offer): ?>
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 offer-card">
                    <!-- Logo and Rating -->
                    <div class="flex items-center justify-between mb-4">
                        <img src="<?= e($offer['logo_url']) ?>" alt="<?= e($offer['name']) ?>" class="h-12 object-contain" onerror="this.src='/assets/img/placeholder-logo.svg'">
                        <div class="flex items-center">
                            <span class="star-rating mr-1">★</span>
                            <span class="font-semibold"><?= e($offer['rating']) ?></span>
                        </div>
                    </div>
                    
                    <!-- Offer Details -->
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Сумма:</span>
                            <span class="font-medium"><?= number_format($offer['min_amount'], 0, '', ' ') ?> – <?= number_format($offer['max_amount'], 0, '', ' ') ?> ₽</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Срок:</span>
                            <span class="font-medium"><?= e($offer['min_term']) ?> – <?= e($offer['max_term']) ?> дней</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Возраст:</span>
                            <span class="font-medium">от <?= e($offer['min_age']) ?> до <?= e($offer['max_age']) ?> лет</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Одобрение:</span>
                            <span class="font-medium approval-<?= $offer['approval_level'] == 'низкое' ? 'low' : ($offer['approval_level'] == 'среднее' ? 'medium' : 'high') ?>">
                                <?= ucfirst(e($offer['approval_level'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- CTA Button -->
                    <a href="<?= e($offer['partner_link']) ?>" target="_blank" rel="nofollow" 
                       class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition-colors cta-button">
                        ПОЛУЧИТЬ ЗАЙМ
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Recommendations Section -->
        <section class="mb-12 bg-white rounded-lg shadow-md p-6 md:p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Как повысить шансы на получение займа?</h2>
            <div class="space-y-4">
                <?php foreach ($recommendations as $rec): ?>
                <details class="group">
                    <summary class="cursor-pointer list-none flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-800"><?= e($rec['title']) ?></span>
                        <span class="text-gray-500 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="p-4 text-gray-600">
                        <?= nl2br(e($rec['content'])) ?>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Reviews Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Отзывы клиентов</h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($reviews as $review): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800"><?= e($review['author_name']) ?></h3>
                        <div class="flex text-yellow-400">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                <span>★</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-2"><?= e($review['review_text']) ?></p>
                    <p class="text-sm text-gray-400"><?= date('d.m.Y', strtotime($review['review_date'])) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Creditors Info Section -->
        <section class="mb-12 bg-gray-100 rounded-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Информация о кредиторах</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <?php foreach ($creditors as $creditor): ?>
                <div class="bg-white rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-3"><?= e($creditor['company_name']) ?></h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><span class="font-medium">Юр. лицо:</span> <?= e($creditor['legal_name']) ?></p>
                        <p><span class="font-medium">ОГРН:</span> <?= e($creditor['ogrn']) ?></p>
                        <p><span class="font-medium">ИНН:</span> <?= e($creditor['inn']) ?></p>
                        <?php if ($creditor['license_number']): ?>
                        <p><span class="font-medium">Лицензия:</span> <?= e($creditor['license_number']) ?></p>
                        <?php endif; ?>
                        <p><span class="font-medium">Адрес:</span> <?= e($creditor['address']) ?></p>
                        <?php if ($creditor['phone']): ?>
                        <p><span class="font-medium">Телефон:</span> <?= e($creditor['phone']) ?></p>
                        <?php endif; ?>
                        <?php if ($creditor['website']): ?>
                        <p><span class="font-medium">Сайт:</span> <a href="https://<?= e($creditor['website']) ?>" target="_blank" class="text-blue-600 hover:underline"><?= e($creditor['website']) ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Fake Form Section -->
        <section class="mb-12 bg-white rounded-lg shadow-md p-6 md:p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Не нашли подходящий займ?</h2>
            <p class="text-gray-600 mb-6">Заполните форму, и мы подберем для вас индивидуальное предложение</p>
            
            <form action="thank-you.php" method="POST" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ФИО *</label>
                        <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон *</label>
                        <input type="tel" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Сумма займа *</label>
                        <input type="number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">СНИЛС</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ИНН</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Фото паспорта</label>
                        <input type="file" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дополнительная информация</label>
                    <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" required class="mr-2">
                    <label class="text-sm text-gray-600">Я согласен на обработку персональных данных</label>
                </div>
                
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                    Отправить заявку
                </button>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Все права защищены.</p>
            <p class="text-sm text-gray-400 mt-2">Сайт носит информационный характер и не является публичной офертой</p>
        </div>
    </footer>
</body>
</html>