{{-- Confirm Modal: used instead of the browser's confirm() box.
     JS: if (await confirmBox('Delete this?', {okText: 'Delete', danger: true})) { ... } --}}
<div id="confirmModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl animate-pop">
        <div class="flex items-start gap-4 px-6 pt-6">
            <span id="confirmModalIcon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                <i class="ti ti-help-circle text-2xl"></i>
            </span>
            <div class="min-w-0 pt-0.5">
                <h4 id="confirmModalTitle" class="text-base font-semibold text-slate-900">Please confirm</h4>
                <p id="confirmModalText" class="mt-1 whitespace-pre-line text-sm text-slate-600"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3 rounded-b-2xl border-t border-slate-100 bg-slate-50 px-6 py-3">
            <button type="button" id="confirmModalCancel" class="btn btn-secondary">Cancel</button>
            <button type="button" id="confirmModalOk" class="btn btn-primary">Yes, continue</button>
        </div>
    </div>
</div>

<script>
    // confirmBox(message, {title, okText, danger}) -> Promise<boolean>
    window.confirmBox = function (message, options = {}) {
        const modal = document.getElementById('confirmModal');
        const ok = document.getElementById('confirmModalOk');
        const cancel = document.getElementById('confirmModalCancel');
        const icon = document.getElementById('confirmModalIcon');
        const danger = !! options.danger;

        document.getElementById('confirmModalTitle').textContent = options.title || (danger ? 'Are you sure?' : 'Please confirm');
        document.getElementById('confirmModalText').textContent = message;
        ok.textContent = options.okText || (danger ? 'Yes, delete' : 'Yes, continue');
        ok.className = 'btn btn-primary ' + (danger ? '!bg-rose-600 hover:!bg-rose-700' : '');
        icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full ' + (danger ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600');
        icon.innerHTML = '<i class="ti ' + (danger ? 'ti-alert-triangle' : 'ti-help-circle') + ' text-2xl"></i>';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        ok.focus();

        return new Promise(function (resolve) {
            function close(answer) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                ok.removeEventListener('click', yes);
                cancel.removeEventListener('click', no);
                modal.removeEventListener('click', outside);
                document.removeEventListener('keydown', keys);
                resolve(answer);
            }
            function yes() { close(true); }
            function no() { close(false); }
            function outside(e) { if (e.target === modal) close(false); }
            function keys(e) { if (e.key === 'Escape') close(false); }
            ok.addEventListener('click', yes);
            cancel.addEventListener('click', no);
            modal.addEventListener('click', outside);
            document.addEventListener('keydown', keys);
        });
    };

    // Plain forms: <form data-confirm="Remove your photo?"> ask in the modal before submitting.
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (! form.matches('form[data-confirm]') || form.dataset.confirmed) return;
        e.preventDefault();
        confirmBox(form.dataset.confirm, {danger: form.dataset.danger !== undefined}).then(function (yes) {
            if (! yes) return;
            form.dataset.confirmed = '1';
            form.requestSubmit ? form.requestSubmit() : form.submit();
        });
    }, true);
</script>

{{-- Show Entity Modal: links with class "modal-one-btn" load /admin/{entity}/{route-key} here. --}}
<div id="modalOne" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="w-full max-w-3xl max-h-[90vh] flex flex-col rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
            <h4 class="text-lg font-semibold text-slate-900" id="modalOneTitle"></h4>
            <button type="button" class="modal-one-close rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <i class="ti ti-x text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto px-6 py-4" id="modalOneBody"></div>
        <div class="flex justify-end border-t border-slate-200 px-6 py-3">
            <button type="button" class="modal-one-close btn btn-secondary">Close</button>
        </div>
    </div>
</div>

<script type="module">
    const modal = $('#modalOne');
    const closeModal = () => modal.addClass('hidden').removeClass('flex');

    $(document).on('click', '.modal-one-btn', function (e) {
        e.preventDefault();
        const entity = $(this).data('entity');
        const title = $(this).data('title');
        const routeKey = $(this).data('route-key');
        $.ajax({
            type: 'GET',
            url: '/admin/' + entity + '/' + routeKey,
            cache: false,
            success: function (data) {
                $('#modalOneTitle').html(title);
                $('#modalOneBody').html(data);
                modal.removeClass('hidden').addClass('flex');
            },
            error: function () {
                toastr.error('There is some problem. Please try again', '', {
                    showMethod: 'slideDown',
                    hideMethod: 'slideUp',
                    timeOut: 1500,
                    closeButton: true,
                });
            }
        });
    });

    $(document).on('click', '.modal-one-close', closeModal);
    modal.on('click', function (e) { if (e.target === this) closeModal(); });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
</script>
