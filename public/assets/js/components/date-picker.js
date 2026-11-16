import { svgIcon } from '../core/icons.js';

const inputs = [...document.querySelectorAll('input[type="date"]:not([data-native-date])')];
if (inputs.length && typeof HTMLDialogElement !== 'undefined') {
  const makeDate = (year,month,day) => { const date = new Date(0); date.setUTCHours(12,0,0,0); date.setUTCFullYear(year,month,day); return date; };
  const parse = value => {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value || '');
    if (!match) return null;
    const date = makeDate(Number(match[1]),Number(match[2])-1,Number(match[3]));
    return date.getUTCFullYear() === Number(match[1]) && date.getUTCMonth() === Number(match[2])-1 && date.getUTCDate() === Number(match[3]) ? date : null;
  };
  const iso = date => `${String(date.getUTCFullYear()).padStart(4,'0')}-${String(date.getUTCMonth()+1).padStart(2,'0')}-${String(date.getUTCDate()).padStart(2,'0')}`;
  const dayLabel = date => new Intl.DateTimeFormat('es-BO',{dateStyle:'full',timeZone:'UTC'}).format(date);
  const now = new Date();
  const today = makeDate(now.getFullYear(),now.getMonth(),now.getDate());
  const dialog = document.createElement('dialog');
  dialog.className = 'refugio-calendar';
  dialog.setAttribute('aria-labelledby','refugio-calendar-title');
  dialog.setAttribute('aria-describedby','refugio-calendar-help');
  dialog.innerHTML = '<div class="calendar-heading"><div><span class="micro-label">UN DÍA PARA RECORDAR</span><h2 id="refugio-calendar-title">Elige una fecha</h2></div><button type="button" class="icon-button" data-calendar-close aria-label="Cerrar calendario"></button></div><div class="calendar-navigation"><button type="button" class="icon-button" data-month="-12" aria-label="Año anterior">«</button><button type="button" class="icon-button" data-month="-1" aria-label="Mes anterior"></button><h3 data-calendar-month id="refugio-calendar-month" aria-live="polite"></h3><button type="button" class="icon-button" data-month="1" aria-label="Mes siguiente"></button><button type="button" class="icon-button" data-month="12" aria-label="Año siguiente">»</button></div><table role="grid" aria-labelledby="refugio-calendar-month"><thead><tr><th scope="col" abbr="Lunes">Lu</th><th scope="col" abbr="Martes">Ma</th><th scope="col" abbr="Miércoles">Mi</th><th scope="col" abbr="Jueves">Ju</th><th scope="col" abbr="Viernes">Vi</th><th scope="col" abbr="Sábado">Sá</th><th scope="col" abbr="Domingo">Do</th></tr></thead><tbody></tbody></table><p id="refugio-calendar-help">Usa las flechas para recorrer los días. Enter elige; Escape vuelve sin cambiar.</p><div class="calendar-actions"><button type="button" class="btn btn-outline" data-calendar-clear>Sin fecha</button><button type="button" class="btn btn-primary" data-calendar-today>Elegir hoy</button></div>';
  dialog.querySelector('[data-calendar-close]').append(svgIcon('close'));
  dialog.querySelector('[data-month="-1"]').append(svgIcon('chevron-left'));
  dialog.querySelector('[data-month="1"]').append(svgIcon('chevron-right'));
  document.body.append(dialog);
  let owner = null, active = today, selected = null, min = makeDate(1,0,1), max = makeDate(9999,11,31);
  const clamp = date => date < min ? new Date(min) : date > max ? new Date(max) : date;
  const allowed = date => date >= min && date <= max;
  const shift = (date,months) => {
    const first = makeDate(date.getUTCFullYear(),date.getUTCMonth()+months,1);
    const last = makeDate(first.getUTCFullYear(),first.getUTCMonth()+1,0).getUTCDate();
    return clamp(makeDate(first.getUTCFullYear(),first.getUTCMonth(),Math.min(last,date.getUTCDate())));
  };
  function updateButton(input,button,label) {
    const date = parse(input.value);
    button.setAttribute('aria-label',date ? `Cambiar fecha de ${label}: ${dayLabel(date)}` : `Elegir fecha de ${label}`);
  }
  function render(focus = false) {
    const year = active.getUTCFullYear(), month = active.getUTCMonth();
    dialog.querySelector('[data-calendar-month]').textContent = new Intl.DateTimeFormat('es-BO',{month:'long',year:'numeric',timeZone:'UTC'}).format(active);
    dialog.querySelectorAll('[data-month]').forEach(button => { button.disabled = iso(shift(active,Number(button.dataset.month))) === iso(active); });
    dialog.querySelector('[data-calendar-today]').disabled = !allowed(today);
    dialog.querySelector('[data-calendar-clear]').hidden = owner.input.required;
    const body = dialog.querySelector('tbody'); body.replaceChildren();
    const offset = (makeDate(year,month,1).getUTCDay()+6)%7;
    const last = makeDate(year,month+1,0).getUTCDate();
    for (let cell=0;cell<Math.ceil((offset+last)/7)*7;cell++) {
      if (cell%7===0) body.append(document.createElement('tr'));
      const td = document.createElement('td'); body.lastChild.append(td);
      const day = cell-offset+1;
      if (day < 1 || day > last) continue;
      const date = makeDate(year,month,day), value = iso(date);
      const button = document.createElement('button'); button.type = 'button';
      button.textContent = String(day); button.dataset.day = value;
      button.setAttribute('aria-label',dayLabel(date));
      button.tabIndex = value === iso(active) ? 0 : -1;
      button.disabled = !allowed(date);
      if (selected && value === iso(selected)) { td.setAttribute('aria-selected','true'); button.classList.add('selected'); }
      if (value === iso(today)) button.setAttribute('aria-current','date');
      button.addEventListener('click', () => choose(value));
      td.append(button);
    }
    if (focus) body.querySelector(`[data-day="${iso(active)}"]`)?.focus();
  }
  function choose(value) {
    const date = parse(value);
    if (value && (!date || !allowed(date))) return;
    owner.input.value = value;
    owner.input.dispatchEvent(new Event('input',{bubbles:true}));
    owner.input.dispatchEvent(new Event('change',{bubbles:true}));
    dialog.close('selected');
  }
  dialog.querySelector('[data-calendar-close]').addEventListener('click', () => dialog.close('cancel'));
  dialog.querySelector('[data-calendar-clear]').addEventListener('click', () => choose(''));
  dialog.querySelector('[data-calendar-today]').addEventListener('click', () => choose(iso(today)));
  dialog.querySelectorAll('[data-month]').forEach(button => button.addEventListener('click', () => { active = shift(active,Number(button.dataset.month)); render(); }));
  dialog.addEventListener('keydown', event => {
    if (!event.target.matches('[data-day]')) return;
    let next = null;
    const weekDay = (active.getUTCDay()+6)%7;
    const delta = {ArrowLeft:-1,ArrowRight:1,ArrowUp:-7,ArrowDown:7,Home:-weekDay,End:6-weekDay}[event.key];
    if (delta !== undefined) next = makeDate(active.getUTCFullYear(),active.getUTCMonth(),active.getUTCDate()+delta);
    if (event.key === 'PageUp' || event.key === 'PageDown') next = shift(active,(event.key === 'PageUp' ? -1 : 1)*(event.shiftKey ? 12 : 1));
    if (next) { event.preventDefault(); active = clamp(next); render(true); }
  });
  dialog.addEventListener('close', () => owner?.button.focus());

  inputs.forEach((input,index) => {
    input.dataset.refugioDate = '';
    input.id ||= `refugio-date-${index}`;
    const label = document.querySelector(`label[for="${CSS.escape(input.id)}"]`) || input.closest('label');
    const control = document.createElement('div'); control.className = 'refugio-date-control';
    if (label && label.contains(input)) {
      const field = document.createElement('div'); field.className = 'form-field';
      label.before(field); field.append(label,control); control.append(input); label.htmlFor = input.id;
    } else { input.before(control); control.append(input); }
    const labelText = label?.textContent.trim() || input.getAttribute('aria-label') || 'este campo';
    const button = document.createElement('button'); button.type = 'button'; button.className = 'icon-button'; button.disabled = input.disabled || input.readOnly;
    button.append(svgIcon('calendar')); control.append(button);
    updateButton(input,button,labelText);
    input.addEventListener('change', () => updateButton(input,button,labelText));
    input.addEventListener('keydown', event => { if (event.altKey && event.key === 'ArrowDown') { event.preventDefault(); button.click(); } });
    input.form?.addEventListener('reset', () => requestAnimationFrame(() => updateButton(input,button,labelText)));
    button.addEventListener('click', () => {
      document.dispatchEvent(new Event('refugio:close-selects'));
      owner = {input,button};
      selected = parse(input.value);
      min = parse(input.min) || makeDate(1,0,1); max = parse(input.max) || makeDate(9999,11,31);
      if (min > max) return;
      active = clamp(selected || today);
      dialog.querySelector('#refugio-calendar-title').textContent = labelText;
      render(); dialog.showModal(); render(true);
    });
  });
}
