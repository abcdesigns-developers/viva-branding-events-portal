@extends('layouts.admin')
@section('title') Product Samples @endsection
@section('content')
@php
    $isCreate = Route::is('admin.product-samples.create');
@endphp
<div class="card">
    <form method="POST" action="{{ $isCreate ? route('admin.product-samples.store') : route('admin.product-samples.update', ['product_sample' => $productSample->route_key]) }}" autocomplete="off" id="product-sample-form">
        @csrf
        {{ $isCreate ? '' : method_field('PUT') }}
        <div class="card-header">
            <h2 class="card-title">{{ $isCreate ? 'Create' : 'Edit' }} Product Sample</h2>
        </div>
        <div class="card-body">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Name" name="name" value="{{ $productSample->name ?? '' }}"/>
                    <div id="name-error" class="form-error"></div>
                </div>
                <div>
                    <label for="unit_cost" class="form-label">Unit cost (₹, optional)</label>
                    <input type="number" step="0.01" min="0" id="unit_cost" class="form-control" placeholder="e.g. 450" name="unit_cost" value="{{ $productSample->unit_cost ?? '' }}"/>
                    <div id="unit_cost-error" class="form-error"></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Save
            </button>
            <a href="{{ route('admin.product-samples.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back-up-double"></i> Cancel
            </a>
        </div>
    </form>
</div>
<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 1500, closeButton: true };

    $('#product-sample-form').submit(function(e) {
        e.preventDefault();
        $('div[id$="-error"]').empty();
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, '', toastOptions);
                    setTimeout(function() {
                        window.location.href = "{!! route('admin.product-samples.index') !!}";
                    }, 500);
                } else {
                    toastr.error('There is some error!!', '', toastOptions);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
                $.each(xhr.responseJSON?.errors || {}, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
            }
        });
    });
</script>
@endsection
