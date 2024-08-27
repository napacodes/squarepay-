@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Initiated')</th>
                                    <th>@lang('Transaction Id')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    @php
                                        $details = $deposit->detail ? json_encode($deposit->detail) : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                        </td>

                                        <td>{{ $deposit->trx }}</td>

                                        <td>
                                            <span class="fw-bold">{{ $deposit->user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ appendQuery('search', @$deposit->user->username) }}"><span>@</span>{{ $deposit->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            {{ __($deposit->crypto->symbol) }}{{ showAmount($deposit->amount,8) }} + <span class="text-danger" title="@lang('charge')">{{ showAmount($deposit->charge,8)}} {{ __($deposit->crypto->code) }}</span>
                                            <br>
                                            <strong title="@lang('Amount with charge')">
                                            {{ showAmount($deposit->amount + $deposit->charge, 8) }} {{ __($deposit->crypto->code) }}
                                            </strong>
                                        </td>
                                        <td>
                                            <span><span class="badge badge--success">@lang('Success')</span><br>{{diffForHumans($deposit->created_at)}}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($deposits->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($deposits) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' placeholder='Username / Email' />
@endpush