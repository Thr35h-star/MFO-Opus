<?php
require_once '../config.php';
session_start();

// Если админ уже авторизован, показываем панель управления
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $db = getDB();
    
    // Обработка действий
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'toggle_offer':
                    $id = (int)$_POST['id'];
                    $db->prepare("UPDATE offers SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
                    break;
                    
                case 'delete_offer':
                    $id = (int)$_POST['id'];
                    $db->prepare("DELETE FROM offers WHERE id = ?")->execute([$id]);
                    break;
            }
            header('Location: /admin/');
            exit;
        }
    }
    
    // Получаем все офферы
    $offers = $db->query("SELECT * FROM offers ORDER BY position ASC, id ASC")->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Админ-панель - <?= e(SITE_NAME) ?></title>
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="bg-gray-50">
        <!-- Header -->
        <header class="bg-gray-800 text-white">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold">Админ-панель</h1>
                <div class="flex items-center space-x-4">
                    <a href="/" target="_blank" class="hover:text-gray-300">Перейти на сайт</a>
                    <form method="POST" action="login.php" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-sm">Выйти</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Navigation -->
            <nav class="bg-white rounded-lg shadow-md p-4 mb-6">
                <ul class="flex flex-wrap gap-4">
                    <li><a href="/admin/" class="text-blue-600 font-semibold">Офферы</a></li>
                    <li><a href="/admin/add_offer.php" class="hover:text-blue-600">Добавить оффер</a></li>
                    <li><a href="/admin/reviews.php" class="hover:text-blue-600">Отзывы</a></li>
                    <li><a href="/admin/recommendations.php" class="hover:text-blue-600">Рекомендации</a></li>
                    <li><a href="/admin/creditors.php" class="hover:text-blue-600">Информация о кредиторах</a></li>
                </ul>
            </nav>

            <!-- Offers Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold">Управление офферами</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Позиция</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Название</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Рейтинг</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Сумма</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Одобрение</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Статус</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($offers as $offer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <form method="POST" action="update_order.php" class="inline-flex">
                                        <input type="hidden" name="id" value="<?= $offer['id'] ?>">
                                        <input type="number" name="position" value="<?= $offer['position'] ?>" 
                                               class="w-16 px-2 py-1 border rounded text-sm">
                                        <button type="submit" class="ml-1 text-blue-600 hover:text-blue-800 text-sm">✓</button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 font-medium"><?= e($offer['name']) ?></td>
                                <td class="px-4 py-3"><?= e($offer['rating']) ?></td>
                                <td class="px-4 py-3 text-sm"><?= number_format($offer['min_amount'], 0, '', ' ') ?> - <?= number_format($offer['max_amount'], 0, '', ' ') ?> ₽</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded <?= $offer['approval_level'] == 'высокое' ? 'bg-green-100 text-green-800' : ($offer['approval_level'] == 'среднее' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                        <?= ucfirst(e($offer['approval_level'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded <?= $offer['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                        <?= $offer['is_active'] ? 'Активен' : 'Отключен' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="edit_offer.php?id=<?= $offer['id'] ?>" class="text-blue-600 hover:text-blue-800">Изменить</a>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="toggle_offer">
                                            <input type="hidden" name="id" value="<?= $offer['id'] ?>">
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800">
                                                <?= $offer['is_active'] ? 'Отключить' : 'Включить' ?>
                                            </button>
                                        </form>
                                        <form method="POST" class="inline" onsubmit="return confirm('Удалить оффер?')">
                                            <input type="hidden" name="action" value="delete_offer">
                                            <input type="hidden" name="id" value="<?= $offer['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800">Удалить</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </body>
    </html>
    <?php
    exit;
}

// Форма входа
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['login'] === ADMIN_LOGIN && password_verify($_POST['password'], ADMIN_PASSWORD)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /admin/');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель - <?= e(SITE_NAME) ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Вход в админ-панель</h1>
            
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= e($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Логин</label>
                    <input type="text" name="login" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пароль</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    Войти
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                <a href="/" class="hover:text-blue-600">← Вернуться на сайт</a>
            </div>
        </div>
    </div>
</body>
</html>