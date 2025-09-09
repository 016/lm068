/**
 * Login Page Specific JavaScript
 * Extracted from login_1.html for b-login page
 */

// ========== LOGIN PAGE INITIALIZATION ========== 
document.addEventListener('DOMContentLoaded', function() {
    initializeLoginPage();
});

function initializeLoginPage() {
    setupPasswordToggle();
    setupFormValidation();
    setupPageAnimations();
}

// ========== PASSWORD TOGGLE FUNCTIONALITY ========== 
function setupPasswordToggle() {
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordToggleIcon = document.getElementById('passwordToggleIcon');
    
    if (!passwordInput || !passwordToggle || !passwordToggleIcon) return;
    
    passwordToggle.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        passwordToggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}

// ========== FORM VALIDATION ========== 
function setupFormValidation() {
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('blur', window.AdminCommon.ValidationUtils.validateField);
        input.addEventListener('input', window.AdminCommon.ValidationUtils.clearValidation);
    });
}

// ========== PAGE ANIMATIONS ========== 
function setupPageAnimations() {
    // Add subtle entrance animation to login card
    window.addEventListener('load', () => {
        const loginCard = document.querySelector('.login-card');
        if (!loginCard) return;
        
        loginCard.style.opacity = '0';
        loginCard.style.transform = 'translateY(20px)';
        loginCard.style.transition = 'all 0.4s ease';
        
        setTimeout(() => {
            loginCard.style.opacity = '1';
            loginCard.style.transform = 'translateY(0)';
        }, 100);
    });
}

// ========== LOGIN UTILITIES ========== 
function focusFirstInput() {
    const firstInput = document.querySelector('.form-input');
    if (firstInput) {
        firstInput.focus();
    }
}

// Focus first input after page load
window.addEventListener('load', () => {
    setTimeout(focusFirstInput, 500);
});

// ========== KEYBOARD SHORTCUTS ========== 
document.addEventListener('keydown', (e) => {
    // Enter key submits form when not in textarea
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        const loginForm = document.getElementById('loginForm');
        if (loginForm && document.activeElement.form === loginForm) {
            e.preventDefault();
            loginForm.dispatchEvent(new Event('submit'));
        }
    }
});