/** Secuencias propias y ligeras sobre Web Animations API. No es una librería externa. */
export class Hyperframes {
  constructor() {
    this.preference = matchMedia('(prefers-reduced-motion: reduce)');
    this.running = new Set();
    this.preference.addEventListener('change', () => {
      if (this.preference.matches) this.running.forEach(animation => animation.cancel());
    });
  }

  play(elements) {
    if (this.preference.matches || !Element.prototype.animate) return;
    const tokens = getComputedStyle(document.documentElement);
    const duration = Number(tokens.getPropertyValue('--motion-duration')) || 520;
    const stagger = Number(tokens.getPropertyValue('--motion-stagger')) || 85;
    const distance = Number(tokens.getPropertyValue('--motion-distance')) || 18;
    const easing = tokens.getPropertyValue('--motion-easing').trim() || 'ease-out';
    [...elements].forEach((element, index) => {
      const animation = element.animate([
        { opacity: 0, transform: `translateY(${distance}px)` },
        { opacity: 1, transform: 'translateY(0)' },
      ], { duration, delay: Math.min(index, 5) * stagger, easing, fill: 'backwards' });
      this.running.add(animation);
      const release = () => this.running.delete(animation);
      animation.addEventListener('finish', release, { once: true });
      animation.addEventListener('cancel', release, { once: true });
    });
  }
}

const sequences = new Hyperframes();
if ('IntersectionObserver' in window) {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      observer.unobserve(entry.target);
      const grouped = entry.target.matches('[data-sequence], .hero-copy');
      sequences.play(grouped ? entry.target.children : [entry.target]);
    });
  }, { threshold: .08 });
  document.querySelectorAll('.reveal, [data-sequence]').forEach(element => observer.observe(element));
}

const progress = document.querySelector('.scroll-progress');
if (progress) {
  let queued = false;
  const update = () => {
    const maximum = document.documentElement.scrollHeight - innerHeight;
    progress.style.transform = `scaleX(${maximum > 0 ? Math.min(1, scrollY / maximum) : 0})`;
    queued = false;
  };
  addEventListener('scroll', () => {
    if (!queued) { queued = true; requestAnimationFrame(update); }
  }, { passive: true });
  update();
}
