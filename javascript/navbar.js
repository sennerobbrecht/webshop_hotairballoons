
const hamburgerMenu = document.getElementById('hamburgerMenu');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('closeBtn');


hamburgerMenu.addEventListener('click', () => {
    sidebar.classList.add('show');
});


closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('show');
});

