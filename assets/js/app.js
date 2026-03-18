document.querySelectorAll('[data-confirm]').forEach((element) => {
  element.addEventListener('click', (event) => {
    const message = element.getAttribute('data-confirm') || 'Are you sure?';
    if (!confirm(message)) {
      event.preventDefault();
    }
  });
});
