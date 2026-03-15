const openButtons = document.querySelectorAll('.open_c_window');

openButtons.forEach((btn) => {
  btn.addEventListener('click', (e) => {
    const form = btn.nextElementSibling;
    if (form) {
      form.classList.toggle('hidden_ac');
    }
  });
});

