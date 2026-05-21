// Управление вкладками в профиле
const initProfileTabs = () => {
    const tabButtons = document.querySelectorAll('.profile-nav-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    if (tabButtons.length === 0) return;
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab') + '-tab';
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            button.classList.add('active');
            const targetTab = document.getElementById(tabId);
            if (targetTab) targetTab.classList.add('active');
        });
    });
};

// Загрузка данных пользователя из сессии
const loadUserProfile = async () => {
    try {
        const response = await fetch('php/get_user_data.php');
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            
            // Обновляем аватар
            const avatarElement = document.getElementById('user-avatar');
            if (avatarElement) avatarElement.textContent = user.avatar || user.username.charAt(0).toUpperCase();
            
            // Обновляем имя пользователя
            const displayNameElement = document.getElementById('display-username');
            if (displayNameElement) displayNameElement.textContent = user.username;
            
            const userNameElement = document.getElementById('user-name');
            if (userNameElement) userNameElement.textContent = user.username;
            
            // Обновляем email
            const emailElement = document.getElementById('display-email');
            if (emailElement) emailElement.textContent = user.email;
            
            // Обновляем дату регистрации
            const memberSinceElement = document.getElementById('member-since');
            if (memberSinceElement) memberSinceElement.textContent = user.created_at || '2024';
            
            // Обновляем статистику
            const playtimeElement = document.getElementById('profile-playtime');
            if (playtimeElement) playtimeElement.textContent = user.playtime ? `${user.playtime} ч` : '24 ч';
            
            const levelElement = document.getElementById('profile-level');
            if (levelElement) levelElement.textContent = user.level || '15';
            
            // Обновляем настройки
            const settingsUsername = document.getElementById('settings-username');
            if (settingsUsername) settingsUsername.textContent = user.username;
            
            const settingsEmail = document.getElementById('settings-email');
            if (settingsEmail) settingsEmail.textContent = user.email;
            
            const settingsCreated = document.getElementById('settings-created');
            if (settingsCreated) settingsCreated.textContent = user.created_at || '2024';
        } else {
            // Если пользователь не авторизован, перенаправляем на страницу входа
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Ошибка загрузки профиля:', error);
        window.location.href = 'login.html';
    }
};

// Обработка выхода
const setupLogoutButton = () => {
    const logoutBtn = document.getElementById('profile-logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            window.location.href = 'php/logout.php';
        });
    }
};

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    initProfileTabs();
    loadUserProfile();
    setupLogoutButton();
});