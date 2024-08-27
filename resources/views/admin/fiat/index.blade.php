@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Symbol')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fiats as $fiat)
                                    <tr>
                                        <td>{{ $fiats->firstItem() + $loop->index }}</td>
                                        <td>{{ __($fiat->name) }}</td>
                                        <td>{{ $fiat->code }}</td>
                                        <td>{{ $fiat->symbol }}</td>
                                        <td>
                                            <span>{{ showAmount($fiat->rate) }} {{ __($fiat->code) }}</span> / @lang('USD')
                                        </td>
                                        <td>
                                            @php echo $fiat->statusBadge @endphp
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $fiat }}" data-modal_title="@lang('Edit Fiat Currency')" data-has_status="1">
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
                @if ($fiats->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($fiats) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    {{-- Create & Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.fiat.currency.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" placeholder="@lang('Great Britain Pound')" value="{{ old('name') }}" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Code')</label>
                            <input type="text" class="form-control" name="code" placeholder="@lang('GBP')" value="{{ old('code') }}" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Symbol')</label>
                            <input type="text" class="form-control" name="symbol" placeholder="@lang('£')" value="{{ old('symbol') }}" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Rate')</label>
                            <div class="input-group">
                                <span class="input-group-text">1 @lang('USD') = </span>
                                <input class="form-control" type="number" step="any" name="rate" placeholder="@lang('0.75')" value="{{ old('rate') }}" required>
                                <span class="input-group-text currency-symbol"></span>
                            </div>
                        </div>

                        <div class="status"></div>
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
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form></x-search-form>

        <button type="button" class="btn btn-sm btn-outline--primary me-2 h-45 cuModalBtn" data-modal_title="@lang('Add New Fiat Currency')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    </div>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";

            $('#cuModal').on('show.bs.modal', function() {
                $('#cuModal').find('.currency-symbol').text($('[name=code]').val());
            });

            $('[name=code]').on('input', function() {
                $('#cuModal').find('.currency-symbol').text($(this).val());
            });
        })(jQuery);
    </script>
@endpush
