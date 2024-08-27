@extends('admin.layouts.app')

@section('panel')
    @php
        $gatewayImage = fileManager()->gateway();
    @endphp

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Supported Currencies')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fiatGateways as $gateway)
                                    <tr>
                                        <td>{{ __($gateway->name) }}</td>
                                        <td>
                                            <div class="user justify-content-center">
                                                <div class="thumb"><img src="{{ getImage($gatewayImage->path . '/' . $gateway->image, $gatewayImage->size) }}" alt="@lang('image')"></div>
                                            </div>
                                        </td>
                                        <td>{{ $gateway->slug }}</td>
                                        <td>{{ count($gateway->code) }}</td>
                                        <td>
                                            @php echo $gateway->statusBadge @endphp
                                        </td>
                                        <td>
                                            @php
                                                $gateway->image_with_path = getImage($gatewayImage->path . '/' . $gateway->image, $gatewayImage->size);
                                            @endphp

                                            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn editBtn" data-resource="{{ $gateway }}" data-modal_title="@lang('Edit Fiat Gateway')" data-has_status="1">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($fiatGateways->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($fiatGateways) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.fiat.gateway.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">

                                    <label>@lang('Image')</label>
                                    <x-image-uploader class="w-100" type="gateway" :required=false />
                                    
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" placeholder="@lang('Paypal')" value="{{ old('name') }}" required />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input type="text" class="form-control" name="slug" placeholder="@lang('Paypal')" value="{{ old('slug') }}" required />
                                    <code>@lang('Spaces are not allowed')</code>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Supported Currencies')</label>
                                    <select class="select2-auto-tokenize code " name="code[]" multiple="multiple" required>
                                        @foreach ($fiatCodes as $fiat)
                                            <option value="{{ $fiat->id }}">{{ __($fiat->code) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="status"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
        <x-search-form></x-search-form>
        <button type="button" class="btn btn-sm btn-outline--primary me-2 h-45 cuModalBtn" data-modal_title="@lang('Add New Fiat Gateway')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.editBtn').on('click', function() {
                var resource = $(this).data('resource');
                $('#cuModal').find('.code').val(resource.code).select2();
                $('#cuModal').find('[name=image]').removeAttr('required');
            });

            $('#cuModal').on('hidden.bs.modal', function() {
                $('#cuModal').find('.code').val('').select2();
                $('#cuModal').find('[name=image]').attr('required', 'required');
            });

            $('[name=slug]').on('keyup', function(e) {
                var keyCode = e.keyCode || e.which;
                var regex = /^[A-Za-z0-9]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (e.keyCode == 32) {
                    $(this).val($(this).val().replace(/\s+/g, '-'));
                }
            });
        })(jQuery);
    </script>
@endpush
