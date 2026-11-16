import { motionAllowed,systemReduced,finePointer,wantsReduced,wantsCursor,setReduced,setCursor } from '../core/motion.js';

document.querySelectorAll('.motion-preferences').forEach(panel => {
  const reduce = panel.querySelector('[data-reduce-motion]');
  const cursor = panel.querySelector('[data-expressive-cursor]');
  const update = () => {
    reduce.checked = systemReduced() || wantsReduced();
    reduce.disabled = systemReduced();
    cursor.checked = wantsCursor();
    cursor.disabled = !motionAllowed();
    panel.querySelector('[data-cursor-setting]').hidden = !finePointer();
    panel.querySelector('[data-motion-note]').hidden = !systemReduced();
  };
  reduce.addEventListener('change', () => setReduced(reduce.checked));
  cursor.addEventListener('change', () => setCursor(cursor.checked));
  document.addEventListener('refugio:motion-preference',update);
  update();
});
