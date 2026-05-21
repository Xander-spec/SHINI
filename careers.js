// Фильтрация вакансий
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const vacancyCards = document.querySelectorAll('.vacancy-card');
    
    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const department = this.getAttribute('data-department');
                
                // Обновляем активную кнопку
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Фильтруем вакансии
                vacancyCards.forEach(card => {
                    if (department === 'all' || card.getAttribute('data-department') === department) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Обработка формы отправки заявки
    const applicationForm = document.getElementById('career-application-form');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Спасибо! Ваша заявка отправлена. Мы свяжемся с вами в ближайшее время.');
            this.reset();
        });
    }
    
    // Плавная прокрутка к вакансиям
    const vacanciesLink = document.querySelector('a[href="#vacancies"]');
    if (vacanciesLink) {
        vacanciesLink.addEventListener('click', function(e) {
            e.preventDefault();
            const vacanciesSection = document.getElementById('vacancies');
            if (vacanciesSection) {
                vacanciesSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});