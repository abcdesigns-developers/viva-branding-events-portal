import './bootstrap';

import Alpine from 'alpinejs';

// jQuery, DataTables and toastr for the admin pages (Roles list, forms).
// Blade views use them from inline <script type="module"> blocks, which run after this file.
import jQuery from 'jquery';
import JSZip from 'jszip';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
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

pdfMake.addVirtualFileSystem(pdfFonts);
DataTable.Buttons.jszip(JSZip);
DataTable.Buttons.pdfMake(pdfMake);

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

window.Alpine = Alpine;

Alpine.start();
