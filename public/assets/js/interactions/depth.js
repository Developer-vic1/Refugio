import { motionAllowed,finePointer } from '../core/motion.js';
const depthAllowed = () => motionAllowed() && finePointer() && document.documentElement.dataset.motion !== 'serena';

document.querySelectorAll('[data-depth-scene]').forEach(scene => {
  const layer = scene.querySelector('[data-depth-layer]');
  if (!layer) return;
  let bounds, x=0, y=0, queued=false;
  const reset = () => { layer.style.rotate = ''; layer.style.translate = ''; };
  scene.addEventListener('pointerenter', () => { bounds = scene.getBoundingClientRect(); });
  scene.addEventListener('pointermove', event => {
    if (!depthAllowed() || event.pointerType !== 'mouse') { reset(); return; }
    bounds ||= scene.getBoundingClientRect();
    x = Math.max(-.5,Math.min(.5,(event.clientX-bounds.left)/bounds.width-.5));
    y = Math.max(-.5,Math.min(.5,(event.clientY-bounds.top)/bounds.height-.5));
    if (queued) return;
    queued = true;
    requestAnimationFrame(() => {
      if (depthAllowed()) { layer.style.rotate = `${-y} ${x} 0 2deg`; layer.style.translate = `${x*5}px ${y*5}px`; }
      queued = false;
    });
  });
  scene.addEventListener('pointerleave',reset);
  document.addEventListener('refugio:motion-preference',reset);
});

document.querySelectorAll('[data-magnetic]').forEach(button => {
  let bounds;
  const reset = () => { button.style.translate = ''; };
  button.addEventListener('pointerenter', () => { bounds = button.getBoundingClientRect(); });
  button.addEventListener('pointermove', event => {
    if (!depthAllowed() || event.pointerType !== 'mouse') { reset(); return; }
    bounds ||= button.getBoundingClientRect();
    const x = Math.max(-3,Math.min(3,(event.clientX-bounds.left-bounds.width/2)*.04));
    const y = Math.max(-3,Math.min(3,(event.clientY-bounds.top-bounds.height/2)*.08));
    button.style.translate = `${x}px ${y}px`;
  });
  button.addEventListener('pointerleave',reset);
  document.addEventListener('refugio:motion-preference',reset);
});
