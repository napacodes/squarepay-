@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row mb-none-30">
                <div class="col-xl-12 col-lg-12 col-sm-12">
                    <div class="btn-group justify-content-end">
                        <a href="{{ route('ticket.open') }}" class="btn--base btn-sm"><i class="las la-plus-circle"></i> @lang('New Ticket')</a>
                    </div>
                </div>
            </div>
            <div class="row mt-50">
                <div class="col-xl-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md">
                                <table class="table custom--table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Subject')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Priority')</th>
                                            <th>@lang('Last Reply')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($supports as $key => $support)
                                            <tr>
                                                <td> <a href="{{ route('ticket.view', $support->ticket) }}" class="font-weight-bold text--base"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                                                <td>
                                                    @if ($support->status == Status::TICKET_OPEN)
                                                        <span class="badge badge--primary">@lang('Open')</span>
                                                    @elseif($support->status == Status::TICKET_ANSWER)
                                                        <span class="badge badge--success">@lang('Answered')</span>
                                                    @elseif($support->status == Status::TICKET_REPLY)
                                                        <span class="badge badge--warning">@lang('Customer Reply')</span>
                                                    @elseif($support->status == Status::TICKET_CLOSE)
                                                        <span class="badge badge--danger">@lang('Closed')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($support->priority == Status::PRIORITY_LOW)
                                                        <span class="badge badge--primary">@lang('Low')</span>
                                                    @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                                        <span class="badge badge--info">@lang('Medium')</span>
                                                    @elseif($support->priority == Status::PRIORITY_HIGH)
                                                        <span class="badge badge--success">@lang('High')</span>
                                                    @endif
                                                </td>
                                                <td>{{ diffForHumans($support->last_reply) }} </td>

                                                <td>
                                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn-outline--base btn-sm">
                                                        <i class="las la-desktop"></i> @lang('View Ticket')
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                                @if (blank($supports))
                                    <x-no-data message="No support ticket yet"></x-no-data>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($supports->hasPages())
                        <div class="pagination-wrapper">
                            {{ $supports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
