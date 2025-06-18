<?php
require_once '../config.php';
checkAdminAuth();

$db = getDB();
$error = '';
$success = '';

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $stmt = $db->prepare("INSERT INTO reviews (author_name, rating, review_text, review_date, position) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['author_name'],
                        $_POST['rating'],
                        $_POST['review_text'],
                        $_POST['review_date'],
                        $_POST['position']
                    ]);
                    $success = 'Отзыв добавлен';
                } catch (Exception $e) {
                    $error = 'Ошибка добавления отзыва';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $db->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
                $success = 'Отзыв удален';
                break;
                
            case 'toggle':
                $id = (int)$_POST['id'];
                $db->prepare("UPDATE reviews SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
                break;
                
            case 'update_position':
                $id = (int)$_POST['id'];
                $position = (int)$_POST['position'];
                $db->prepare("UPDATE reviews SET position = ? WHERE id = ?")->execute([$position, $id]);
                break;
        }
    }
}

// Получаем все отзывы
$reviews = $db->query("SELECT * FROM reviews ORDER BY position ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление отзывами - Админ-панель</title>
    
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
                <li><a href="/admin/" class="hover:text-blue-600">Офферы</a></li>
                <li><a href="/admin/add_offer.php" class="hover:text-blue-600">Добавить оффер</a></li>
                <li><a href="/admin/reviews.php" class="text-blue-600 font-semibold">Отзывы</a></li>
                <li><a href="/admin/recommendations.php" class="hover:text-blue-600">Рекомендации</a></li>
                <li><a href="/admin/creditors.php" class="hover:text-blue-600">Информация о кредиторах</a></li>
            </ul>
        </nav>

        <!-- Messages -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= e($success) ?>
        </div>
        <?php endif; ?>

        <!-- Add Review Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Добавить новый отзыв</h2>
            <form method="POST" class="grid gap-4 md:grid-cols-2">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Имя автора</label>
                    <input type="text" name="author_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Рейтинг</label>
                    <select name="rating" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="5">5 звезд</option>
                        <option value="4">4 звезды</option>
                        <option value="3">3 звезды</option>
                        <option value="2">2 звезды</option>
                        <option value="1">1 звезда</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата отзыва</label>
                    <input type="date" name="review_date" value="<?= date('Y-m-d') ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Позиция</label>
                    <input type="number" name="position" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Текст отзыва</label>
                    <textarea name="review_text" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Добавить отзыв
                    </button>
                </div>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-semibold">Список отзывов</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Позиция</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Автор</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Рейтинг</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Текст</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Дата</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Статус</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($reviews as $review): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <form method="POST" class="inline-flex">
                                    <input type="hidden" name="action" value="update_position">
                                    <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                    <input type="number" name="position" value="<?= $review['position'] ?>" 
                                           class="w-16 px-2 py-1 border rounded text-sm">
                                    <button type="submit" class="ml-1 text-blue-600 hover:text-blue-800 text-sm">✓</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 font-medium"><?= e($review['author_name']) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-yellow-500">
                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>★<?php endfor; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm max-w-xs truncate"><?= e($review['review_text']) ?></td>
                            <td class="px-4 py-3 text-sm"><?= date('d.m.Y', strtotime($review['review_date'])) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded <?= $review['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= $review['is_active'] ? 'Активен' : 'Отключен' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm">
                                            <?= $review['is_active'] ? 'Отключить' : 'Включить' ?>
                                        </button>
                                    </form>
                                    <form method="POST" class="inline" onsubmit="return confirm('Удалить отзыв?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Удалить</button>
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