const bindings = [
  { button: document.querySelector('[data-menu]'), panel: document.getElementById('public-nav'), openLabel: 'Abrir menú', closeLabel: 'Cerrar menú' },
  { button: document.querySelector('[data-admin-menu]'), panel: document.getElementById('admin-sidebar'), openLabel: 'Abrir panel lateral', closeLabel: 'Cerrar panel lateral' },
];

for (const { button, panel, openLabel, closeLabel } of bindings) {
  if (!button || !panel) continue;
  const close = (restoreFocus = false) => {
    panel.classList.remove('open');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-label', openLabel);
    if (restoreFocus) button.focus();
  };
  button.addEventListener('click', () => {
    const open = panel.classList.toggle('open');
    button.setAttribute('aria-expanded', String(open));
    button.setAttribute('aria-label', open ? closeLabel : openLabel);
  });
  panel.addEventListener('click', event => { if (event.target.closest('a')) close(); });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && panel.classList.contains('open')) close(true);
  });
  document.addEventListener('click', event => {
    if (!panel.contains(event.target) && !button.contains(event.target)) close();
  });
}
