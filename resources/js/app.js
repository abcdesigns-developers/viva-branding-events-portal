import './bootstrap';

import Alpine from 'alpinejs';

// jQuery, DataTables and toastr for the admin pages (Roles list, forms).
// Blade views use them from inline <script type="module"> blocks, which run after this file.
import jQuery from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import toastr from 'toastr';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';

window.$ = window.jQuery = jQuery;
window.toastr = toastr;

// DataTables 3 no longer attaches to jQuery on its own; this enables $(...).DataTable().
DataTable.use(jQuery);

// Excel and PDF export libraries are large (about 1.8 MB). They load only when someone
// clicks Excel or PDF, so every other page opens fast. The placeholders below make
// DataTables show the buttons; the real libraries replace them on the first click.
DataTable.Buttons.jszip(function JSZipPlaceholder() {});
DataTable.Buttons.pdfMake({});

const loadOnFirstClick = (buttonName, load) => {
    const definition = DataTable.ext.buttons[buttonName];
    const exportNow = definition.action;
    let ready = false;
    definition.action = function (...args) {
        if (ready) return exportNow.apply(this, args);
        const button = this;
        load().then(() => {
            ready = true;
            exportNow.apply(button, args);
        }).catch(() => toastr.error('Could not load the export tool. Please try again.'));
    };
};

loadOnFirstClick('excelHtml5', async () => {
    const { default: JSZip } = await import('jszip');
    DataTable.Buttons.jszip(JSZip);
});

loadOnFirstClick('pdfHtml5', async () => {
    const [{ default: pdfMake }, { default: pdfFonts }] = await Promise.all([
        import('pdfmake/build/pdfmake'),
        import('pdfmake/build/vfs_fonts'),
    ]);
    pdfMake.addVirtualFileSystem(pdfFonts);
    DataTable.Buttons.pdfMake(pdfMake);
});

// Date fields: <input class="date-picker"> shows dd-mm-yyyy (e.g. 12-12-2026) to the user
// and sends yyyy-mm-dd to the server. Any date can be chosen, past dates too.
window.flatpickr = flatpickr;
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input.date-picker').forEach((input) => {
        flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd-m-Y',
            allowInput: true,
        });
    });
    // Time fields: <input class="time-picker"> shows 03:30 PM and sends 15:30.
    document.querySelectorAll('input.time-picker').forEach((input) => {
        flatpickr(input, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            altInput: true,
            altFormat: 'h:i K',
            allowInput: true,
        });
    });
});


// ---------------------------------------------------------------------------
// Look and feel: top progress bar, count-up numbers, command palette, charts.
// Every animation is skipped when the user asks for reduced motion.
// ---------------------------------------------------------------------------
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Thin progress bar: runs on link clicks, form submits and ajax calls.
const progressBar = () => document.getElementById('topbar-progress');
const progressStart = () => progressBar()?.classList.add('is-running');
const progressDone = () => {
    const bar = progressBar();
    if (! bar) return;
    bar.classList.remove('is-running');
    bar.style.transform = 'scaleX(1)';
    bar.style.opacity = '0';
    setTimeout(() => { bar.style.transform = ''; bar.style.opacity = ''; }, 350);
};
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (! link || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
    const href = link.getAttribute('href') || '';
    if (link.target === '_blank' || link.hasAttribute('download') || href.startsWith('#') || href.startsWith('javascript') || link.origin !== location.origin) return;
    progressStart();
});
document.addEventListener('submit', (e) => {
    // Ajax forms call preventDefault in their own handlers, which run after this one.
    setTimeout(() => { if (! e.defaultPrevented) progressStart(); }, 0);
});
window.addEventListener('pageshow', () => progressBar()?.classList.remove('is-running'));
jQuery(document).on('ajaxStart', progressStart).on('ajaxStop', progressDone);

// Numbers with data-count="123" count up from zero when the page opens.
const countUp = (el) => {
    const target = parseFloat(el.dataset.count || '0');
    if (reduceMotion || ! Number.isFinite(target) || target === 0) { el.textContent = (target || 0).toLocaleString('en-IN'); return; }
    const duration = 900;
    const started = performance.now();
    const tick = (now) => {
        const t = Math.min((now - started) / duration, 1);
        const eased = 1 - Math.pow(1 - t, 3);
        el.textContent = Math.round(target * eased).toLocaleString('en-IN');
        if (t < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
};
document.addEventListener('DOMContentLoaded', () => document.querySelectorAll('[data-count]').forEach(countUp));

// Command palette (Ctrl+K): jumps to any menu link the user can open.
Alpine.data('palette', () => ({
    open: false,
    q: '',
    items: [],
    index: 0,
    init() {
        this.items = [...document.querySelectorAll('[data-nav]')].map((a) => ({
            label: a.dataset.nav,
            group: a.dataset.group || '',
            href: a.getAttribute('href'),
        }));
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); this.show(); }
        });
    },
    show() { this.open = true; this.q = ''; this.index = 0; this.$nextTick(() => this.$refs.search?.focus()); },
    get results() {
        const q = this.q.trim().toLowerCase();
        return q ? this.items.filter((i) => (i.label + ' ' + i.group).toLowerCase().includes(q)) : this.items;
    },
    go() { const hit = this.results[this.index]; if (hit) { progressStart(); window.location.href = hit.href; } },
    move(step) { const n = this.results.length; if (n) this.index = (this.index + step + n) % n; },
}));

// Dashboard charts load on demand so other pages stay light.
window.initDashboardCharts = async (cfg) => {
    const { default: Chart } = await import('chart.js/auto');
    Chart.defaults.font.family = '"Inter Variable", Inter, system-ui, sans-serif';
    Chart.defaults.color = '#64748b';
    Chart.defaults.animation = reduceMotion ? false : { duration: 900, easing: 'easeOutQuart' };

    const doughnut = document.getElementById('chart-modules');
    if (doughnut && cfg.modules?.data?.some((n) => n > 0)) {
        new Chart(doughnut, {
            type: 'doughnut',
            data: { labels: cfg.modules.labels, datasets: [{ data: cfg.modules.data, backgroundColor: cfg.modules.colors, borderWidth: 3, borderColor: '#fff', hoverOffset: 6 }] },
            options: { cutout: '68%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 14 } } } },
        });
    }

    const line = document.getElementById('chart-weekly');
    if (line) {
        const ctx = line.getContext('2d');
        const fill = ctx.createLinearGradient(0, 0, 0, 240);
        fill.addColorStop(0, 'rgba(28,156,216,0.30)');
        fill.addColorStop(1, 'rgba(28,156,216,0)');
        new Chart(line, {
            type: 'line',
            data: { labels: cfg.weekly.labels, datasets: [{ label: 'Requests', data: cfg.weekly.data, borderColor: '#1c9cd8', backgroundColor: fill, fill: true, tension: 0.38, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5, pointBackgroundColor: '#1c9cd8' }] },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false }, border: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 }, border: { display: false }, grid: { color: '#eef2f6' } } },
            },
        });
    }
};

// Section 3 and 4 step buttons (resources/views/Admin/partials/flow.blade.php).
Alpine.data('flowPanel', (token) => ({
    step: null, busy: false, error: '',
    open(step) { this.step = step; this.error = ''; },
    async submit(event) {
        this.busy = true; this.error = '';
        try {
            const response = await fetch(this.step.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
                body: new FormData(event.target),
            });
            const json = await response.json();
            if (response.ok) { window.location.reload(); return; }
            this.error = json.errors ? Object.values(json.errors).flat()[0] : (json.message || 'Could not save.');
        } catch (e) { this.error = 'Network problem, please try again.'; }
        this.busy = false;
    },
}));

// Stock screen: add or correct stock (resources/views/Admin/Stock/index.blade.php).
Alpine.data('stockForm', (url, token) => ({
    name: '', busy: false, error: '',
    form: { item_type: 'gift', item_id: null, type: 'in', quantity: null, note: '', unit_cost: '' },
    pick(type, id, name) { this.form = { item_type: type, item_id: id, type: 'in', quantity: null, note: '', unit_cost: '' }; this.name = name; this.error = ''; },
    async submit() {
        this.busy = true; this.error = '';
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(this.form),
            });
            const json = await response.json();
            if (response.ok) { window.location.reload(); return; }
            this.error = json.errors ? Object.values(json.errors).flat()[0] : (json.message || 'Could not save.');
        } catch (e) { this.error = 'Network problem, please try again.'; }
        this.busy = false;
    },
}));

window.Alpine = Alpine;

Alpine.start();
