<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Спасибо за заявку - <?= e(SITE_NAME) ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?= e(SITE_NAME) ?></h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-16">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8 md:p-12 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Спасибо за вашу заявку!</h2>
                <p class="text-lg text-gray-600 mb-2">Ваша заявка успешно отправлена.</p>
                <p class="text-gray-600">Наши специалисты свяжутся с вами в ближайшее время для уточнения деталей.</p>
            </div>
            
            <div class="space-y-4">
                <a href="/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                    Вернуться на главную
                </a>
                
                <p class="text-sm text-gray-500 mt-6">
                    Обычно обработка заявки занимает от 15 минут до 2 часов в рабочее время.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Все права защищены.</p>
            <p class="text-sm text-gray-400 mt-2">Сайт носит информационный характер и не является публичной офертой</p>
        </div>
    </footer>
</body>
</html>