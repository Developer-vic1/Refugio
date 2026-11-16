import { svgIcon } from '../core/icons.js';

let current = null;
let serial = 0;
const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase('es');

document.querySelectorAll('select:not([multiple]):not([data-native-select])').forEach(select => {
  if (select.dataset.enhanced) return;
  select.dataset.enhanced = 'true';
  select.dataset.refugioSelect = '';
  const id = select.id || `refugio-select-${++serial}`;
  select.id = id;
  let label = document.querySelector(`label[for="${CSS.escape(id)}"]`) || select.closest('label');
  const wrapper = document.createElement('div');
  wrapper.className = 'refugio-select';
  if (label && label.contains(select)) {
    const field = document.createElement('div');
    field.className = 'form-field';
    label.before(field);
    field.append(label, wrapper);
    wrapper.append(select);
  } else { select.before(wrapper); wrapper.append(select); }

  const trigger = document.createElement('button');
  trigger.type = 'button';
  trigger.id = `${id}-trigger`;
  trigger.className = 'refugio-select-trigger';
  trigger.setAttribute('role','combobox');
  trigger.setAttribute('aria-haspopup','listbox');
  trigger.setAttribute('aria-controls',`${id}-options`);
  trigger.setAttribute('aria-expanded','false');
  if (label) {
    label.id ||= `${id}-label`;
    label.htmlFor = trigger.id;
    trigger.setAttribute('aria-labelledby',label.id);
    select.setAttribute('aria-label',label.textContent.trim());
  } else trigger.setAttribute('aria-label', select.getAttribute('aria-label') || select.name || 'Elegir una opción');
  if (select.required) trigger.setAttribute('aria-required','true');
  trigger.disabled = select.disabled;
  const text = document.createElement('span');
  trigger.append(text, svgIcon('chevron-down'));
  const error = document.createElement('small');
  error.className = 'field-error'; error.id = `${id}-error`; error.hidden = true;
  trigger.setAttribute('aria-describedby', [select.getAttribute('aria-describedby'),error.id].filter(Boolean).join(' '));
  wrapper.append(trigger,error);
  select.classList.add('refugio-native-select');
  select.setAttribute('aria-hidden','true'); select.tabIndex = -1;

  const panel = document.createElement('div');
  panel.className = 'refugio-options'; panel.id = `${id}-options`; panel.hidden = true;
  panel.setAttribute('role','listbox');
  panel.setAttribute('aria-label', label?.textContent.trim() || 'Opciones');
  const options = [...select.options].map((option,index) => {
    const item = document.createElement('div');
    item.id = `${id}-option-${index}`; item.setAttribute('role','option');
    item.textContent = option.textContent;
    const disabled = option.disabled || (option.parentElement.tagName === 'OPTGROUP' && option.parentElement.disabled);
    item.setAttribute('aria-disabled',String(disabled));
    item.addEventListener('pointerdown', event => { event.preventDefault(); if (!disabled) { active = index; close(true); trigger.focus(); } });
    panel.append(item);
    return { item, disabled };
  });
  document.body.append(panel);
  let active = Math.max(0,select.selectedIndex), buffer = '', typedAt = 0, suppressKeyboardClickUntil = 0;
  const enabled = index => index >= 0 && index < options.length && !options[index].disabled;
  function render() {
    text.textContent = select.selectedOptions[0]?.textContent || 'Elegir una opción';
    options.forEach(({item},index) => {
      item.setAttribute('aria-selected',String(index === select.selectedIndex));
      item.classList.toggle('is-active', index === active);
    });
  }
  function close(commit = false) {
    if (panel.hidden) return;
    if (commit && enabled(active) && select.selectedIndex !== active) {
      select.selectedIndex = active;
      select.dispatchEvent(new Event('input',{bubbles:true}));
      select.dispatchEvent(new Event('change',{bubbles:true}));
    }
    panel.hidden = true; trigger.setAttribute('aria-expanded','false'); trigger.removeAttribute('aria-activedescendant');
    if (current?.panel === panel) current = null;
    render();
  }
  function highlight(index) {
    if (!enabled(index)) return;
    active = index;
    options.forEach(({item},i) => item.classList.toggle('is-active',i === active));
    trigger.setAttribute('aria-activedescendant',options[active].item.id);
    options[active].item.scrollIntoView({block:'nearest'});
  }
  function position() {
    const rect = trigger.getBoundingClientRect();
    if (rect.bottom < 0 || rect.top > innerHeight) { close(false); return false; }
    const below = innerHeight - rect.bottom - 16;
    const above = rect.top - 16;
    const up = below < Math.min(panel.scrollHeight || 280,200) && above > below;
    panel.style.width = `${Math.min(Math.max(rect.width,190),innerWidth-32)}px`;
    panel.style.left = `${Math.max(16,Math.min(rect.left,innerWidth-parseFloat(panel.style.width)-16))}px`;
    panel.style.maxHeight = `${Math.max(80,Math.min(320,up ? above : below))}px`;
    panel.style.top = up ? 'auto' : `${rect.bottom+6}px`;
    panel.style.bottom = up ? `${innerHeight-rect.top+6}px` : 'auto';
    return true;
  }
  function open() {
    if (trigger.disabled || !options.some(option => !option.disabled)) return;
    if (current && current.panel !== panel) current.close(true);
    active = enabled(select.selectedIndex) ? select.selectedIndex : options.findIndex(option => !option.disabled);
    if (!position()) return;
    panel.hidden = false;
    trigger.setAttribute('aria-expanded','true');
    current = {panel,trigger,close,position};
    highlight(active);
  }
  function step(amount) {
    const available = options.map((option,index) => option.disabled ? -1 : index).filter(index => index >= 0);
    const position = Math.max(0,available.indexOf(active));
    const next = available[Math.max(0,Math.min(available.length-1,position+amount))];
    if (next !== undefined) highlight(next);
  }
  trigger.addEventListener('click', event => {
    if (event.detail === 0 && performance.now() < suppressKeyboardClickUntil) return;
    panel.hidden ? open() : close(false);
  });
  trigger.addEventListener('blur', () => close(true));
  trigger.addEventListener('keydown', event => {
    if (event.key === 'Escape') { if (!panel.hidden) { event.preventDefault(); close(false); } return; }
    if (event.key === 'Tab') { close(true); return; }
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault(); suppressKeyboardClickUntil = performance.now()+300;
      panel.hidden ? open() : close(true); return;
    }
    if (['ArrowDown','ArrowUp','Home','End','PageDown','PageUp'].includes(event.key)) {
      event.preventDefault(); const wasClosed = panel.hidden; if (wasClosed) open();
      if (event.altKey && event.key === 'ArrowUp') { close(true); return; }
      if (event.key === 'Home') highlight(options.findIndex(option => !option.disabled));
      else if (event.key === 'End') highlight(options.map(option => option.disabled).lastIndexOf(false));
      else if (!wasClosed) step(event.key === 'ArrowDown' ? 1 : event.key === 'ArrowUp' ? -1 : event.key === 'PageDown' ? 10 : -10);
      return;
    }
    if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
      event.preventDefault();
      if (panel.hidden) open();
      const now = Date.now();
      buffer = now-typedAt > 650 || buffer === event.key ? event.key : buffer+event.key; typedAt = now;
      const query = normalize(buffer);
      const order = [...options.keys()].map(i => (active+1+i)%options.length);
      const match = order.find(i => enabled(i) && normalize(select.options[i].textContent).startsWith(query));
      if (match !== undefined) highlight(match);
    }
  });
  trigger.addEventListener('keyup', event => { if (event.key === 'Enter' || event.key === ' ') event.preventDefault(); });
  select.addEventListener('change', () => { render(); trigger.removeAttribute('aria-invalid'); error.hidden = true; });
  select.addEventListener('invalid', event => { event.preventDefault(); trigger.setAttribute('aria-invalid','true'); error.textContent = 'Elige una opción para continuar.'; error.hidden = false; trigger.focus(); });
  select.form?.addEventListener('reset', () => requestAnimationFrame(() => { close(false); render(); trigger.removeAttribute('aria-invalid'); error.hidden = true; }));
  render();
});

document.addEventListener('pointerdown', event => { if (current && !current.panel.contains(event.target) && !current.trigger.contains(event.target)) current.close(true); });
document.addEventListener('refugio:close-selects', () => current?.close(false));
addEventListener('resize', () => current?.close(false));
addEventListener('scroll', event => {
  if (current && !current.panel.contains(event.target)) requestAnimationFrame(() => current?.position());
},true);
