// Selecteer elementen
const hamburgerMenu = document.getElementById('hamburgerMenu');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('closeBtn');

// Open sidebar bij klikken op hamburger-menu
hamburgerMenu.addEventListener('click', () => {
    sidebar.classList.add('show');
});

// Sluit sidebar bij klikken op de sluit-knop
closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('show');
});

