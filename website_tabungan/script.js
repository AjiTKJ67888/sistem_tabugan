// ============================================
// FILE SCRIPT.JS - LOGIKA JAVASCRIPT
// ============================================

/**
 * Menampilkan alert/notifikasi
 * @param {string} type - Tipe alert (success, error, warning)
 * @param {string} message - Pesan yang ingin ditampilkan
 */
function showAlert(type, message) {
    // Buat elemen alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible`;
    alertDiv.id = 'alert';

    // Icon berdasarkan tipe
    const iconClass = type === 'success' ? 'check-circle' : 'exclamation-circle';

    // Isi HTML alert
    alertDiv.innerHTML = `
        <i class="fas fa-${iconClass}"></i>
        <span>${message}</span>
        <button type="button" onclick="closeAlert()" class="alert-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Cari container untuk alert (di dashboard atau halaman lain)
    const mainContent = document.querySelector('.main-content') || document.querySelector('.auth-card');
    
    if (mainContent) {
        // Insert alert di awal main content
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
    }

    // Auto-remove alert setelah 5 detik
    setTimeout(() => {
        closeAlert();
    }, 5000);
}

/**
 * Menutup alert
 */
function closeAlert() {
    const alert = document.getElementById('alert');
    if (alert) {
        alert.style.animation = 'slideUp 0.3s ease';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

/**
 * Format angka ke format rupiah
 * @param {number} num - Angka yang ingin di-format
 * @returns {string} - Angka dalam format rupiah
 */
function formatRupiah(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
}

/**
 * Parse string rupiah ke number
 * @param {string} rupiahString - String format rupiah
 * @returns {number} - Nilai numerik
 */
function parseRupiah(rupiahString) {
    return parseInt(rupiahString.replace(/\D/g, ''));
}

/**
 * Validasi email sederhana
 * @param {string} email - Email yang ingin divalidasi
 * @returns {boolean} - True jika email valid
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validasi nominal uang
 * @param {number} nominal - Nominal yang ingin divalidasi
 * @returns {boolean} - True jika nominal valid
 */
function validateNominal(nominal) {
    return nominal > 0 && !isNaN(nominal);
}

/**
 * Animate element
 * @param {HTMLElement} element - Element yang ingin di-animate
 * @param {string} animationName - Nama animasi
 */
function animateElement(element, animationName) {
    element.style.animation = `${animationName} 0.3s ease`;
    
    element.addEventListener('animationend', () => {
        element.style.animation = '';
    }, { once: true });
}

/**
 * Copy text ke clipboard
 * @param {string} text - Text yang ingin dicopy
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('success', 'Berhasil dicopy!');
    }).catch(() => {
        showAlert('error', 'Gagal dicopy!');
    });
}

/**
 * Debounce function untuk performance
 * @param {Function} func - Function yang ingin di-debounce
 * @param {number} delay - Delay dalam ms
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

/**
 * Throttle function untuk performance
 * @param {Function} func - Function yang ingin di-throttle
 * @param {number} limit - Limit dalam ms
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Get value dari form input
 * @param {string} fieldName - Nama field
 * @returns {string} - Value dari field
 */
function getFormValue(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    return field ? field.value.trim() : '';
}

/**
 * Set value ke form input
 * @param {string} fieldName - Nama field
 * @param {string} value - Value yang ingin diset
 */
function setFormValue(fieldName, value) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field) {
        field.value = value;
    }
}

/**
 * Clear form
 * @param {string} formId - ID form
 */
function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
    }
}

/**
 * Disable button
 * @param {string} buttonId - ID button
 */
function disableButton(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = true;
        button.style.opacity = '0.6';
    }
}

/**
 * Enable button
 * @param {string} buttonId - ID button
 */
function enableButton(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = false;
        button.style.opacity = '1';
    }
}

/**
 * Tampilkan/sembunyikan loading spinner
 * @param {boolean} show - True untuk tampil, false untuk sembunyikan
 */
function toggleLoader(show) {
    // Buat atau ambil existing loader
    let loader = document.getElementById('loader');
    
    if (!loader && show) {
        loader = document.createElement('div');
        loader.id = 'loader';
        loader.className = 'loader';
        loader.innerHTML = `
            <div class="loader-content">
                <div class="spinner"></div>
                <p>Sedang memproses...</p>
            </div>
        `;
        document.body.appendChild(loader);
    }
    
    if (loader) {
        if (show) {
            loader.style.display = 'flex';
        } else {
            loader.style.display = 'none';
        }
    }
}

/**
 * Get current date dalam format tertentu
 * @returns {string} - Tanggal saat ini
 */
function getCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const date = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${date}`;
}

/**
 * Format date ke format yang rapi
 * @param {string} dateString - String tanggal
 * @returns {string} - Tanggal yang sudah di-format
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

/**
 * Check apakah device adalah mobile
 * @returns {boolean} - True jika mobile
 */
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

/**
 * Add CSS class ke element
 * @param {HTMLElement} element - Element target
 * @param {string} className - Class name
 */
function addClass(element, className) {
    if (element) {
        element.classList.add(className);
    }
}

/**
 * Remove CSS class dari element
 * @param {HTMLElement} element - Element target
 * @param {string} className - Class name
 */
function removeClass(element, className) {
    if (element) {
        element.classList.remove(className);
    }
}

/**
 * Toggle CSS class pada element
 * @param {HTMLElement} element - Element target
 * @param {string} className - Class name
 */
function toggleClass(element, className) {
    if (element) {
        element.classList.toggle(className);
    }
}

// ============================================
// EVENT LISTENERS & INITIALIZATION
// ============================================

// Apply saved theme immediately so the UI does not flash back to light mode
(function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark' && document.body) {
        document.body.classList.add('dark-mode');
    }
})();

/**
 * Initialize semua event listeners saat DOM sudah loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips jika ada
    initializeTooltips();
    
    // Add smooth scroll behavior untuk link
    initializeSmoothScroll();
    
    // Add form validation
    initializeFormValidation();
    
    // Initialize theme
    initializeTheme();
});

/**
 * Initialize theme berdasarkan pengaturan user
 */
function initializeTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.checked = radio.value === savedTheme;
        radio.addEventListener('change', function() {
            localStorage.setItem('theme', this.value);
            applyTheme(this.value);
        });
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'theme') {
            applyTheme(e.newValue || 'light');
        }
    });
}

/**
 * Toggle theme antara light dan dark mode
 */
function toggleTheme() {
    const currentTheme = localStorage.getItem('theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', newTheme);
    applyTheme(newTheme);
}

/**
 * Apply theme ke document
 */
function applyTheme(theme) {
    const button = document.getElementById('themeToggle');
    
    if (theme === 'dark') {
        document.body.classList.add('dark-mode');
        if (button) {
            button.innerHTML = '<i class="fas fa-sun"></i>';
            button.title = 'Switch to Light Mode';
        }
    } else {
        document.body.classList.remove('dark-mode');
        if (button) {
            button.innerHTML = '<i class="fas fa-moon"></i>';
            button.title = 'Switch to Dark Mode';
        }
    }
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(el => {
        el.addEventListener('mouseover', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
        });

        el.addEventListener('mouseout', function() {
            const tooltips = document.querySelectorAll('.tooltip');
            tooltips.forEach(t => t.remove());
        });
    });
}

/**
 * Initialize smooth scroll behavior
 */
function initializeSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    addClass(input, 'error');
                    isValid = false;
                } else {
                    removeClass(input, 'error');
                }

                // Validasi email
                if (input.type === 'email' && input.value.trim()) {
                    if (!validateEmail(input.value.trim())) {
                        addClass(input, 'error');
                        isValid = false;
                    }
                }

                // Validasi number
                if (input.type === 'number' && input.value.trim()) {
                    if (!validateNominal(parseFloat(input.value))) {
                        addClass(input, 'error');
                        isValid = false;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                showAlert('error', 'Mohon isi semua field dengan benar');
            }
        });

        // Remove error class saat input
        form.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    removeClass(this, 'error');
                }
            });
        });
    });
}

// ============================================
// KEYBOARD SHORTCUTS
// ============================================

document.addEventListener('keydown', function(e) {
    // ESC untuk close modal
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.active');
        modals.forEach(modal => {
            modal.classList.remove('active');
        });
    }

    // Ctrl + S untuk save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        // Handle save logic di sini
    }
});

// ============================================
// UTILITY ANIMATIONS
// ============================================

// Slide Up animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .loader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loader-content {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        text-align: center;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    .loader-content p {
        margin: 0;
        color: #666;
    }

    .tooltip {
        position: fixed;
        background: #333;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        z-index: 1000;
        pointer-events: none;
        animation: fadeIn 0.2s ease;
    }
`;
document.head.appendChild(style);

// ============================================
// CONSOLE LOG HELPER
// ============================================

/**
 * Custom log dengan prefix
 * @param {string} message - Pesan
 * @param {string} type - Tipe (log, warn, error)
 */
function customLog(message, type = 'log') {
    const timestamp = new Date().toLocaleTimeString();
    const prefix = `[${timestamp}] SaveHub:`;
    
    switch(type) {
        case 'warn':
            console.warn(`%c${prefix}`, 'color: orange;', message);
            break;
        case 'error':
            console.error(`%c${prefix}`, 'color: red;', message);
            break;
        default:
            console.log(`%c${prefix}`, 'color: blue;', message);
    }
}

// ============================================
// EXPORT FUNCTIONS (untuk digunakan di file lain yang meng-include ini)
// ============================================

// Jika menggunakan module system, uncomment line berikut:
// export { showAlert, closeAlert, formatRupiah, validateEmail, ... }
