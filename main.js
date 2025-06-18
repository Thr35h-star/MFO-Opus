// Анимация при прокрутке
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer для анимации появления элементов
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Наблюдаем за карточками офферов
    document.querySelectorAll('.offer-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        observer.observe(card);
    });

    // Наблюдаем за секциями
    document.querySelectorAll('section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        observer.observe(section);
    });

    // CSS класс для анимации
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);

    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Валидация формы
    const form = document.querySelector('form[action="thank-you.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Убираем красную рамку при вводе
                    field.addEventListener('input', function() {
                        this.classList.remove('border-red-500');
                    }, { once: true });
                }
            });

            // Проверка телефона
            const phoneField = form.querySelector('input[type="tel"]');
            if (phoneField && phoneField.value) {
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                if (!phoneRegex.test(phoneField.value) || phoneField.value.replace(/\D/g, '').length < 10) {
                    isValid = false;
                    phoneField.classList.add('border-red-500');
                    alert('Пожалуйста, введите корректный номер телефона');
                }
            }

            // Проверка email (если есть)
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    emailField.classList.add('border-red-500');
                    alert('Пожалуйста, введите корректный email');
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    }

    // Форматирование телефона при вводе
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';
            
            if (value.length > 0) {
                if (value[0] === '7' || value[0] === '8') {
                    value = value.substring(1);
                }
                
                if (value.length > 0) {
                    formattedValue = '+7 ';
                    if (value.length > 0) {
                        formattedValue += '(' + value.substring(0, 3);
                    }
                    if (value.length >= 3) {
                        formattedValue += ') ';
                    }
                    if (value.length > 3) {
                        formattedValue += value.substring(3, 6);
                    }
                    if (value.length > 6) {
                        formattedValue += '-' + value.substring(6, 8);
                    }
                    if (value.length > 8) {
                        formattedValue += '-' + value.substring(8, 10);
                    }
                }
            }
            
            e.target.value = formattedValue;
        });
    });

    // Счетчик для статистики (если нужно)
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString('ru-RU');
        }, 16);
    }

    // Пример использования счетчика (раскомментируйте если нужно)
    // const counters = document.querySelectorAll('.counter');
    // counters.forEach(counter => {
    //     const target = parseInt(counter.getAttribute('data-target'));
    //     observer = new IntersectionObserver((entries) => {
    //         entries.forEach(entry => {
    //             if (entry.isIntersecting) {
    //                 animateCounter(entry.target, target);
    //                 observer.unobserve(entry.target);
    //             }
    //         });
    //     });
    //     observer.observe(counter);
    // });
});

// Функция для отправки событий в аналитику (если подключена)
function trackEvent(action, category, label, value) {
    // Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            'event_category': category,
            'event_label': label,
            'value': value
        });
    }
    
    // Яндекс.Метрика
    if (typeof ym !== 'undefined') {
        ym(window.yaCounterId, 'reachGoal', action, {
            category: category,
            label: label,
            value: value
        });
    }
}

// Отслеживание кликов по офферам
document.addEventListener('click', function(e) {
    const offerLink = e.target.closest('a[href*="example.com"]');
    if (offerLink) {
        const offerName = offerLink.closest('.offer-card')?.querySelector('img')?.alt || 'Unknown';
        trackEvent('click', 'Offer', offerName);
    }
});