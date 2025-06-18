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
                    $stmt = $db->prepare("INSERT INTO recommendations (title, content, position) VALUES (?, ?, ?)");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['content'],
                        $_POST['position']
                    ]);
                    $success = 'Рекомендация добавлена';
                } catch (Exception $e) {
                    $error = 'Ошибка добавления рекомендации';
                }
                break;
                
            case 'update':
                try {
                    $stmt = $db->prepare("UPDATE recommendations SET title = ?, content = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['content'],
                        $_POST['id']
                    ]);
                    $success = 'Рекомендация обновлена';
                } catch (Exception $e) {
                    $error = 'Ошибка обновления рекомендации';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $db->prepare("DELETE FROM recommendations WHERE id = ?")->execute([$id]);
                $success = 'Рекомендация удалена';
                break;
                
            case 'toggle':
                $id = (int)$_POST['id'];
                $db->prepare("UPDATE recommendations SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
                break;
                
            case 'update_position':
                $id = (int)$_POST['id'];
                $position = (int)$_POST['position'];
                $db->prepare("UPDATE recommendations SET position = ? WHERE id = ?")->execute([$position, $id]);
                break;
        }
    }
}

// Получаем все рекомендации
$recommendations = $db->query("SELECT * FROM recommendations ORDER BY position ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление рекомендациями - Админ-панель</title>
    
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
                <li><a href="/admin/reviews.php" class="hover:text-blue-600">Отзывы</a></li>
                <li><a href="/admin/recommendations.php" class="text-blue-600 font-semibold">Рекомендации</a></li>
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

        <!-- Add Recommendation Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Добавить новую рекомендацию</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Заголовок</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Содержание</label>
                    <textarea name="content" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Позиция</label>
                    <input type="number" name="position" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Добавить рекомендацию
                </button>
            </form>
        </div>

        <!-- Recommendations List -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-semibold">Список рекомендаций</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($recommendations as $rec): ?>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg mb-1"><?= e($rec['title']) ?></h3>
                            <span class="px-2 py-1 text-xs rounded <?= $rec['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $rec['is_active'] ? 'Активна' : 'Отключена' ?>
                            </span>
                        </div>
                        <form method="POST" class="ml-4 flex items-center">
                            <input type="hidden" name="action" value="update_position">
                            <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                            <label class="text-sm text-gray-600 mr-2">Позиция:</label>
                            <input type="number" name="position" value="<?= $rec['position'] ?>" 
                                   class="w-16 px-2 py-1 border rounded text-sm">
                            <button type="submit" class="ml-1 text-blue-600 hover:text-blue-800 text-sm">✓</button>
                        </form>
                    </div>
                    
                    <form method="POST" class="mb-3">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                        
                        <div class="space-y-3">
                            <input type="text" name="title" value="<?= e($rec['title']) ?>" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <textarea name="content" rows="3" required 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"><?= e($rec['content']) ?></textarea>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                Сохранить
                            </button>
                        </div>
                    </form>
                    
                    <div class="flex space-x-3">
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm">
                                <?= $rec['is_active'] ? 'Отключить' : 'Включить' ?>
                            </button>
                        </form>
                        <form method="POST" class="inline" onsubmit="return confirm('Удалить рекомендацию?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Удалить</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>