-- Таблица офферов
CREATE TABLE offers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  logo_url VARCHAR(512) NOT NULL,
  icon_url VARCHAR(512) NOT NULL,
  min_amount INT NOT NULL,
  max_amount INT NOT NULL,
  min_term INT NOT NULL,
  max_term INT NOT NULL,
  min_age INT NOT NULL,
  max_age INT NOT NULL,
  approval_level ENUM('низкое', 'среднее', 'высокое') NOT NULL,
  rating DECIMAL(2,1) NOT NULL,
  partner_link VARCHAR(1024) NOT NULL,
  position INT NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица отзывов
CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author_name VARCHAR(255) NOT NULL,
  rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  review_text TEXT NOT NULL,
  review_date DATE NOT NULL,
  position INT NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица информации о кредиторах
CREATE TABLE creditors_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(255) NOT NULL,
  legal_name VARCHAR(512) NOT NULL,
  ogrn VARCHAR(20) NOT NULL,
  inn VARCHAR(12) NOT NULL,
  license_number VARCHAR(100),
  address TEXT NOT NULL,
  phone VARCHAR(50),
  website VARCHAR(255),
  position INT NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица рекомендаций
CREATE TABLE recommendations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  position INT NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вставка тестовых данных для офферов
INSERT INTO offers (name, logo_url, icon_url, min_amount, max_amount, min_term, max_term, min_age, max_age, approval_level, rating, partner_link, position) VALUES
('Займер', '/assets/img/zaimer-logo.png', '/assets/img/zaimer-icon.png', 1500, 30000, 7, 30, 18, 70, 'высокое', 4.8, 'https://example.com/zaimer', 1),
('МаниМен', '/assets/img/moneymen-logo.png', '/assets/img/moneymen-icon.png', 2000, 70000, 5, 168, 21, 65, 'среднее', 4.5, 'https://example.com/moneymen', 2),
('Турбозайм', '/assets/img/turbozaim-logo.png', '/assets/img/turbozaim-icon.png', 3000, 15000, 7, 21, 18, 60, 'высокое', 4.7, 'https://example.com/turbozaim', 3),
('Веббанкир', '/assets/img/webbankir-logo.png', '/assets/img/webbankir-icon.png', 5000, 80000, 7, 365, 25, 70, 'низкое', 4.2, 'https://example.com/webbankir', 4),
('Платиза', '/assets/img/platiza-logo.png', '/assets/img/platiza-icon.png', 1000, 50000, 7, 60, 18, 65, 'среднее', 4.6, 'https://example.com/platiza', 5),
('МигКредит', '/assets/img/migcredit-logo.png', '/assets/img/migcredit-icon.png', 3000, 100000, 5, 365, 21, 70, 'низкое', 4.0, 'https://example.com/migcredit', 6),
('ВиваДеньги', '/assets/img/vivadengi-logo.png', '/assets/img/vivadengi-icon.png', 2000, 20000, 7, 30, 18, 65, 'высокое', 4.9, 'https://example.com/vivadengi', 7);

-- Вставка тестовых отзывов
INSERT INTO reviews (author_name, rating, review_text, review_date, position) VALUES
('Андрей К.', 5, 'Отличный сервис! Деньги получил за 15 минут на карту. Никаких скрытых комиссий, все прозрачно.', '2024-12-15', 1),
('Мария Петрова', 4, 'Хорошая подборка МФО. Подала заявку в три компании, одобрили в двух. Выбрала более выгодные условия.', '2024-12-20', 2),
('Сергей М.', 5, 'Пользуюсь уже не первый раз. Всегда быстро и без проблем. Рекомендую!', '2024-12-25', 3),
('Елена Иванова', 4, 'Удобный сайт, все понятно расписано. Единственный минус - хотелось бы больше информации о процентных ставках.', '2025-01-05', 4),
('Дмитрий В.', 5, 'Спасибо за помощь! Срочно нужны были деньги, через ваш сайт нашел подходящую МФО с моментальным одобрением.', '2025-01-10', 5);

-- Вставка информации о кредиторах
INSERT INTO creditors_info (company_name, legal_name, ogrn, inn, license_number, address, phone, website, position) VALUES
('Займер', 'ООО МКК "ЗАЙМЕР"', '1107746831241', '7704784062', '651303045003951', '115280, г. Москва, ул. Ленинская Слобода, д. 19', '8 (495) 123-45-67', 'zaimer.ru', 1),
('МаниМен', 'ООО МФК "МаниМен"', '1117746442850', '7707747444', '2110177000037', '121087, г. Москва, ул. Барклая, д. 6, стр. 3', '8 (800) 234-56-78', 'moneyman.ru', 2),
('Турбозайм', 'ООО МКК "Турбозайм"', '1141690064316', '1655291358', '651403140005483', '420043, г. Казань, ул. Калинина, д. 48', '8 (843) 567-89-01', 'turbozaim.ru', 3),
('Веббанкир', 'ООО МФК "Веббанкир"', '1107746671306', '7709858345', '2120177001845', '123290, г. Москва, 1-й Магистральный туп., д. 5А', '8 (495) 974-64-43', 'webbankir.com', 4);

-- Вставка рекомендаций
INSERT INTO recommendations (title, content, position) VALUES
('Проверьте свою кредитную историю', 'Перед подачей заявки убедитесь, что ваша кредитная история в порядке. Закройте просроченные задолженности, если они есть. Чистая кредитная история значительно повышает шансы на одобрение.', 1),
('Подавайте заявки в несколько МФО одновременно', 'Не ограничивайтесь одной компанией. Подайте заявки в 3-5 МФО одновременно. Это не повлияет на вашу кредитную историю, но увеличит вероятность получения займа.', 2),
('Указывайте достоверную информацию', 'Всегда указывайте только правдивые данные о себе. МФО проверяют информацию, и обман может привести к отказу не только сейчас, но и в будущем.', 3),
('Начните с небольшой суммы', 'Если вы новый клиент, запрашивайте небольшую сумму на короткий срок. После успешного погашения первого займа вам будут доступны более крупные суммы.', 4),
('Внимательно читайте условия договора', 'Перед подписанием договора внимательно изучите все условия, особенно информацию о процентах, штрафах и комиссиях. Это поможет избежать неприятных сюрпризов.', 5);