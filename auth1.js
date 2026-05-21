// Система аутентификации через PHP
class AuthSystem {
    constructor() {
        this.currentUser = null;
        this.init();
    }

    init() {
        this.checkSession();
        this.setupEventListeners();
    }

    async checkSession() {
        try {
            const response = await fetch('php/check_session.php');
            const data = await response.json();
            
            if (data.isAuthenticated) {
                this.currentUser = data.user;
                console.log('User loaded from session:', this.currentUser);
            } else {
                this.currentUser = null;
            }
            this.updateNavigation();
        } catch (error) {
            console.error('Error checking session:', error);
            this.currentUser = null;
            this.updateNavigation();
        }
    }

    async register(userData) {
        try {
            const formData = new FormData();
            formData.append('username', userData.username);
            formData.append('email', userData.email);
            formData.append('password', userData.password);
            formData.append('confirm_password', userData.confirm_password);

            const response = await fetch('php/register.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.currentUser = result.user;
                this.updateNavigation();
                return result;
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            console.error('Registration error:', error);
            throw error;
        }
    }

    async login(email, password) {
        try {
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            const response = await fetch('php/login.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.currentUser = result.user;
                this.updateNavigation();
                return result;
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    }

    logout() {
        window.location.href = 'php/logout.php';
    }

    updateNavigation() {
        const guestNav = document.querySelector('.nav-auth:not(.auth-user)');
        const userNav = document.querySelector('.nav-auth.auth-user');
        const userNameElement = document.getElementById('user-name');
        
        if (this.currentUser && userNav && guestNav) {
            guestNav.style.display = 'none';
            userNav.style.display = 'flex';
            if (userNameElement) {
                userNameElement.textContent = this.currentUser.username;
            }
        } else if (guestNav && userNav) {
            guestNav.style.display = 'flex';
            userNav.style.display = 'none';
        }
    }

    setupEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.id === 'logout-btn') {
                e.preventDefault();
                this.logout();
            }
        });
        this.protectRoutes();
    }

    protectRoutes() {
        const currentPage = window.location.pathname.split('/').pop();
        
        if (currentPage === 'profile.html' && !this.currentUser) {
            window.location.href = 'login.html';
            return;
        }
        if ((currentPage === 'login.html' || currentPage === 'register.html') && this.currentUser) {
            window.location.href = 'profile.html';
            return;
        }
    }

    getCurrentUser() {
        return this.currentUser;
    }

    isAuthenticated() {
        return this.currentUser !== null;
    }

    async loadProfileData() {
        try {
            const response = await fetch('php/get_user_data.php');
            const data = await response.json();
            if (data.success) {
                this.currentUser = data.user;
                return data.user;
            }
            return null;
        } catch (error) {
            console.error('Error loading profile data:', error);
            return null;
        }
    }
}

const auth = new AuthSystem();

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        element.style.display = 'block';
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
    });
}

function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    successDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 1rem 2rem;
        border-radius: 4px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const userData = {
                username: formData.get('username').trim(),
                email: formData.get('email').trim(),
                password: formData.get('password'),
                confirm_password: formData.get('confirm-password')
            };
            
            clearErrors();
            let hasErrors = false;
            
            if (userData.username.length < 3) {
                showError('username-error', 'Имя пользователя должно содержать минимум 3 символа');
                hasErrors = true;
            }
            if (!isValidEmail(userData.email)) {
                showError('email-error', 'Введите корректный email');
                hasErrors = true;
            }
            if (userData.password.length < 6) {
                showError('password-error', 'Пароль должен содержать минимум 6 символов');
                hasErrors = true;
            }
            if (userData.password !== userData.confirm_password) {
                showError('confirm-password-error', 'Пароли не совпадают');
                hasErrors = true;
            }
            
            if (!hasErrors) {
                try {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Регистрация...';
                    await auth.register(userData);
                    showSuccess('Регистрация успешна! Перенаправляем...');
                    setTimeout(() => window.location.href = 'profile.html', 1000);
                } catch (error) {
                    showError('email-error', error.message);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Создать аккаунт';
                }
            }
        });
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;
            clearErrors();
            
            if (!isValidEmail(email)) {
                showError('login-email-error', 'Введите корректный email');
                return;
            }
            if (!password) {
                showError('login-password-error', 'Введите пароль');
                return;
            }
            
            try {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Вход...';
                await auth.login(email, password);
                showSuccess('Вход выполнен! Перенаправляем...');
                setTimeout(() => window.location.href = 'profile.html', 1000);
            } catch (error) {
                showError('login-password-error', error.message);
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Войти';
            }
        });
    }

    if (window.location.pathname.includes('profile.html')) {
        initializeProfileData();
    }
});

async function initializeProfileData() {
    if (!auth.currentUser) {
        await auth.checkSession();
    }
    
    const user = auth.currentUser;
    if (!user) {
        window.location.href = 'login.html';
        return;
    }
    
    const avatarElement = document.getElementById('user-avatar');
    if (avatarElement) avatarElement.textContent = user.avatar || user.username.charAt(0).toUpperCase();
    
    const displayNameElement = document.getElementById('display-username');
    if (displayNameElement) displayNameElement.textContent = user.username;
    
    const emailElement = document.getElementById('display-email');
    if (emailElement) emailElement.textContent = user.email;
    
    const memberSinceElement = document.getElementById('member-since');
    if (memberSinceElement) memberSinceElement.textContent = user.created_at || '2024';
}