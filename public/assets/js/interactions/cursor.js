import { cursorAllowed,motionAllowed } from '../core/motion.js';
import { svgIcon } from '../core/icons.js';

if (document.body.classList.contains('mode-public')) {
  let cursor, paw, queued=false, x=0,y=0,diameter=28,lastTarget=null,bounce,lastCelebration=0;
  const sparks = new Set();
  const hide = () => {
    if (cursor) cursor.hidden=true;
    document.body.classList.remove('has-context-cursor');
    lastTarget=null; bounce?.cancel();
    sparks.forEach(spark => spark.remove()); sparks.clear();
  };
  function greet(clicked = false) {
    if (!paw?.animate || !motionAllowed()) return;
    bounce?.cancel();
    bounce=paw.animate(clicked ? [
      {transform:'translateY(0) rotate(0) scale(1)'},
      {transform:'translateY(-5px) rotate(-14deg) scale(1.15)',offset:.4},
      {transform:'translateY(1px) rotate(7deg) scale(.95)',offset:.75},
      {transform:'translateY(0) rotate(0) scale(1)'},
    ] : [
      {transform:'rotate(0)'},{transform:'rotate(-12deg)',offset:.3},{transform:'rotate(8deg)',offset:.65},{transform:'rotate(0)'},
    ],{duration:clicked ? 420 : 340,easing:'cubic-bezier(.2,.7,.3,1)'});
  }
  document.addEventListener('pointermove', event => {
    const field=event.target.closest('input,textarea,select,[role="combobox"],[contenteditable],dialog');
    if (!cursorAllowed() || event.pointerType !== 'mouse' || field) { hide(); return; }
    if (!cursor) {
      cursor=document.createElement('div'); cursor.className='paw-cursor'; cursor.setAttribute('aria-hidden','true'); cursor.hidden=true;
      paw=svgIcon('paw'); cursor.append(paw); document.body.append(cursor);
    }
    const target=event.target.closest('[data-cursor],a,button');
    const kind=target?.dataset.cursor || (target ? 'link' : 'normal');
    cursor.dataset.state=kind; diameter=target ? 34 : 28;
    cursor.hidden=false; document.body.classList.add('has-context-cursor');
    if (target && target !== lastTarget) greet();
    lastTarget=target; x=event.clientX;y=event.clientY;
    if (queued) return;
    queued=true;
    requestAnimationFrame(() => { cursor.style.transform=`translate3d(${x-diameter/2}px,${y-diameter/2}px,0)`; queued=false; });
  },{passive:true});
  document.addEventListener('pointerdown', event => {
    if (event.target.closest('input,textarea,select,[role="combobox"],[contenteditable],dialog')) { hide();return; }
    if (!cursorAllowed() || !cursor || cursor.hidden || event.pointerType !== 'mouse') return;
    greet(true);
    if (!Element.prototype.animate || performance.now()-lastCelebration<180) return;
    lastCelebration=performance.now();
    for (const direction of [-1,1]) {
      const spark=document.createElement('span'); spark.className='paw-spark';spark.setAttribute('aria-hidden','true');spark.append(svgIcon('paw'));
      spark.style.left=`${event.clientX-6}px`;spark.style.top=`${event.clientY-6}px`;document.body.append(spark);sparks.add(spark);
      const animation=spark.animate([{opacity:0,transform:'translate(0,0) scale(.4)'},{opacity:.8,offset:.2},{opacity:0,transform:`translate(${direction*19}px,-30px) rotate(${direction*20}deg) scale(.85)`}],{duration:460,easing:'ease-out'});
      const remove=()=>{spark.remove();sparks.delete(spark);};animation.finished.then(remove,remove);
    }
  });
  document.addEventListener('pointerleave',hide);
  addEventListener('blur',hide);
  document.addEventListener('refugio:motion-preference',hide);
}
