// Функциональность для страницы поддержки
document.addEventListener('DOMContentLoaded', function() {
    // Раскрывающиеся FAQ
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            if (question) {
                question.addEventListener('click', () => {
                    // Закрываем все остальные элементы
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    // Переключаем текущий элемент
                    item.classList.toggle('active');
                });
            }
        });
    }

    // Поиск по FAQ
    const searchInput = document.getElementById('support-search');
    const searchButton = searchInput?.nextElementSibling;
    
    if (searchInput && searchButton) {
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const faqQuestions = document.querySelectorAll('.faq-question h3');
            
            faqItems.forEach(item => {
                const questionText = item.querySelector('.faq-question h3')?.textContent.toLowerCase() || '';
                if (searchTerm === '' || questionText.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // Обработка кнопки онлайн-чата
    const chatButton = document.querySelector('.contact-method .btn-primary');
    if (chatButton) {
        chatButton.addEventListener('click', function() {
            alert('Чат поддержки скоро будет доступен. Пожалуйста, напишите нам на support@shinigames.com');
        });
    }
});