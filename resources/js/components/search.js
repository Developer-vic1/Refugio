document.querySelectorAll('[data-search-control]').forEach(control => {
  const input = control.querySelector('input');
  const clear = control.querySelector('[data-search-clear]');
  const update = () => { clear.hidden = !input.value; };
  input.addEventListener('input', update);
  clear.addEventListener('click', () => {
    input.value = '';
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.focus();
  });
  input.form?.addEventListener('reset', () => requestAnimationFrame(update));
  update();
});
