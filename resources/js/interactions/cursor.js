import { cursorAllowed } from '../core/motion.js';

if (document.body.classList.contains('mode-public')) {
  let cursor, queued=false, x=0,y=0,diameter=24;
  const hide = () => { if (cursor) cursor.hidden = true; document.body.classList.remove('has-context-cursor'); };
  document.addEventListener('pointermove', event => {
    const field = event.target.closest('input,textarea,select,[role="combobox"],[contenteditable],dialog');
    if (!cursorAllowed() || event.pointerType !== 'mouse' || field) { hide(); return; }
    if (!cursor) { cursor = document.createElement('div'); cursor.className = 'context-cursor'; cursor.setAttribute('aria-hidden','true'); document.body.append(cursor); }
    const target = event.target.closest('[data-cursor],a,button');
    const kind = target?.dataset.cursor || (target ? 'link' : 'normal');
    cursor.dataset.state = kind;
    cursor.textContent = {link:'Ver',zoom:'Ampliar',drag:'Mover',click:'Elegir'}[kind] || '';
    diameter = kind === 'normal' ? 24 : 52;
    cursor.hidden = false; document.body.classList.add('has-context-cursor');
    x=event.clientX; y=event.clientY;
    if (queued) return;
    queued=true;
    requestAnimationFrame(() => { cursor.style.transform=`translate3d(${x-diameter/2}px,${y-diameter/2}px,0)`; queued=false; });
  },{passive:true});
  document.addEventListener('pointerdown', () => cursor?.classList.add('is-pressed'));
  addEventListener('pointerup', () => cursor?.classList.remove('is-pressed'));
  document.addEventListener('pointerleave',hide);
  addEventListener('blur',hide);
  document.addEventListener('refugio:motion-preference',hide);
}
