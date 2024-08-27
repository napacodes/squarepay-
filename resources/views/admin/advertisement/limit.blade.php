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
                                    <th>@lang('Completed Trade')</th>
                                    <th>@lang('Advertise Limit')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($limits as $limit)
                                    <tr>
                                        <td>{{ $limits->firstItem() + $loop->index }}</td>
                                        <td>{{$limit->completed_trade}}</td>
                                        <td>{{$limit->ad_limit}}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $limit }}" data-modal_title="@lang('Edit Advertisement Limit')">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </button>

                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" type="button" data-action="{{ route('admin.ad.limit.remove',$limit->id)}}" data-question="@lang('Are you sure to remove this ad limit?')">
                                                <i class="la la-trash"></i> @lang('Remove')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($limits->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($limits) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add METHOD MODAL --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('New Advertise Limit')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.ad.limit.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Completed Trade')</label>
                            <input type="number" class="form-control" placeholder="0" name="completed_trade" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Advertisement Limit')</label>
                            <input type="number" class="form-control" placeholder="0" name="ad_limit" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <!-- Modal Trigger Button -->
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Limit')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    </div>
@endpush
