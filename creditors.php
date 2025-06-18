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
                    $stmt = $db->prepare("
                        INSERT INTO creditors_info 
                        (company_name, legal_name, ogrn, inn, license_number, address, phone, website, position) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['company_name'],
                        $_POST['legal_name'],
                        $_POST['ogrn'],
                        $_POST['inn'],
                        $_POST['license_number'],
                        $_POST['address'],
                        $_POST['phone'],
                        $_POST['website'],
                        $_POST['position']
                    ]);
                    $success = 'Информация о кредиторе добавлена';
                } catch (Exception $e) {
                    $error = 'Ошибка добавления информации';
                }
                break;
                
            case 'update':
                try {
                    $stmt = $db->prepare("
                        UPDATE creditors_info SET 
                        company_name = ?, legal_name = ?, ogrn = ?, inn = ?, 
                        license_number = ?, address = ?, phone = ?, website = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['company_name'],
                        $_POST['legal_name'],
                        $_POST['ogrn'],
                        $_POST['inn'],
                        $_POST['license_number'],
                        $_POST['address'],
                        $_POST['phone'],
                        $_POST['website'],
                        $_POST['id']
                    ]);
                    $success = 'Информация обновлена';
                } catch (Exception $e) {
                    $error = 'Ошибка обновления информации';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $db->prepare("DELETE FROM creditors_info WHERE id = ?")->execute([$id]);
                $success = 'Информация удалена';
                break;
                
            case 'toggle':
                $id = (int)$_POST['id'];
                $db->prepare("UPDATE creditors_info SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
                break;
                
            case 'update_position':
                $id = (int)$_POST['id'];
                $position = (int)$_POST['position'];
                $db->prepare("UPDATE creditors_info SET position = ? WHERE id = ?")->execute([$position, $id]);
                break;
        }
    }
}

// Получаем всех кредиторов
$creditors = $db->query("SELECT * FROM creditors_info ORDER BY position ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о кредиторах - Админ-панель</title>
    
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
                <li><a href="/admin/creditors.php" class="text-blue-600 font-semibold">Информация о кредиторах</a></li>
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

        <!-- Add Creditor Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Добавить информацию о кредиторе</h2>
            <form method="POST" class="grid gap-4 md:grid-cols-2">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Название компании</label>
                    <input type="text" name="company_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Юридическое лицо</label>
                    <input type="text" name="legal_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ОГРН</label>
                    <input type="text" name="ogrn" required pattern="[0-9]{13,15}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ИНН</label>
                    <input type="text" name="inn" required pattern="[0-9]{10,12}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Номер лицензии</label>
                    <input type="text" name="license_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Сайт</label>
                    <input type="text" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Позиция</label>
                    <input type="number" name="position" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Адрес</label>
                    <textarea name="address" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Добавить информацию
                    </button>
                </div>
            </form>
        </div>

        <!-- Creditors List -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-semibold">Список кредиторов</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($creditors as $creditor): ?>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-lg"><?= e($creditor['company_name']) ?></h3>
                            <span class="px-2 py-1 text-xs rounded <?= $creditor['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $creditor['is_active'] ? 'Активен' : 'Отключен' ?>
                            </span>
                        </div>
                        <form method="POST" class="flex items-center">
                            <input type="hidden" name="action" value="update_position">
                            <input type="hidden" name="id" value="<?= $creditor['id'] ?>">
                            <label class="text-sm text-gray-600 mr-2">Позиция:</label>
                            <input type="number" name="position" value="<?= $creditor['position'] ?>" 
                                   class="w-16 px-2 py-1 border rounded text-sm">
                            <button type="submit" class="ml-1 text-blue-600 hover:text-blue-800 text-sm">✓</button>
                        </form>
                    </div>
                    
                    <form method="POST" class="mb-3">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $creditor['id'] ?>">
                        
                        <div class="grid gap-3 md:grid-cols-2 mb-3">
                            <input type="text" name="company_name" value="<?= e($creditor['company_name']) ?>" required 
                                   placeholder="Название компании" class="px-3 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="legal_name" value="<?= e($creditor['legal_name']) ?>" required 
                                   placeholder="Юридическое лицо" class="px-3 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="ogrn" value="<?= e($creditor['ogrn']) ?>" required 
                                   placeholder="ОГРН" class="px-3 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="inn" value="<?= e($creditor['inn']) ?>" required 
                                   placeholder="ИНН" class="px-3 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="license_number" value="<?= e($creditor['license_number']) ?>" 
                                   placeholder="Номер лицензии" class="px-3 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="phone" value="<?= e($creditor['phone']) ?>" 
                                   placeholder="Телефон" class="px-3 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="website" value="<?= e($creditor['website']) ?>" 
                                   placeholder="Сайт" class="px-3 py-2 border border-gray-300 rounded-lg md:col-span-2">
                            <textarea name="address" rows="2" required placeholder="Адрес" 
                                      class="px-3 py-2 border border-gray-300 rounded-lg md:col-span-2"><?= e($creditor['address']) ?></textarea>
                        </div>
                        
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                            Сохранить изменения
                        </button>
                    </form>
                    
                    <div class="flex space-x-3">
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $creditor['id'] ?>">
                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm">
                                <?= $creditor['is_active'] ? 'Отключить' : 'Включить' ?>
                            </button>
                        </form>
                        <form method="POST" class="inline" onsubmit="return confirm('Удалить информацию о кредиторе?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $creditor['id'] ?>">
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