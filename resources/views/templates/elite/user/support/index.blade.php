@extends($activeTemplate . 'layouts.master_with_menu')

@section('content')
    @if (blank($supports))
        <x-no-data message="No support ticket yet"></x-no-data>
    @else
        <div class="ptable-wrapper">
            <table class="table table--responsive--lg">
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
                            <td> <a class="font-weight-bold text--base" href="{{ route('ticket.view', $support->ticket) }}"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
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
                                <a class="btn btn-outline--base-two btn--sm" href="{{ route('ticket.view', $support->ticket) }}">
                                    <i class="las la-desktop"></i> @lang('View Ticket')
                                </a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        @if ($supports->hasPages())
            <div class="pagination-wrapper">
                {{ $supports->links() }}
            </div>
        @endif
    @endif
@endsection

@push('breadcrumb-plugins')
    <a class="ptable-header-right__link" href="{{ route('ticket.open') }}">
        <span class="icon"><i class="las la-plus"></i></span>
        <span class="text">@lang('New Ticket')</span>
    </a>
@endpush
