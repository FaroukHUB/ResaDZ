// app.js — Code revu, fusionné et propre

console.log("MB CARS DZ JS ✅");

// ===============================================
// 1. FONCTIONS ET UTILITAIRES
// ===============================================

function px(n){ return Number(String(n||"0").replace("px",""))||0; }

// Gestion des sliders de véhicules (rangées de cartes)
function initSliders(){
  const rows = document.querySelectorAll(".veh-row");
  rows.forEach((row) => {
    const track = row.querySelector(".cards-track");
    if(!track) return;

    const getStep = () => {
      const card = track.querySelector(".card-veh");
      const gap  = px(getComputedStyle(track).gap);
      return Math.ceil((card?.getBoundingClientRect().width || row.clientWidth*0.8) + gap);
    };

    const go = (dir) => track.scrollBy({ left: dir * getStep(), behavior: "smooth" });

    row.querySelector(".nav-arrow.left")  ?.addEventListener("click", () => go(-1));
    row.querySelector(".nav-arrow.right") ?.addEventListener("click", () => go(+1));

    const head = document.querySelector(`.sec-head[data-target="#${row.id}"]`);
    if(head){
      head.querySelector(".sec-arrow.left")  ?.addEventListener("click", () => go(-1));
      head.querySelector(".sec-arrow.right") ?.addEventListener("click", () => go(+1));
    }
  });
}

// Gestion du slider principal (carrousel en haut de page)
const initializeHeroSlider = () => {
  const slides = document.querySelectorAll('.carousel-slide');
  if (slides.length === 0) return;

  let currentSlideIndex = 0;
  const totalSlides = slides.length;
  const intervalTime = 5000;

  const nextSlide = () => {
    slides[currentSlideIndex].classList.remove('active');
    currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
    slides[currentSlideIndex].classList.add('active');
  };

  setInterval(nextSlide, intervalTime);
};


// ===============================================
// 2. FORMULAIRE AÉROPORT (heures + WhatsApp + envoi)
// ===============================================
(() => {
  const form = document.getElementById("airportForm");
  const wa = document.getElementById('waLink');
  const to = '+213656697788';
  const encode = s => encodeURIComponent(s || '');

  const timeSel = document.getElementById("airportTime") || form?.querySelector('select[name="time"]');
  if (timeSel && !timeSel.options.length) {
    for (let h = 0; h < 24; h++) {
      for (let m of [0, 30]) {
        const v = `${String(h).padStart(2,'0')}:${m === 0 ? "00" : "30"}`;
        const o = document.createElement("option");
        o.value = o.textContent = v;
        timeSel.appendChild(o);
      }
    }
  }

  const buildWa = () => {
    if (!form || !wa) return;
    const data = new FormData(form);
    const msg =
`Bonjour, je souhaite une voiture à l'aéroport.
Nom: ${data.get('name')}
Tél: ${data.get('phone')}
Email: ${data.get('email')}
Date/Heure: ${data.get('date')} ${data.get('time')}
Vol: ${data.get('flight') || '-'}
Modèle: ${data.get('car')}
Lieu: ${data.get('place')}
Message: ${data.get('msg') || '-'}`;
    wa.href = `https://wa.me/${to.replace(/\D/g,'')}?text=${encode(msg)}`;
  };

  form?.addEventListener('input', buildWa);
  form?.addEventListener('change', buildWa);
  buildWa();

  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.checkValidity()) { form.reportValidity(); return; }

    const fd = new FormData(form);
    fd.append('source', 'form-aeroport');

    fetch("send.php", { method: "POST", body: fd })
      .then(r => r.text())
      .then(t => {
        if (t.trim() === "OK") {
          alert("Demande envoyée ✅");
          form.reset();
          buildWa();
        } else {
          alert("Erreur d’envoi ❌");
        }
      })
      .catch(() => alert("Erreur réseau ❌"));
  });
})();


// ===============================================
// 3. BLOC DOMContentLoaded (UI générale + NAV MOBILE)
// ===============================================
document.addEventListener("DOMContentLoaded", () => {

  // --- Sliders + Hero ---
  initSliders();
  initializeHeroSlider();
  window.addEventListener("load", initSliders);


  // --- Dropdown "Nos véhicules" dans le FOOTER ---
  const footerHasSub = document.querySelector('.footer .nav-has-submenu');
  const footerTrigger = document.querySelector('.footer .nav-has-submenu > a');

  if (footerHasSub && footerTrigger) {
    footerTrigger.addEventListener('click', (e) => {
      e.preventDefault();
      footerHasSub.classList.toggle('open');
    });
  }


  // =========================================
  // [NAV MOBILE] HAMBURGER + PANEL
  // =========================================
  const burger      = document.getElementById('hamburger');
  const mobilePanel = document.getElementById('mobilePanel');

  if (burger && mobilePanel) {
    const openPanel = () => {
      mobilePanel.classList.add('open');
      document.body.classList.add('no-scroll'); // .no-scroll { overflow:hidden; } en CSS
      burger.setAttribute('aria-expanded', 'true');
    };

    const closePanel = () => {
      mobilePanel.classList.remove('open');
      document.body.classList.remove('no-scroll');
      burger.setAttribute('aria-expanded', 'false');
    };

    const togglePanel = () => {
      if (mobilePanel.classList.contains('open')) {
        closePanel();
      } else {
        openPanel();
      }
    };

    burger.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      togglePanel();
    });

    // Ferme si clic sur l’overlay (fond sombre)
    mobilePanel.addEventListener('click', (e) => {
      if (e.target === mobilePanel) closePanel();
    });

    // Escape ferme
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closePanel();
    });
  }


  // =========================================
  // [NAV MOBILE] Sous-menus (Nos véhicules / Rechercher par marque)
  // =========================================
  document.querySelectorAll('.mobile-panel .has-sub > .nav-parent').forEach((trigger) => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const li = trigger.closest('.has-sub');
      if (!li) return;
      li.classList.toggle('open');
    });
  });


  // =========================================
  // [NAV MOBILE] Sélecteur de téléphone
  // =========================================
  const phoneSelect = document.getElementById('phoneSelect');
  const btnCall     = document.getElementById('btnCall');
  const btnWa       = document.getElementById('btnWa');

  function updatePhoneButtons() {
    if (!phoneSelect || !btnCall || !btnWa) return;
    const val = phoneSelect.value.replace(/\s+/g, '');
    btnCall.href = 'tel:' + val;
    btnWa.href   = 'https://wa.me/' + val.replace('+', '');
  }

  if (phoneSelect) {
    phoneSelect.addEventListener('change', updatePhoneButtons);
    updatePhoneButtons();
  }


  // --- LANGUE MOBILE ---
  const btnLangMobile = document.getElementById('btnLangMobile');
  const langDropMobile = document.getElementById('langDropMobile');
  if(btnLangMobile && langDropMobile){
    const openLang = () => { langDropMobile.classList.add('open'); btnLangMobile.setAttribute('aria-expanded','true'); };
    const closeLang = () => { langDropMobile.classList.remove('open'); btnLangMobile.setAttribute('aria-expanded','false'); };
    const toggleLang = (ev) => {
      ev.preventDefault();
      ev.stopPropagation();
      ev.stopImmediatePropagation?.();
      langDropMobile.classList.contains('open') ? closeLang() : openLang();
    };

    ['touchstart','click'].forEach(evt => btnLangMobile.addEventListener(evt, toggleLang, {passive:false}));

    langDropMobile.addEventListener('click', (e)=>{
      const li = e.target.closest('li[data-lang]');
      if(!li) return;
      // setLang(li.dataset.lang); // si tu as cette fonction
      closeLang();
    });

    document.addEventListener('click', (e)=>{
      if(langDropMobile.contains(e.target) || e.target === btnLangMobile) return;
      setTimeout(closeLang, 0);
    });
  }


  // --- MODAL ASK ---
  const modalAsk = document.getElementById('askModal');
  if (modalAsk) {
    const selectCar = modalAsk.querySelector('select[name="car"]');
    const dateStart = modalAsk.querySelector('#dateStart, [name="start"]');
    const dateEnd   = modalAsk.querySelector('#dateEnd, [name="end"]');

    function openModal(){
      modalAsk.setAttribute('aria-hidden','false');
      const first = modalAsk.querySelector('input,select,textarea,button,a[href]');
      first && first.focus();
    }

    const toYMD = d => {
      const y = d.getFullYear();
      const m = String(d.getMonth()+1).padStart(2,'0');
      const day = String(d.getDate()).padStart(2,'0');
      return `${y}-${m}-${day}`;
    };
    const clamp = (input, dateObj) => {
      let t = dateObj.getTime();
      const min = input.getAttribute('min');
      const max = input.getAttribute('max');
      if (min) t = Math.max(t, new Date(min+'T00:00:00').getTime());
      if (max) t = Math.min(t, new Date(max+'T00:00:00').getTime());
      return new Date(t);
    };
    const write = (input, d) => {
      input.value = toYMD(d);
      input.dispatchEvent(new Event('input', { bubbles:true }));
      input.dispatchEvent(new Event('change', { bubbles:true }));
    };

    document.addEventListener('click', (e)=>{
      const t = e.target.closest('[data-open-ask]');
      if(!t) return;
      openModal();
    });

    document.querySelectorAll('[data-ask-car]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const model = btn.getAttribute('data-ask-car') || '';

        if (selectCar) {
          let opt = [...selectCar.options].find(o => o.value === model || o.textContent.trim() === model);
          if (!opt) {
            opt = new Option(model, model, true, true);
            selectCar.add(opt);
          } else {
            selectCar.value = opt.value;
          }
        }
        if (dateStart) {
          const d = new Date(); d.setHours(12,0,0,0);
          dateStart.value = d.toISOString().slice(0,10);
        }
        if (dateEnd) {
          const d2 = new Date(); d2.setDate(d2.getDate()+3); d2.setHours(12,0,0,0);
          dateEnd.value = d2.toISOString().slice(0,10);
        }
        openModal();
      });
    });

    modalAsk.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-days], [data-add-days]');
      if (!btn) return;
      
      const delta = parseInt(btn.dataset.days || btn.dataset.addDays || '0', 10);
      let target = (document.activeElement && document.activeElement.matches('input[type="date"]') && modalAsk.contains(document.activeElement))
        ? document.activeElement
        : (dateStart?.value ? dateEnd : dateStart);

      if (!target) return;

      const base = target.value ? new Date(target.value+'T12:00:00') : new Date();
      base.setHours(12,0,0,0);

      const next = new Date(base);
      next.setDate(base.getDate() + delta);

      write(target, clamp(target, next));

      if (target === dateStart && dateEnd) {
        const dS = new Date(dateStart.value+'T12:00:00');
        const dE = dateEnd.value ? new Date(dateEnd.value+'T12:00:00') : null;
        if (!dE || dE.getTime() < dS.getTime()) write(dateEnd, dS);
      }
    });

    const modalForm = modalAsk.querySelector('form');
    const btnSubmit = modalForm?.querySelector('[type="submit"]');
    if (modalForm && btnSubmit) {
      btnSubmit.addEventListener('click', (e) => {
        e.preventDefault();
        if (!modalForm.checkValidity()) { modalForm.reportValidity(); return; }

        const fd = new FormData(modalForm);
        fd.append('source', 'modal-carte');

        fetch('send.php', { method:'POST', body:fd })
          .then(r => r.text())
          .then(t => {
            if (t.trim()==='OK') { 
              alert('Demande envoyée ✅'); 
              modalAsk.setAttribute('aria-hidden','true'); 
              modalForm.reset(); 
            } else { alert('Erreur d’envoi ❌'); }
          })
          .catch(()=> alert('Erreur réseau ❌'));
      });
    }
  }

  // --- Nettoyage lang-mobile sur Escape / click extérieur ---
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape'){
      document.querySelectorAll('.lang-mobile').forEach(el=>el.blur());
    }
  });
  document.addEventListener('click', (e)=>{
    const lm = e.target.closest('.lang-mobile');
    document.querySelectorAll('.lang-mobile').forEach(el=>{
      if(el !== lm) el.blur();
    });
  });

}); // FIN DOMContentLoaded
