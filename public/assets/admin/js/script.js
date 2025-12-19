// Inject Sidebar HTML
const sidebarContent = document.getElementById('sidebarContent').innerHTML;
document.querySelector('.sidebar-wrapper').innerHTML = sidebarContent;
document.querySelector('.offcanvas-body').innerHTML = sidebarContent;

// 
const sidebarToggle = document.getElementById("sidebarToggle");
sidebarToggle?.addEventListener("click", () => {
    document.body.classList.toggle("sidebar-collapsed");
});

// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;
const sunIcon = document.getElementById('sunIcon');
const moonIcon = document.getElementById('moonIcon');

// Load Saved Theme
const savedTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-bs-theme', savedTheme);
updateIcons(savedTheme);

themeToggle.addEventListener('click', () => {
    const current = html.getAttribute('data-bs-theme');
    const next = current === 'dark' ? 'light' : 'dark';

    html.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
    updateIcons(next);
    updateChartTheme(next);
});

function updateIcons(theme) {
    if (theme === 'dark') {
        sunIcon.classList.add('d-none');
        moonIcon.classList.remove('d-none');
    } else {
        sunIcon.classList.remove('d-none');
        moonIcon.classList.add('d-none');
    }
}