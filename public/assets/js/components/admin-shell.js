const shell = document.querySelector('[data-admin-shell]');
if (shell) {
  const sidebar = shell.querySelector('#admin-sidebar');
  const toggle = shell.querySelector('[data-admin-menu]');
  const closeButton = shell.querySelector('[data-admin-close]');
  const overlay = shell.querySelector('[data-admin-overlay]');
  const main = shell.querySelector('.admin-main');
  const mobile = matchMedia('(max-width: 1024px)');
  const search = shell.querySelector('[data-nav-search]');
  const groups = [...shell.querySelectorAll('[data-nav-group]')];
  const savedGroups = new Map();
  let open = false;
  let previousFocus;
  const read = key => { try { return localStorage.getItem(key); } catch { return null; } };
  const write = (key, value) => { try { localStorage.setItem(key, value); } catch { /* Preferencia opcional. */ } };
  shell.classList.toggle('sidebar-collapsed', read('refugio.sidebar.collapsed') === 'true');

  function sync() {
    sidebar.classList.toggle('open', open && mobile.matches);
    overlay.hidden = !(mobile.matches && open);
    main.inert = mobile.matches && open;
    sidebar.inert = mobile.matches && !open;
    document.body.classList.toggle('admin-nav-open', mobile.matches && open);
    if (mobile.matches && open) {
      sidebar.setAttribute('role', 'dialog');
      sidebar.setAttribute('aria-modal', 'true');
    } else {
      sidebar.removeAttribute('role');
      sidebar.removeAttribute('aria-modal');
    }
    const expanded = mobile.matches ? open : !shell.classList.contains('sidebar-collapsed');
    toggle.setAttribute('aria-expanded', String(expanded));
    toggle.setAttribute('aria-label', mobile.matches ? (open ? 'Cerrar panel lateral' : 'Abrir panel lateral') : (expanded ? 'Contraer navegación' : 'Expandir navegación'));
  }
  function close(restore = true) {
    open = false;
    sync();
    if (restore) previousFocus?.focus();
  }
  toggle.addEventListener('click', () => {
    if (mobile.matches) {
      previousFocus = document.activeElement;
      open = !open;
      sync();
      if (open) closeButton.focus();
    } else {
      const collapsed = shell.classList.toggle('sidebar-collapsed');
      write('refugio.sidebar.collapsed', String(collapsed));
      if (collapsed) groups.forEach(group => { group.open = true; });
      sync();
    }
  });
  closeButton.addEventListener('click', () => close());
  overlay.addEventListener('click', () => close());
  mobile.addEventListener('change', () => close(false));
  sidebar.addEventListener('click', event => { if (event.target.closest('a') && mobile.matches) close(false); });
  document.addEventListener('keydown', event => {
    if (!mobile.matches || !open) return;
    if (event.key === 'Escape') { event.preventDefault(); close(); return; }
    if (event.key !== 'Tab') return;
    const focusable = [...sidebar.querySelectorAll('a[href],button,input,summary')].filter(element => !element.hidden && element.getClientRects().length);
    const first = focusable[0], last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });

  for (const group of groups) {
    const key = 'refugio.nav.' + group.querySelector('summary').textContent.trim();
    if (group.dataset.active !== 'true' && read(key) === 'false') group.open = false;
    if (shell.classList.contains('sidebar-collapsed')) group.open = true;
    group.addEventListener('toggle', () => { if (!search.value && !shell.classList.contains('sidebar-collapsed')) write(key, String(group.open)); });
  }
  const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es');
  search.addEventListener('input', () => {
    const query = normalize(search.value.trim());
    let found = 0;
    groups.forEach(group => {
      if (query && !savedGroups.has(group)) savedGroups.set(group, group.open);
      let matches = 0;
      group.querySelectorAll('[data-nav-label]').forEach(link => {
        link.hidden = !normalize(link.dataset.navLabel).includes(query);
        if (!link.hidden) matches++;
      });
      group.hidden = matches === 0;
      found += matches;
      if (query) group.open = true;
      else if (savedGroups.has(group)) { group.open = group.dataset.active === 'true' || savedGroups.get(group); savedGroups.delete(group); }
    });
    shell.querySelector('[data-nav-empty]').hidden = found > 0;
  });
  sync();
}
