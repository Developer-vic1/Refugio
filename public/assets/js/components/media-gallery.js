import { motionAllowed } from '../core/motion.js';

document.querySelectorAll('img[data-animal-media]').forEach(image => {
  image.addEventListener('error', () => { image.src='/assets/img/placeholder.svg'; }, {once:true});
});

document.querySelectorAll('[data-media-gallery]').forEach(gallery => {
  const links = [...gallery.querySelectorAll('[data-gallery-open]')];
  const photos = [...new Map(links.map(link => [Number(link.dataset.galleryOpen), {src:link.href,alt:link.querySelector('img').alt}])).values()];
  const dialog = gallery.querySelector('[data-gallery-dialog]');
  if (!dialog?.showModal || !photos.length) return;
  const image = dialog.querySelector('[data-gallery-image]');
  const previous = dialog.querySelector('[data-gallery-previous]');
  const next = dialog.querySelector('[data-gallery-next]');
  let index = 0, origin = null, overflow = '', animation;
  const render = () => {
    image.src=photos[index].src; image.alt=photos[index].alt;
    dialog.querySelector('[data-gallery-count]').textContent='Vista '+(index+1)+' de '+photos.length;
    previous.disabled=index===0; next.disabled=index===photos.length-1;
    animation?.cancel();
    if (motionAllowed() && image.animate) animation=image.animate([{opacity:.45,transform:'scale(.98)'},{opacity:1,transform:'scale(1)'}],{duration:220,easing:'ease-out'});
  };
  links.forEach(link => link.addEventListener('click', event => {
    event.preventDefault();
    index=Number(link.dataset.galleryOpen); origin=link; render();
    overflow=document.body.style.overflow; document.body.style.overflow='hidden';
    dialog.showModal(); dialog.querySelector('[data-gallery-close]').focus();
  }));
  previous.addEventListener('click', () => { if(index>0) {index--;render();} });
  next.addEventListener('click', () => { if(index<photos.length-1) {index++;render();} });
  dialog.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());
  dialog.addEventListener('keydown', event => {
    if (event.key==='ArrowLeft') {event.preventDefault();previous.click();}
    if (event.key==='ArrowRight') {event.preventDefault();next.click();}
  });
  dialog.addEventListener('click', event => {
    if (event.target!==dialog) return;
    const rect=dialog.getBoundingClientRect();
    if(event.clientX<rect.left||event.clientX>rect.right||event.clientY<rect.top||event.clientY>rect.bottom) dialog.close();
  });
  dialog.addEventListener('close', () => { document.body.style.overflow=overflow; animation?.cancel();origin?.focus(); });
  image.addEventListener('error', () => { if(!image.src.endsWith('/placeholder.svg')) image.src='/assets/img/placeholder.svg'; });
});
