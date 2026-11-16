import { motionAllowed } from '../core/motion.js';

document.querySelectorAll('[data-copy-phone]').forEach(button => button.addEventListener('click', async () => {
  const status = document.querySelector('[data-copy-status]');
  try {
    if (!navigator.clipboard?.writeText) throw new Error('Clipboard unavailable');
    await navigator.clipboard.writeText(button.dataset.copyPhone);
    status.textContent = 'Número copiado. Puedes pegarlo donde prefieras.';
  } catch { status.textContent = 'Selecciona el número visible para copiarlo, o abre WhatsApp directamente.'; }
}));

const choices = {
  hogar: {eyebrow:'UN VÍNCULO CON FUTURO',title:'Un hogar empieza antes del encuentro.',copy:'Conocer el proceso, conversar sobre tus posibilidades y hacer preguntas también es cuidar.',href:'/adopciones',link:'Prepararme para adoptar'},
  tiempo: {eyebrow:'EL VALOR DE ESTAR',title:'Tu tiempo puede encontrar un propósito.',copy:'Cuéntanos qué te gusta hacer y cuándo puedes acompañar. La colaboración empieza por una conversación.',href:'/voluntariado',link:'Explorar voluntariado'},
  ayuda: {eyebrow:'LO COTIDIANO TAMBIÉN SUMA',title:'Una manta, una idea, un gesto a tiempo.',copy:'Antes de llevar materiales, conversemos sobre necesidades y coordinación. Ayudar con intención evita desperdicios.',href:'/donaciones',link:'Conocer formas de ayudar'},
};
document.querySelectorAll('[data-care-moment]').forEach(moment => {
  moment.querySelectorAll('[data-care-choice]').forEach(button => button.addEventListener('click', () => {
    const choice = choices[button.dataset.careChoice];
    if (!choice) return;
    moment.querySelectorAll('[data-care-choice]').forEach(item => item.setAttribute('aria-pressed',String(item===button)));
    moment.querySelector('[data-care-eyebrow]').textContent = choice.eyebrow;
    moment.querySelector('[data-care-title]').textContent = choice.title;
    moment.querySelector('[data-care-copy]').textContent = choice.copy;
    const link = moment.querySelector('[data-care-link]');
    link.href = choice.href; link.firstChild.textContent = choice.link+' ';
    if (motionAllowed() && Element.prototype.animate) moment.querySelector('[data-care-tip]').animate([{opacity:.65,transform:'perspective(900px) rotateY(-5deg) translateY(6px)'},{opacity:1,transform:'perspective(900px) rotateY(0) translateY(0)'}],{duration:360,easing:'cubic-bezier(.2,.7,.3,1)'});
  }));
});
