(() => {
  const doc = document;

  const qs = (sel, root = doc) => root.querySelector(sel);
  const qsa = (sel, root = doc) => [...root.querySelectorAll(sel)];

  const base = typeof window.WMB_BASE === 'string' ? window.WMB_BASE : '';

  function apiUrl(path, query = '') {
    const clean = String(path).replace(/^\//, '').replace(/\.php$/i, '');
    const url = `${base}/${clean}`;
    return query ? `${url}?${query}` : url;
  }

  function sectionIdFromPath() {
    const path = window.location.pathname.replace(base, '').replace(/^\/+|\/+$/g, '');
    if (!path || path === 'index') return 'home';
    return path;
  }

  function setActiveNav(id) {
    qsa('.main-nav a[data-section]').forEach((link) => {
      const active = link.getAttribute('data-section') === id
        || (id === 'home' && link.getAttribute('data-section') === 'home');
      link.classList.toggle('is-active', active);
    });
  }

  function scrollToSection(id, replaceUrl = false) {
    if (!id) return;
    const target = doc.getElementById(id);
    if (!target) return;
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    setActiveNav(id === 'over-fgwmb-detail' ? 'over-fgwmb' : id);
    if (replaceUrl) {
      const next = id === 'home' ? `${base}/` : `${base}/${id}`;
      history.replaceState(null, '', next);
    }
  }

  function initSectionRouting() {
    const initial = sectionIdFromPath();
    setActiveNav(initial === 'over-fgwmb-detail' ? 'over-fgwmb' : initial || 'home');
    if (initial && initial !== 'home') {
      window.requestAnimationFrame(() => scrollToSection(initial, false));
    }

    if (window.location.hash) {
      const hashId = window.location.hash.replace(/^#/, '');
      if (hashId) {
        window.requestAnimationFrame(() => scrollToSection(hashId, true));
      }
    }

    qsa('a[data-section]').forEach((link) => {
      link.addEventListener('click', (event) => {
        const id = link.getAttribute('data-section');
        if (!id || !doc.getElementById(id)) return;
        event.preventDefault();
        scrollToSection(id, true);
        const menuButton = qs('.menu-toggle');
        const navigation = qs('.main-nav');
        if (menuButton && navigation) {
          menuButton.setAttribute('aria-expanded', 'false');
          navigation.classList.remove('open');
        }
      });
    });
  }

  function initMenu() {
    const menuButton = qs('.menu-toggle');
    const navigation = qs('.main-nav');
    if (!menuButton || !navigation) return;

    menuButton.addEventListener('click', () => {
      const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
      menuButton.setAttribute('aria-expanded', String(!isOpen));
      navigation.classList.toggle('open', !isOpen);
    });
  }

  function initImageProtection() {
    qsa('[data-protect-images]').forEach((wrap) => {
      wrap.addEventListener('contextmenu', (e) => e.preventDefault());
      wrap.addEventListener('dragstart', (e) => e.preventDefault());
    });
  }

  function showAlert(type, message) {
    const alert = qs('#contact-alert');
    if (!alert) return;
    alert.hidden = !message;
    alert.className = `contact-alert${type ? ` contact-alert--${type}` : ''}`;
    alert.textContent = message || '';
  }

  function renderSeals(seals) {
    const wrap = qs('[data-zegels]');
    if (!wrap || !Array.isArray(seals)) return;
    wrap.innerHTML = '';
    seals.forEach((seal) => {
      const label = doc.createElement('label');
      label.className = 'contact-zegel';
      const input = doc.createElement('input');
      input.type = 'radio';
      input.name = 'zegel';
      input.value = seal.token;
      input.required = true;
      const img = doc.createElement('img');
      img.src = seal.src;
      img.alt = '';
      img.width = 120;
      img.height = 120;
      img.draggable = false;
      label.append(input, img);
      wrap.appendChild(label);
    });
  }

  async function loadSeals(refresh = false) {
    const wrap = qs('[data-zegels]');
    const loading = qs('[data-captcha-loading]');
    if (!wrap) return;
    wrap.innerHTML = '';
    if (loading) loading.hidden = false;

    try {
      const url = refresh ? apiUrl('api/challenge', 'refresh=1') : apiUrl('api/challenge');
      const res = await fetch(url, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
      });
      const data = await res.json();
      if (!data.ok || !Array.isArray(data.seals)) throw new Error('bad');
      if (loading) loading.hidden = true;
      renderSeals(data.seals);
    } catch {
      if (loading) loading.hidden = true;
      showAlert('error', 'De zegels konden niet worden geladen. Vernieuw de pagina.');
    }
  }

  function setIntent(intent) {
    const value = intent === 'kennismaking' ? 'kennismaking' : 'vraag';
    const intentField = qs('#contact-intent');
    const subjectField = qs('#contact-subject');
    const intro = qs('[data-contact-intro]');

    if (intentField) intentField.value = value;

    if (subjectField && subjectField.tagName === 'SELECT') {
      subjectField.value = value === 'kennismaking'
        ? 'Kennismaking met Fotogroep Windmolenbroek'
        : 'Algemene vraag';
    } else if (subjectField) {
      subjectField.value = value === 'kennismaking'
        ? 'Kennismaking met Fotogroep Windmolenbroek'
        : 'Algemene vraag';
    }

    if (intro) {
      intro.textContent = value === 'kennismaking'
        ? 'Laat een bericht achter. We nemen contact met je op om een bezoek af te spreken.'
        : 'Laat een bericht achter. We nemen contact met je op om je vraag te beantwoorden.';
    }

    qsa('[data-contact-tab]').forEach((tab) => {
      const active = tab.getAttribute('data-intent') === value;
      tab.classList.toggle('is-active', active);
      tab.setAttribute('aria-selected', String(active));
    });
  }

  function submitButtonHtml() {
    return 'Bericht versturen <img class="icon" src="' + `${base}/assets/icons/arrow-up-right.svg` + '" alt="" width="18" height="18" aria-hidden="true">';
  }

  function initContact() {
    const form = qs('#contact-form');
    if (!form) return;
    const submit = qs('[data-submit]', form);

    qsa('[data-contact-tab]').forEach((tab) => {
      tab.addEventListener('click', () => {
        setIntent(tab.getAttribute('data-intent') || 'vraag');
      });
    });

    qsa('[data-scroll-form]').forEach((btn) => {
      btn.addEventListener('click', () => {
        setIntent(btn.getAttribute('data-intent') || 'vraag');
        scrollToSection('contact', true);
        window.setTimeout(() => {
          qs('#contact-formulier')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
          qs('input[name="name"]', form)?.focus();
        }, 50);
      });
    });

    setIntent('kennismaking');
    loadSeals();

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const chosen = form.querySelector('input[name="zegel"]:checked');
      if (!chosen) {
        showAlert('error', 'Kies eerst het juiste zegel.');
        return;
      }

      const fd = new FormData(form);
      if (submit) {
        submit.disabled = true;
        submit.textContent = 'Bezig met versturen…';
      }
      showAlert('', '');

      try {
        const res = await fetch(apiUrl('api/contact'), {
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
          headers: { Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.ok) {
          showAlert('success', data.message || 'Bedankt! Je bericht is verstuurd.');
          form.reset();
          setIntent('kennismaking');
          await loadSeals(true);
        } else {
          showAlert('error', data.message || 'Er ging iets mis.');
          if (data.seals) {
            renderSeals(data.seals);
          } else {
            await loadSeals(true);
          }
        }
      } catch {
        showAlert('error', 'Er ging iets mis bij het versturen. Probeer het later nog eens.');
        await loadSeals(true);
      } finally {
        if (submit) {
          submit.disabled = false;
          submit.innerHTML = submitButtonHtml();
        }
      }
    });
  }

  function initHeaderScroll() {
    const header = qs('[data-site-header]');
    if (!header) return;
    const onScroll = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 24);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  function initBackToTop() {
    const btn = qs('[data-back-to-top]');
    if (!btn) return;
    const onScroll = () => {
      btn.hidden = window.scrollY <= 640;
    };
    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
      history.replaceState(null, '', `${base}/`);
      setActiveNav('home');
    });
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  function initReveal() {
    const items = qsa('.section, .contact');
    if (!items.length || !('IntersectionObserver' in window)) return;
    items.forEach((el) => el.setAttribute('data-reveal', ''));
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    items.forEach((el) => io.observe(el));
  }

  initMenu();
  initSectionRouting();
  initImageProtection();
  initContact();
  initHeaderScroll();
  initBackToTop();
  initReveal();
})();
