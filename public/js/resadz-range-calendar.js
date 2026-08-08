/**
 * ResaDZ Range Calendar — sélection de dates façon Airbnb (vanilla JS, zéro dépendance).
 *
 * new ResadzRangeCalendar(container, {
 *   unavailable: ['YYYY-MM-DD', ...],   // dates grisées incliquables
 *   minDate: 'YYYY-MM-DD',              // défaut aujourd'hui
 *   minDays: 1,                         // durée minimum de location
 *   months: 2,                          // mois affichés (1 auto sur mobile)
 *   start: 'YYYY-MM-DD' | null,         // pré-sélection
 *   end:   'YYYY-MM-DD' | null,
 *   dark: false,                        // thème sombre (hero)
 *   onChange: function (start, end) {}  // appelé quand la plage est complète
 * })
 */
(function () {
    'use strict';

    var STYLE_ID = 'resadz-rc-style';
    var CSS = ''
        + '.rzc{font-family:inherit;user-select:none;}'
        + '.rzc-months{display:flex;gap:24px;flex-wrap:wrap;justify-content:center;}'
        + '.rzc-month{width:280px;max-width:100%;}'
        + '.rzc-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}'
        + '.rzc-title{font-weight:700;font-size:14px;text-transform:capitalize;text-align:center;flex:1;}'
        + '.rzc-nav{width:32px;height:32px;border-radius:9999px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;font-size:16px;line-height:1;color:#374151;}'
        + '.rzc-nav:disabled{opacity:.3;cursor:default;}'
        + '.rzc-nav:hover:not(:disabled){background:#f3f4f6;}'
        + '.rzc-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;}'
        + '.rzc-dow{font-size:11px;font-weight:700;color:#9ca3af;text-align:center;padding:4px 0;text-transform:uppercase;}'
        + '.rzc-day{position:relative;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border-radius:9999px;cursor:pointer;border:none;background:transparent;color:#111827;}'
        + '.rzc-day:hover:not(.rzc-off):not(.rzc-dis){box-shadow:inset 0 0 0 2px #111827;}'
        + '.rzc-off{visibility:hidden;}'
        + '.rzc-dis{color:#d1d5db;cursor:default;text-decoration:line-through;}'
        + '.rzc-start,.rzc-end{background:#111827!important;color:#fff!important;}'
        + '.rzc-in{background:#f3f4f6;border-radius:0;}'
        + '.rzc-day.rzc-in:hover{box-shadow:inset 0 0 0 2px #111827;border-radius:9999px;}'
        + '.rzc-dark .rzc-day{color:#f9fafb;}'
        + '.rzc-dark .rzc-dis{color:#4b5563;}'
        + '.rzc-dark .rzc-nav{background:#1f2937;border-color:#374151;color:#f9fafb;}'
        + '.rzc-dark .rzc-start,.rzc-dark .rzc-end{background:#16a34a!important;color:#fff!important;}'
        + '.rzc-dark .rzc-in{background:rgba(22,163,74,.25);}'
        + '.rzc-hint{margin-top:8px;font-size:12px;color:#6b7280;text-align:center;}'
        + '.rzc-legend{display:flex;gap:16px;justify-content:center;margin-top:8px;font-size:11px;color:#6b7280;}'
        + '.rzc-legend span{display:flex;align-items:center;gap:5px;}'
        + '.rzc-dot{width:10px;height:10px;border-radius:9999px;display:inline-block;}'
        + '@media(max-width:640px){.rzc-months{gap:12px;}.rzc-month{width:100%;}}';

    function injectStyle() {
        if (document.getElementById(STYLE_ID)) return;
        var st = document.createElement('style');
        st.id = STYLE_ID;
        st.textContent = CSS;
        document.head.appendChild(st);
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }
    function fmt(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
    function parse(s) { var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }
    function addDays(s, n) { var d = parse(s); d.setDate(d.getDate() + n); return fmt(d); }

    var MONTHS_FR = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    var DOW_FR = ['L','M','M','J','V','S','D'];

    function ResadzRangeCalendar(container, opts) {
        injectStyle();
        opts = opts || {};
        this.el = typeof container === 'string' ? document.querySelector(container) : container;
        this.unavailable = {};
        (opts.unavailable || []).forEach(function (d) { this.unavailable[d] = true; }.bind(this));
        var today = new Date();
        this.minDate = opts.minDate || fmt(today);
        this.minDays = opts.minDays || 1;
        this.months = window.innerWidth < 640 ? 1 : (opts.months || 2);
        this.start = opts.start || null;
        this.end = opts.end || null;
        this.dark = !!opts.dark;
        this.onChange = opts.onChange || function () {};
        var base = this.start ? parse(this.start) : new Date();
        this.viewYear = base.getFullYear();
        this.viewMonth = base.getMonth();
        this.render();
    }

    ResadzRangeCalendar.prototype.setUnavailable = function (dates) {
        this.unavailable = {};
        (dates || []).forEach(function (d) { this.unavailable[d] = true; }.bind(this));
        this.render();
    };

    ResadzRangeCalendar.prototype.isDisabled = function (dateStr) {
        if (dateStr < this.minDate) return true;
        if (this.unavailable[dateStr]) return true;
        // En cours de sélection : dates avant le départ ou au-delà de la 1re indispo interdites
        if (this.start && !this.end) {
            if (dateStr < this.start) return false; // clic = nouveau départ, autorisé
            if (dateStr === this.start) return true;
            if (dateStr > this.start && dateStr < addDays(this.start, this.minDays)) return true;
            var cur = addDays(this.start, 1);
            while (cur < dateStr) {
                if (this.unavailable[cur]) return true;
                cur = addDays(cur, 1);
            }
        }
        return false;
    };

    ResadzRangeCalendar.prototype.pick = function (dateStr) {
        if (!this.start || (this.start && this.end)) {
            this.start = dateStr; this.end = null;
        } else if (dateStr < this.start) {
            this.start = dateStr; this.end = null;
        } else {
            this.end = dateStr;
            this.onChange(this.start, this.end);
        }
        this.render();
    };

    ResadzRangeCalendar.prototype.render = function () {
        var self = this;
        this.el.innerHTML = '';
        var root = document.createElement('div');
        root.className = 'rzc' + (this.dark ? ' rzc-dark' : '');
        var monthsWrap = document.createElement('div');
        monthsWrap.className = 'rzc-months';

        for (var m = 0; m < this.months; m++) {
            var y = this.viewYear, mo = this.viewMonth + m;
            var d0 = new Date(y, mo, 1);
            y = d0.getFullYear(); mo = d0.getMonth();

            var month = document.createElement('div');
            month.className = 'rzc-month';

            var head = document.createElement('div');
            head.className = 'rzc-head';
            var prev = document.createElement('button');
            prev.type = 'button'; prev.className = 'rzc-nav'; prev.textContent = '‹';
            prev.style.visibility = m === 0 ? 'visible' : 'hidden';
            var nowKey = new Date();
            prev.disabled = (y < nowKey.getFullYear()) || (y === nowKey.getFullYear() && mo <= nowKey.getMonth());
            prev.onclick = function () { self.viewMonth--; self.render(); };
            var next = document.createElement('button');
            next.type = 'button'; next.className = 'rzc-nav'; next.textContent = '›';
            next.style.visibility = m === this.months - 1 ? 'visible' : 'hidden';
            next.onclick = function () { self.viewMonth++; self.render(); };
            var title = document.createElement('div');
            title.className = 'rzc-title';
            title.textContent = MONTHS_FR[mo] + ' ' + y;
            head.appendChild(prev); head.appendChild(title); head.appendChild(next);
            month.appendChild(head);

            var grid = document.createElement('div');
            grid.className = 'rzc-grid';
            DOW_FR.forEach(function (d) {
                var c = document.createElement('div'); c.className = 'rzc-dow'; c.textContent = d; grid.appendChild(c);
            });

            var firstDow = (new Date(y, mo, 1).getDay() + 6) % 7; // lundi = 0
            for (var i = 0; i < firstDow; i++) {
                var e = document.createElement('div'); e.className = 'rzc-day rzc-off'; grid.appendChild(e);
            }
            var daysIn = new Date(y, mo + 1, 0).getDate();
            for (var day = 1; day <= daysIn; day++) {
                var ds = y + '-' + pad(mo + 1) + '-' + pad(day);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'rzc-day';
                btn.textContent = day;
                var disabled = this.isDisabled(ds);
                if (disabled && !(this.start && !this.end && ds < this.start && ds >= this.minDate && !this.unavailable[ds])) {
                    btn.classList.add('rzc-dis');
                } else {
                    btn.dataset.date = ds;
                    btn.onclick = (function (d2) { return function () { self.pick(d2); }; })(ds);
                }
                if (this.start === ds) btn.classList.add('rzc-start');
                if (this.end === ds) btn.classList.add('rzc-end');
                if (this.start && this.end && ds > this.start && ds < this.end) btn.classList.add('rzc-in');
                grid.appendChild(btn);
            }
            month.appendChild(grid);
            monthsWrap.appendChild(month);
        }
        root.appendChild(monthsWrap);

        var hint = document.createElement('div');
        hint.className = 'rzc-hint';
        if (!this.start) hint.textContent = 'Sélectionnez votre date de départ';
        else if (!this.end) hint.textContent = 'Sélectionnez votre date de retour';
        else {
            var n = Math.round((parse(this.end) - parse(this.start)) / 86400000);
            hint.textContent = 'Du ' + this.start.split('-').reverse().join('/') + ' au ' + this.end.split('-').reverse().join('/') + ' — ' + n + ' jour' + (n > 1 ? 's' : '');
        }
        root.appendChild(hint);

        var legend = document.createElement('div');
        legend.className = 'rzc-legend';
        legend.innerHTML = '<span><span class="rzc-dot" style="background:' + (this.dark ? '#16a34a' : '#111827') + ';"></span>Sélection</span>'
            + '<span><span class="rzc-dot" style="background:#d1d5db;"></span>Indisponible</span>';
        root.appendChild(legend);

        this.el.appendChild(root);
    };

    window.ResadzRangeCalendar = ResadzRangeCalendar;
})();
