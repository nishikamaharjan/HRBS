/**
 * Hotel Reservation System - Customer Frontend
 * Main JavaScript File
 */

// ========================================
// Utility Functions
// ========================================

/**
 * Format date to YYYY-MM-DD
 */
function formatDate(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Get today's date in YYYY-MM-DD format
 */
function getTodayDate() {
    return formatDate(new Date());
}

/**
 * Get tomorrow's date in YYYY-MM-DD format
 */
function getTomorrowDate() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return formatDate(tomorrow);
}

/**
 * Calculate number of nights between two dates
 */
function calculateNights(checkin, checkout) {
    const checkinDate = new Date(checkin);
    const checkoutDate = new Date(checkout);
    const diffTime = Math.abs(checkoutDate - checkinDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

/**
 * Format price with currency
 */
function formatPrice(price) {
    return `Rs.${parseFloat(price).toLocaleString('en-NP')}`;
}

// ========================================
// Mobile Navigation Toggle
// ========================================

function initMobileNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            
            // Animate hamburger icon
            const spans = navToggle.querySelectorAll('span');
            if (navMenu.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translateY(10px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translateY(-10px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                const spans = navToggle.querySelectorAll('span');
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
    }
}

// ========================================
// Modal Functions
// ========================================

/**
 * Open modal
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

/**
 * Initialize modal close buttons
 */
function initModals() {
    // Close button clicks
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });
    
    // Click outside modal to close
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });
}

// ========================================
// Form Validation
// ========================================

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number (basic)
 */
function isValidPhone(phone) {
    const phoneRegex = /^[0-9]{10}$/;
    return phoneRegex.test(phone.replace(/[\s-]/g, ''));
}

/**
 * Show form error
 */
function showError(inputId, message) {
    const input = document.getElementById(inputId);
    const errorElement = input.nextElementSibling;
    
    if (input) {
        input.classList.add('error');
    }
    
    if (errorElement && errorElement.classList.contains('form-error')) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

/**
 * Clear form error
 */
function clearError(inputId) {
    const input = document.getElementById(inputId);
    const errorElement = input.nextElementSibling;
    
    if (input) {
        input.classList.remove('error');
    }
    
    if (errorElement && errorElement.classList.contains('form-error')) {
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }
}

/**
 * Validate date range
 */
function validateDateRange(checkinId, checkoutId) {
    const checkin = document.getElementById(checkinId).value;
    const checkout = document.getElementById(checkoutId).value;
    const today = getTodayDate();
    
    if (!checkin) {
        showError(checkinId, 'Check-in date is required');
        return false;
    }
    
    if (!checkout) {
        showError(checkoutId, 'Check-out date is required');
        return false;
    }
    
    if (checkin < today) {
        showError(checkinId, 'Check-in date cannot be in the past');
        return false;
    }
    
    if (checkout <= checkin) {
        showError(checkoutId, 'Check-out must be after check-in');
        return false;
    }
    
    clearError(checkinId);
    clearError(checkoutId);
    return true;
}

// ========================================
// LocalStorage Functions
// ========================================

/**
 * Save form data to localStorage
 */
function saveFormData(formId, data) {
    try {
        localStorage.setItem(`hrs_${formId}`, JSON.stringify(data));
    } catch (e) {
        console.error('Error saving to localStorage:', e);
    }
}

/**
 * Load form data from localStorage
 */
function loadFormData(formId) {
    try {
        const data = localStorage.getItem(`hrs_${formId}`);
        return data ? JSON.parse(data) : null;
    } catch (e) {
        console.error('Error loading from localStorage:', e);
        return null;
    }
}

/**
 * Clear form data from localStorage
 */
function clearFormData(formId) {
    try {
        localStorage.removeItem(`hrs_${formId}`);
    } catch (e) {
        console.error('Error clearing localStorage:', e);
    }
}

// ========================================
// API Functions
// ========================================

/**
 * Fetch available rooms
 */
async function fetchRooms(filters = {}) {
    try {
        const params = new URLSearchParams();
        
        if (filters.checkin) params.append('checkin', filters.checkin);
        if (filters.checkout) params.append('checkout', filters.checkout);
        if (filters.guests) params.append('guests', filters.guests);
        if (filters.room_type) params.append('room_type', filters.room_type);
        if (filters.min_price) params.append('min_price', filters.min_price);
        if (filters.max_price) params.append('max_price', filters.max_price);
        
        const response = await fetch(`../api/rooms.php?${params.toString()}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch rooms');
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching rooms:', error);
        throw error;
    }
}

/**
 * Submit booking
 */
async function submitBooking(bookingData) {
    try {
        const response = await fetch('../api/book.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(bookingData)
        });
        
        if (!response.ok) {
            throw new Error('Failed to submit booking');
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error submitting booking:', error);
        throw error;
    }
}

// ========================================
// Image Gallery/Carousel
// ========================================

/**
 * Initialize image gallery
 */
function initImageGallery() {
    const mainImage = document.querySelector('.main-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                mainImage.src = thumbnail.src;
                mainImage.alt = thumbnail.alt;
                
                // Add active class to clicked thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
            });
        });
    }
}

// ========================================
// Price Calculator
// ========================================

/**
 * Calculate and update total price
 */
function updateTotalPrice(pricePerNight, checkinId, checkoutId, totalElementId) {
    const checkin = document.getElementById(checkinId).value;
    const checkout = document.getElementById(checkoutId).value;
    const totalElement = document.getElementById(totalElementId);
    
    if (checkin && checkout && checkin < checkout) {
        const nights = calculateNights(checkin, checkout);
        const total = pricePerNight * nights;
        
        if (totalElement) {
            totalElement.textContent = `${formatPrice(total)} (${nights} night${nights > 1 ? 's' : ''})`;
        }
        
        return total;
    }
    
    return 0;
}

// ========================================
// Smooth Scroll
// ========================================

/**
 * Initialize smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// ========================================
// Loading Spinner
// ========================================

/**
 * Show loading spinner
 */
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="loading-spinner"></div>';
        element.style.opacity = '0.6';
    }
}

/**
 * Hide loading spinner
 */
function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.opacity = '1';
    }
}

// ========================================
// Toast Notifications
// ========================================

/**
 * Show toast notification
 */
function showToast(message, type = 'info', duration = 3000) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 3000;
        animation: slideDown 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Remove after duration
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, duration);
}

// ========================================
// Initialize on DOM Load
// ========================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize components
    initMobileNav();
    initModals();
    initImageGallery();
    initSmoothScroll();
    
    // Set minimum dates for date inputs
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (input.id.includes('checkin') || input.id.includes('check-in')) {
            input.min = getTodayDate();
        }
        if (input.id.includes('checkout') || input.id.includes('check-out')) {
            input.min = getTomorrowDate();
        }
    });
    
    // Auto-update checkout min date when checkin changes
    document.querySelectorAll('input[type="date"]').forEach(input => {
        if (input.id.includes('checkin') || input.id.includes('check-in')) {
            input.addEventListener('change', (e) => {
                const checkinDate = new Date(e.target.value);
                checkinDate.setDate(checkinDate.setDate() + 1);
                
                // Find corresponding checkout input
                const checkoutId = input.id.replace('checkin', 'checkout').replace('check-in', 'check-out');
                const checkoutInput = document.getElementById(checkoutId);
                
                if (checkoutInput) {
                    checkoutInput.min = formatDate(checkinDate);
                }
            });
        }
    });
    
    console.log('HRS Customer Frontend initialized');
});

// Export functions for use in other scripts
window.HRS = {
    formatDate,
    getTodayDate,
    getTomorrowDate,
    calculateNights,
    formatPrice,
    openModal,
    closeModal,
    showError,
    clearError,
    validateDateRange,
    isValidEmail,
    isValidPhone,
    saveFormData,
    loadFormData,
    clearFormData,
    fetchRooms,
    submitBooking,
    updateTotalPrice,
    showLoading,
    hideLoading,
    showToast
};
