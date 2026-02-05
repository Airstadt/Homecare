document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('.menu-toggle');
  const heroMenu = document.querySelector('.hero-menu');

  if (menuToggle && heroMenu) {
    menuToggle.addEventListener('click', () => {
      heroMenu.classList.toggle('active');
      menuToggle.classList.toggle('open');
    });
  }
});

