import { motionAllowed } from '../core/motion.js';

document.querySelectorAll('[data-readiness]').forEach(list => {
  list.addEventListener('change', () => {
    const count=list.querySelectorAll('input:checked').length;
    document.querySelector('[data-readiness-status]').textContent='Has revisado '+count+' de 5 preparativos. Las preguntas que te queden también podemos conversarlas.';
  });
});

document.querySelectorAll('[data-prepared-form]').forEach(form => {
  const steps=[...form.querySelectorAll('[data-form-step]')];
  const next=form.querySelector('[data-step-next]');
  const back=form.querySelector('[data-step-back]');
  const submit=form.querySelector('[data-prepare-pdf]');
  const summary=form.querySelector('[data-review-summary]');
  const notice=form.querySelector('[data-form-error]');
  const status=form.querySelector('[data-prepare-status]');
  const ready=form.parentElement.querySelector('[data-pdf-ready]');
  const download=ready.querySelector('[data-pdf-download]');
  const draft=form.querySelector('[data-save-draft]');
  const clear=form.querySelector('[data-clear-draft]');
  const draftStatus=form.querySelector('[data-draft-status]');
  const draftKey='refugio.adoption.draft.'+form.dataset.animalId;
  const fields=[...form.elements].filter(field => field.name && field.name!=='_csrf' && field.name!=='acepta_privacidad' && field.matches('input,textarea'));
  let current=0,busy=false,objectUrl=null,timer;
  form.noValidate=true;
  form.querySelectorAll('[data-step-navigation],[data-draft-panel]').forEach(element => {element.hidden=false;});
  summary.hidden=false;

  const setDraftStatus = message => {if(draftStatus.textContent!==message)draftStatus.textContent=message;};
  const saveDraft = () => {
    if(!draft.checked)return;
    try {
      sessionStorage.setItem(draftKey,JSON.stringify(Object.fromEntries(fields.map(field=>[field.name,field.value]))));
      clear.hidden=false;setDraftStatus('Tu avance está guardado en esta pestaña.');
    } catch {draft.checked=false;setDraftStatus('Este navegador no pudo guardar el avance. Tus respuestas siguen en el formulario.');}
  };
  const discardDraft = message => {
    clearTimeout(timer);
    try {sessionStorage.removeItem(draftKey);} catch { /* El formulario sigue disponible. */ }
    draft.checked=false;clear.hidden=true;setDraftStatus(message);
  };
  try {
    const saved=JSON.parse(sessionStorage.getItem(draftKey)||'null');
    if(saved && typeof saved==='object' && !Array.isArray(saved)) {
      if(!fields.some(field=>field.value!=='')) fields.forEach(field => {if(typeof saved[field.name]==='string') field.value=saved[field.name].slice(0,field.maxLength>0?field.maxLength:2000);});
      draft.checked=true;clear.hidden=false;setDraftStatus('Recuperamos el borrador de esta pestaña. Revisa tus respuestas antes de continuar.');
    }
  } catch {setDraftStatus('Puedes completar la ficha sin guardar un borrador.');}
  draft.addEventListener('change',()=>draft.checked?saveDraft():discardDraft('El borrador guardado se eliminó; tus respuestas siguen en este formulario.'));
  clear.addEventListener('click',()=>discardDraft('El borrador guardado se eliminó; tus respuestas siguen en este formulario.'));

  function updateReview() {
    form.querySelectorAll('[data-review-value]').forEach(item => {item.textContent=form.elements.namedItem(item.dataset.reviewValue)?.value.trim()||'Sin información adicional.';});
  }
  function showStep(index, focus = false) {
    current=Math.max(0,Math.min(steps.length-1,index));
    steps.forEach((step,i)=>{step.hidden=i!==current;});
    form.querySelectorAll('[data-step-indicator]').forEach((indicator,i)=> {
      if(i===current)indicator.setAttribute('aria-current','step');else indicator.removeAttribute('aria-current');
      indicator.classList.toggle('is-complete',i<current);
    });
    form.querySelector('[data-step-progress]').value=current+1;
    form.querySelector('[data-step-label]').textContent='Paso '+(current+1)+' de '+steps.length;
    back.hidden=current===0;next.hidden=current===steps.length-1;
    if(current===steps.length-1)updateReview();
    if(focus) {
      steps[current].querySelector('legend').focus({preventScroll:true});
      steps[current].scrollIntoView({behavior:motionAllowed()?'smooth':'auto',block:'start'});
      if(motionAllowed() && steps[current].animate)steps[current].animate([{opacity:.6,transform:'translateY(10px)'},{opacity:1,transform:'translateY(0)'}],{duration:240,easing:'ease-out'});
    }
  }
  function setFieldError(field,message) {
    field.id ||= 'prepare-'+field.name;
    const id=field.id+'-error';
    let error=document.getElementById(id);
    if(!error) {
      error=document.createElement('p');error.id=id;error.className='field-error';
      const group=field.closest('.form-field');
      if(group)group.append(error);else field.closest('label').after(error);
    }
    error.textContent=message;error.hidden=false;field.setAttribute('aria-invalid','true');
    const ids=new Set((field.getAttribute('aria-describedby')||'').split(' ').filter(Boolean));ids.add(id);
    field.setAttribute('aria-describedby',[...ids].join(' '));
  }
  function clearFieldError(field) {
    field.removeAttribute('aria-invalid');
    const error=document.getElementById(field.id+'-error');
    if(error){error.textContent='';error.hidden=true;}
  }
  function constraintMessage(field) {
    if(field.validity.valueMissing)return field.type==='checkbox'?'Lee la información de privacidad y marca esta casilla.':'Completa este campo para continuar.';
    if(field.validity.typeMismatch)return 'Escribe un correo válido, como nombre@ejemplo.com.';
    if(field.validity.badInput||field.validity.rangeUnderflow||field.validity.rangeOverflow)return 'Indica una edad entre 18 y 120 años.';
    if(field.validity.stepMismatch)return 'Indica tu edad en años completos.';
    if(field.validity.tooLong)return 'Reduce la respuesta al máximo indicado.';
    if(field.name==='telefono' && (!/^\+?[0-9 ()-]+$/.test(field.value.trim())||field.value.replace(/\D/g,'').length<7||field.value.replace(/\D/g,'').length>15))return 'Incluye entre 7 y 15 dígitos; puedes usar el prefijo de tu país.';
    return '';
  }
  function validate(elements) {
    let first=null;
    elements.forEach(field=>{
      clearFieldError(field);
      const message=constraintMessage(field);
      if(message){setFieldError(field,message);first ||= field;}
    });
    if(first) {
      const index=steps.indexOf(first.closest('[data-form-step]'));if(index>=0)showStep(index);
      notice.textContent='Revisa los campos señalados. Tus respuestas siguen aquí.';notice.hidden=false;first.focus();
      return false;
    }
    notice.hidden=true;return true;
  }
  next.addEventListener('click',()=>{if(validate([...steps[current].querySelectorAll('input,textarea')]))showStep(current+1,true);});
  back.addEventListener('click',()=>showStep(current-1,true));
  function invalidatePdf() {
    ready.hidden=true;
    if(objectUrl){URL.revokeObjectURL(objectUrl);objectUrl=null;}
    download.removeAttribute('href');status.textContent='';
  }
  const updateCounters = () => {
    form.querySelectorAll('[data-count-for]').forEach(counter=>{
      const field=document.getElementById(counter.dataset.countFor);counter.textContent=field.value.length+' / '+field.maxLength;
    });
  };
  form.addEventListener('input',event=>{
    if(!event.target.name || event.target.name==='_csrf')return;
    clearFieldError(event.target);updateCounters();invalidatePdf();
    clearTimeout(timer);if(draft.checked)timer=setTimeout(saveDraft,250);
  });
  updateCounters();
  const firstServerError=form.querySelector('[aria-invalid=true]');
  showStep(firstServerError?Math.max(0,steps.indexOf(firstServerError.closest('[data-form-step]'))):0);
  form.addEventListener('submit',async event=>{
    event.preventDefault();
    if(busy || !validate([...fields,form.elements.namedItem('acepta_privacidad')]))return;
    invalidatePdf();
    const data=new FormData(form);
    busy=true;form.setAttribute('aria-busy','true');submit.disabled=true;
    next.disabled=back.disabled=true;
    steps.forEach(step=>{step.disabled=true;});
    status.textContent='Preparando tu documento con cuidado…';
    try {
      const response=await fetch(form.action,{method:'POST',body:data,credentials:'same-origin',cache:'no-store',headers:{Accept:'application/pdf, application/json'}});
      if(!response.ok || !response.headers.get('content-type')?.includes('application/pdf')) {
        const problem=response.headers.get('content-type')?.includes('application/json')?await response.json():{};
        const errors=problem.errors||{};
        Object.entries(errors).forEach(([name,message])=>{const field=form.elements.namedItem(name);if(field instanceof HTMLElement)setFieldError(field,String(message));});
        const invalid=form.querySelector('[aria-invalid=true]');
        if(invalid){showStep(Math.max(0,steps.indexOf(invalid.closest('[data-form-step]'))));}
        throw Object.assign(new Error(response.status===419?'Tu sesión venció. Guarda un borrador y actualiza la página para continuar.':problem.message||'No pudimos preparar el documento. Tus respuestas siguen aquí; inténtalo de nuevo.'),{publicMessage:true});
      }
      const blob=await response.blob();
      objectUrl=URL.createObjectURL(blob);download.href=objectUrl;
      const filename=response.headers.get('content-disposition')?.match(/filename="([a-z0-9-]+\.pdf)"/i)?.[1];
      download.download=filename||'huella-serena-ficha-adopcion.pdf';
      discardDraft('El borrador se eliminó al preparar el PDF. Tus respuestas siguen en este formulario.');
      ready.hidden=false;ready.tabIndex=-1;ready.focus({preventScroll:true});
      ready.scrollIntoView({behavior:motionAllowed()?'smooth':'auto',block:'start'});
      status.textContent='Tu ficha está lista para descargar y compartir por tu cuenta.';
    } catch(error) {notice.textContent=error.publicMessage?error.message:'No pudimos conectar para preparar el PDF. Tus respuestas siguen aquí; inténtalo de nuevo.';notice.hidden=false;status.textContent='El archivo todavía no se ha preparado.';}
    finally {busy=false;form.removeAttribute('aria-busy');submit.disabled=next.disabled=back.disabled=false;steps.forEach(step=>{step.disabled=false;}); const invalid=form.querySelector('[aria-invalid=true]');if(!notice.hidden&&invalid)invalid.focus();}
  });
  addEventListener('pagehide',event=>{if(!event.persisted && objectUrl)URL.revokeObjectURL(objectUrl);});
});
