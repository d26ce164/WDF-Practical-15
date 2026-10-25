/**
 * StudentHub Frontend UI JavaScript (Practical 4 & Interactivity)
 * - Light/Dark Theme Switcher with localStorage persistence
 * - Mobile Hamburger Navigation Toggle
 * - FAQ Accordion Dropdowns
 * - Dynamic Modal Dialog Handlers
 * - Toast Notification Helper
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Theme Management (Light / Dark)
  const currentTheme = localStorage.getItem('studenthub_theme') || 'light';
  document.documentElement.setAttribute('data-theme', currentTheme);

  const themeToggle = document.getElementById('theme-toggle');
  if (themeToggle) {
    updateThemeButtonText(themeToggle, currentTheme);
    themeToggle.addEventListener('click', () => {
      const active = document.documentElement.getAttribute('data-theme');
      const target = active === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', target);
      localStorage.setItem('studenthub_theme', target);
      updateThemeButtonText(themeToggle, target);
    });
  }

  function updateThemeButtonText(btn, theme) {
    btn.innerHTML = theme === 'dark' ? '☀️ Light' : '🌙 Dark';
  }

  // 2. Mobile Hamburger Menu
  const hamburger = document.getElementById('nav-toggle');
  const navMenu = document.getElementById('nav-menu');
  if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
      navMenu.classList.toggle('show');
    });
  }

  // 3. Collapsible Accordions (for FAQ page)
  const accordions = document.querySelectorAll('.accordion-header');
  accordions.forEach(header => {
    header.addEventListener('click', () => {
      const parent = header.parentElement;
      parent.classList.toggle('active');
      const body = parent.querySelector('.accordion-body');
      if (body) {
        body.style.display = parent.classList.contains('active') ? 'block' : 'none';
      }
    });
  });

  // 4. Modal Logic
  window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('active');
  };

  window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
  };

  // Close modals on backdrop click
  document.querySelectorAll('.modal-backdrop').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.remove('active');
    });
  });
});

/**
 * Toast Notification Helper
 */
function showToast(message, type = 'info') {
  let toastContainer = document.getElementById('toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.style.position = 'fixed';
    toastContainer.style.bottom = '20px';
    toastContainer.style.right = '20px';
    toastContainer.style.zIndex = '9999';
    toastContainer.style.display = 'flex';
    toastContainer.style.flexDirection = 'column';
    toastContainer.style.gap = '10px';
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement('div');
  toast.className = `alert alert-${type}`;
  toast.style.margin = '0';
  toast.style.minWidth = '240px';
  toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
  toast.innerHTML = `<span>${message}</span>`;
  toastContainer.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 4000);
}
