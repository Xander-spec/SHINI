// Мобильное меню
const mobileMenu = () => {
    const menu = document.querySelector('.nav-menu');
    const toggle = document.querySelector('.nav-toggle');
    
    if(toggle) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    mobileMenu();
    
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const menu = document.querySelector('.nav-menu');
            const toggle = document.querySelector('.nav-toggle');
            if (menu && toggle && menu.classList.contains('active')) {
                menu.classList.remove('active');
                toggle.classList.remove('active');
            }
        });
    });
});