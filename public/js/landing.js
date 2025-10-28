// Init Lucide icons
if (window.lucide) {
  window.lucide.createIcons();
}

// Mobile menu toggle
const navToggle = document.getElementById('navToggle');
const navLinks  = document.getElementById('navLinks');

if (navToggle && navLinks) {
  navToggle.addEventListener('click', () => {
    const opened = navLinks.style.display === 'flex';
    navLinks.style.display = opened ? 'none' : 'flex';
    navLinks.style.flexDirection = 'column';
    navLinks.style.position = 'absolute';
    navLinks.style.top = '64px';
    navLinks.style.right = '16px';
    navLinks.style.gap = '12px';
    navLinks.classList.add('glass-effect');
    navLinks.style.padding = '12px';
  });
}

// Smooth scroll for internal anchors
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', (e) => {
    const id = a.getAttribute('href');
    if (!id || id === '#') return;
    const target = document.querySelector(id);
    if (!target) return;
    e.preventDefault();
    window.scrollTo({ top: target.offsetTop - 72, behavior: 'smooth' });
  });
});
