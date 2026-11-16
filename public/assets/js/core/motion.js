const system = matchMedia('(prefers-reduced-motion: reduce)');
const fine = matchMedia('(hover: hover) and (pointer: fine)');
const read = key => { try { return localStorage.getItem(key); } catch { return null; } };
const write = (key,value) => { try { localStorage.setItem(key,value); } catch { /* Preferencia opcional. */ } };
let reduced = read('refugio.motion') === 'reduce';
let cursor = read('refugio.cursor') === 'true';
export const motionAllowed = () => !system.matches && !reduced;
export const cursorAllowed = () => motionAllowed() && fine.matches && cursor;
export const systemReduced = () => system.matches;
export const finePointer = () => fine.matches;
export const wantsReduced = () => reduced;
export const wantsCursor = () => cursor;
function publish() {
  document.documentElement.dataset.userMotion = motionAllowed() ? 'full' : 'reduced';
  document.dispatchEvent(new Event('refugio:motion-preference'));
}
export function setReduced(value) { reduced = value; write('refugio.motion',value ? 'reduce' : 'full'); publish(); }
export function setCursor(value) { cursor = value; write('refugio.cursor',String(value)); publish(); }
system.addEventListener('change',publish);
fine.addEventListener('change',publish);
publish();
