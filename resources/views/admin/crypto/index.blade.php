@extends('admin.layouts.app')

@section('panel')
    @php $cryptoImage = fileManager()->crypto(); @endphp

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Symbol')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($cryptos as $crypto)
                                    <tr>
                                        <td>{{ $cryptos->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="user justify-content-center">
                                                <div class="thumb"><img src="{{ getImage($cryptoImage->path . '/' . $crypto->image, $cryptoImage->size) }}" alt="@lang('image')"></div>
                                            </div>
                                        </td>
                                        <td>{{ __($crypto->name) }}</td>
                                        <td>{{ __($crypto->code) }}</td>
                                        <td>{{ __($crypto->symbol) }}</td>
                                        <td>
                                            <span >{{ showAmount($crypto->rate, 8) }} @lang('USD')</span> / {{ __($crypto->code) }}
                                        </td>

                                        <td>
                                            @php echo $crypto->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.crypto.edit', $crypto->id) }}" class="btn btn-sm btn-outline--primary"><i class="la la-pencil-alt"></i> @lang('Edit')</a>
                                                @if ($crypto->status)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.crypto.status', $crypto->id) }}" data-question="@lang('Are you sure to disable this crypto currency?')">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.crypto.status', $crypto->id) }}" data-question="@lang('Are you sure to enable this crypto currency?')">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @endif
                                            </div>
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
                @if ($cryptos->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cryptos) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    {{-- Status Modal --}}
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center has-search-form">
        <x-search-form></x-search-form>
        <a class="btn btn-outline--primary" href="{{ route('admin.crypto.add') }}"><i class="las la-plus"></i>@lang('Add New')</a>
    </div>
@endpush
