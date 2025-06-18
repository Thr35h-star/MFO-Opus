<?php
require_once '../config.php';
checkAdminAuth();

$db = getDB();
$error = '';
$success = '';

// Получаем ID оффера
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем данные оффера
$stmt = $db->prepare("SELECT * FROM offers WHERE id = ?");
$stmt->execute([$id]);
$offer = $stmt->fetch();

if (!$offer) {
    header('Location: /admin/');
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация данных
        $name = trim($_POST['name']);
        $logo_url = trim($_POST['logo_url']);
        $icon_url = trim($_POST['icon_url']);
        $min_amount = (int)$_POST['min_amount'];
        $max_amount = (int)$_POST['max_amount'];
        $min_term = (int)$_POST['min_term'];
        $max_term = (int)$_POST['max_term'];
        $min_age = (int)$_POST['min_age'];
        $max_age = (int)$_POST['max_age'];
        $approval_level = $_POST['approval_level'];
        $rating = (float)$_POST['rating'];
        $partner_link = trim($_POST['partner_link']);
        $position = (int)$_POST['position'];
        
        if (empty($name) || empty($partner_link)) {
            throw new Exception('Название и партнерская ссылка обязательны');
        }
        
        if ($min_amount >= $max_amount || $min_term >= $max_term || $min_age >= $max_age) {
            throw new Exception('Минимальные значения должны быть меньше максимальных');
        }
        
        if ($rating < 1 || $rating > 5) {
            throw new Exception('Рейтинг должен быть от 1 до 5');
        }
        
        // Обновление в БД
        $stmt = $db->prepare("
            UPDATE offers SET 
                name = ?, logo_url = ?, icon_url = ?, min_amount = ?, max_amount = ?, 
                min_term = ?, max_term = ?, min_age = ?, max_age = ?, approval_level = ?, 
                rating = ?, partner_link = ?, position = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $name, $logo_url, $icon_url, $min_amount, $max_amount, $min_term, $max_term,
            $min_age, $max_age, $approval_level, $rating, $partner_link, $position, $id
        ]);
        
        $success = 'Оффер успешно обновлен!';
        
        // Обновляем данные для отображения
        $offer = array_merge($offer, $_POST);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать оффер - Админ-панель</title>
    
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
                <li><a href="/admin/recommendations.php" class="hover:text-blue-600">Рекомендации</a></li>
                <li><a href="/admin/creditors.php" class="hover:text-blue-600">Информация о кредиторах</a></li>
            </ul>
        </nav>

        <!-- Edit Offer Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Редактировать оффер: <?= e($offer['name']) ?></h2>
            
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
            
            <form method="POST" class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Название *</label>
                        <input type="text" name="name" value="<?= e($offer['name']) ?>" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Партнерская ссылка *</label>
                        <input type="url" name="partner_link" value="<?= e($offer['partner_link']) ?>" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL логотипа</label>
                        <input type="text" name="logo_url" value="<?= e($offer['logo_url']) ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL иконки</label>
                        <input type="text" name="icon_url" value="<?= e($offer['icon_url']) ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Минимальная сумма *</label>
                        <input type="number" name="min_amount" value="<?= e($offer['min_amount']) ?>" required min="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Максимальная сумма *</label>
                        <input type="number" name="max_amount" value="<?= e($offer['max_amount']) ?>" required min="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Минимальный срок (дней) *</label>
                        <input type="number" name="min_term" value="<?= e($offer['min_term']) ?>" required min="1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Максимальный срок (дней) *</label>
                        <input type="number" name="max_term" value="<?= e($offer['max_term']) ?>" required min="1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Минимальный возраст *</label>
                        <input type="number" name="min_age" value="<?= e($offer['min_age']) ?>" required min="18" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Максимальный возраст *</label>
                        <input type="number" name="max_age" value="<?= e($offer['max_age']) ?>" required min="18" max="100" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Уровень одобрения *</label>
                        <select name="approval_level" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="низкое" <?= ($offer['approval_level'] == 'низкое') ? 'selected' : '' ?>>Низкое</option>
                            <option value="среднее" <?= ($offer['approval_level'] == 'среднее') ? 'selected' : '' ?>>Среднее</option>
                            <option value="высокое" <?= ($offer['approval_level'] == 'высокое') ? 'selected' : '' ?>>Высокое</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Рейтинг (1-5) *</label>
                        <input type="number" name="rating" value="<?= e($offer['rating']) ?>" required min="1" max="5" step="0.1" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Позиция</label>
                        <input type="number" name="position" value="<?= e($offer['position']) ?>" min="0" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="/admin/" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Отмена</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>