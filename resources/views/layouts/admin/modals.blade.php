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
