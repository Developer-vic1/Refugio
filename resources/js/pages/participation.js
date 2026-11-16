import { motionAllowed } from '../core/motion.js';

document.querySelectorAll('[data-coordination-form]').forEach(form => {
  const status=form.querySelector('[data-coordinate-status]');
  const ready=form.querySelector('[data-coordinate-ready]');
  const submit=form.querySelector('[type=submit]');
  let busy=false;
  const updateQuantity=()=>{
    const container=form.querySelector('[data-coordinate-quantity]');
    if(!container)return;
    const hide=form.elements.namedItem('tipo').value==='aporte';
    container.hidden=hide;container.querySelectorAll('input,select').forEach(field=>{field.disabled=hide;});
  };
  form.addEventListener('input',()=>{ready.hidden=true;updateQuantity();});
  form.addEventListener('change',updateQuantity);updateQuantity();
  form.addEventListener('submit',async event=>{
    event.preventDefault();if(busy)return;
    busy=true;submit.disabled=true;form.setAttribute('aria-busy','true');
    status.textContent='Dando forma a tu mensaje…';status.classList.remove('has-error');ready.hidden=true;
    try {
      const response=await fetch(form.action,{method:'POST',body:new FormData(form),credentials:'same-origin',headers:{Accept:'application/json'}});
      if(response.status===419)throw new Error('Actualiza la página para preparar el mensaje de nuevo.');
      const result=await response.json();
      if(!response.ok)throw new Error(result.message||'Revisa los datos e inténtalo de nuevo.');
      ready.querySelector('[data-coordinate-preview]').textContent=result.message;
      ready.querySelector('[data-coordinate-link]').href=result.whatsapp;
      ready.hidden=false;status.textContent='Mensaje preparado. Revísalo y decide cuándo abrir WhatsApp.';
      if(motionAllowed() && ready.animate)ready.animate([{opacity:0,transform:'translateY(12px)'},{opacity:1,transform:'translateY(0)'}],{duration:300,easing:'ease-out'});
      ready.scrollIntoView({behavior:motionAllowed()?'smooth':'auto',block:'nearest'});
    } catch(error) {status.textContent=error instanceof TypeError?'No pudimos conectar. Tus respuestas siguen aquí; inténtalo de nuevo.':error.message;status.classList.add('has-error');}
    finally {busy=false;submit.disabled=false;form.removeAttribute('aria-busy');}
  });
});

document.querySelectorAll('[data-editorial-filter]').forEach(section=>{
  const search=section.querySelector('input[type=search]');
  const buttons=[...section.querySelectorAll('[data-story-category]')];
  const cards=[...section.querySelectorAll('[data-story-card]')];
  let category='all';
  const normalize=value=>value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase('es');
  const filter=()=>{
    const query=normalize(search?.value.trim()||'');let count=0;
    cards.forEach(card=>{const visible=(category==='all'||card.dataset.category===category)&&normalize(card.innerText).includes(query);card.hidden=!visible;if(visible)count++;});
    section.querySelector('[data-story-count]').textContent=count+' '+(count===1?'lectura para descubrir':'lecturas para descubrir');
    section.querySelector('[data-story-empty]').hidden=count>0;
    section.querySelector('[data-journal-grid]').dataset.filtered=String(category!=='all'||query!=='');
  };
  buttons.forEach(button=>button.addEventListener('click',()=>{category=button.dataset.storyCategory;buttons.forEach(item=>item.setAttribute('aria-pressed',String(item===button)));filter();}));
  search?.addEventListener('input',filter);
});
