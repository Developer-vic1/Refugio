document.querySelectorAll('[data-toggle-password]').forEach(button => {
  button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) return;
    const visible = input.type === 'password';
    input.type = visible ? 'text' : 'password';
    button.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
    button.setAttribute('aria-pressed', String(visible));
  });
});

const busy = new WeakSet();
const restore = form => {
  busy.delete(form);
  form.removeAttribute('aria-busy');
  form.querySelectorAll('[data-submit-label]').forEach(button => {
    button.textContent = button.dataset.submitLabel;
    delete button.dataset.submitLabel;
    button.removeAttribute('aria-disabled');
  });
  const status = form.querySelector('[data-submit-status]');
  if (status) status.textContent = '';
};

// Solo formularios que optan por este contrato. No altera campos ni CSRF.
document.addEventListener('submit', event => {
  const form = event.target;
  if (!form.matches('form[data-submit-state]') || event.defaultPrevented) return;
  if (busy.has(form)) { event.preventDefault(); return; }
  busy.add(form);
  form.setAttribute('aria-busy', 'true');
  const button = event.submitter;
  if (button) {
    button.dataset.submitLabel = button.textContent;
    button.textContent = 'Enviando…';
    button.setAttribute('aria-disabled', 'true');
  }
  const status = form.querySelector('[data-submit-status]');
  if (status) status.textContent = 'Enviando formulario. Espera la respuesta.';
});
window.addEventListener('pageshow', () => document.querySelectorAll('form[data-submit-state]').forEach(restore));
