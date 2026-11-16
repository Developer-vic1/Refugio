document.querySelectorAll('[data-close-toast]').forEach(button => {
  button.addEventListener('click', () => button.closest('[data-toast]')?.remove());
});

// Interceptar submit cubre botones, Enter y requestSubmit; mantiene la validación nativa.
const dialog = document.querySelector('[data-confirm-dialog]');
let pending = null;
const approved = new WeakSet();

document.addEventListener('submit', event => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement) || form.method === 'dialog') return;
  const submitter = event.submitter;
  const message = submitter?.dataset.confirm ?? form.dataset.confirm;
  if (!message || approved.delete(form)) return;
  event.preventDefault();
  if (pending) return;
  if (!dialog || typeof dialog.showModal !== 'function') {
    if (window.confirm(message)) {
      approved.add(form);
      form.requestSubmit(submitter || undefined);
    }
    return;
  }
  pending = { form, submitter, focus: document.activeElement };
  dialog.querySelector('[data-dialog-message]').textContent = message;
  dialog.returnValue = 'cancel';
  dialog.showModal();
  dialog.querySelector('[value="cancel"]').focus();
});

dialog?.addEventListener('close', () => {
  const current = pending;
  pending = null;
  if (!current) return;
  current.focus?.focus();
  if (dialog.returnValue === 'confirm' && current.form.isConnected) {
    approved.add(current.form);
    current.form.requestSubmit(current.submitter || undefined);
  }
});
