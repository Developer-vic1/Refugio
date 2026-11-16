import { motionAllowed } from '../core/motion.js';

/** Motor propio de secuencias. API nativa; sin dependencia ni navegación interceptada. */
export class Hyperframes {
  constructor() {
    this.running = new Set();
    document.addEventListener('refugio:motion-preference', () => {
      if (!motionAllowed()) this.running.forEach(animation => animation.cancel());
    });
    document.addEventListener('focusin', event => {
      this.running.forEach(animation => { if (animation.effect?.target?.contains(event.target)) animation.finish(); });
    });
  }
  tokens() {
    const tokens = getComputedStyle(document.documentElement);
    return {
      duration: Number(tokens.getPropertyValue('--motion-duration')) || 520,
      stagger: Number(tokens.getPropertyValue('--motion-stagger')) || 85,
      distance: Number(tokens.getPropertyValue('--motion-distance')) || 18,
      easing: tokens.getPropertyValue('--motion-easing').trim() || 'ease-out',
    };
  }
  play(elements, options = {}) {
    if (!motionAllowed() || !Element.prototype.animate) return [];
    const settings = {...this.tokens(),...options};
    return [...elements].filter(Boolean).map((element,index) => {
      const frames = settings.effect === 'photo' ? [
        {opacity:.25,transform:'scale(1.045)'},{opacity:1,transform:'scale(1)'},
      ] : settings.effect === 'scale' ? [
        {opacity:0,transform:'scale(.97)'},{opacity:1,transform:'scale(1)'},
      ] : [
        {opacity:0,transform:`translateY(${settings.distance}px)`},{opacity:1,transform:'translateY(0)'},
      ];
      const animation = element.animate(frames,{duration:settings.duration,delay:(settings.delay||0)+Math.min(index,5)*settings.stagger,easing:settings.easing,fill:'backwards'});
      this.running.add(animation);
      const release = () => this.running.delete(animation);
      animation.addEventListener('finish',release,{once:true});
      animation.addEventListener('cancel',release,{once:true});
      return animation;
    });
  }
  timeline(steps, scope = document) {
    const animations = [];
    let elapsed = 0;
    for (const step of steps) {
      const targets = typeof step.targets === 'string' ? scope.querySelectorAll(step.targets) : step.targets;
      const duration = step.duration || this.tokens().duration;
      const at = step.at ?? elapsed;
      animations.push(...this.play(targets || [],{...step,duration,delay:at}));
      elapsed = at+duration;
    }
    return Promise.all(animations.map(animation => animation.finished.catch(() => undefined)));
  }
}

const sequences = new Hyperframes();
if ('IntersectionObserver' in window) {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      observer.unobserve(entry.target);
      if (entry.target.matches('[data-hyperframes="hero"]')) {
        sequences.timeline([
          {targets:'.hero-photo',effect:'photo',duration:900,at:0},
          {targets:'.hero-copy .micro-label',at:0},
          {targets:'.hero-copy h1',at:90},
          {targets:'.hero-copy > p',at:190},
          {targets:'.hero-actions',at:280},
          {targets:'.hero-caption',at:380},
        ],entry.target);
      } else sequences.play(entry.target.matches('[data-sequence]') ? entry.target.children : [entry.target]);
    });
  },{threshold:.08});
  document.querySelectorAll('[data-hyperframes="hero"],.reveal,[data-sequence]').forEach(element => {
    if (!element.matches('[data-hyperframes="hero"]') && element.closest('[data-hyperframes="hero"]')) return;
    observer.observe(element);
  });
}
const progress = document.querySelector('.scroll-progress');
if (progress) {
  let queued = false;
  const update = () => {
    const maximum = document.documentElement.scrollHeight-innerHeight;
    progress.style.transform = `scaleX(${maximum>0 ? Math.min(1,scrollY/maximum) : 0})`;
    queued=false;
  };
  addEventListener('scroll', () => { if (!queued) {queued=true;requestAnimationFrame(update);} },{passive:true});
  addEventListener('pageshow',update);
  update();
}
for (const type of ['pageswap','pagereveal']) addEventListener(type,event => { if (!motionAllowed()) event.viewTransition?.skipTransition(); });
