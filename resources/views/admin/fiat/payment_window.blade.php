@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        @forelse ($paymentWindows as $window)
            <div class="col-lg-3">
                <div class="card overflow-hidden">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                        <div class="bg--dark b-radius--4 px-2 py-1">
                            {{ __($window->minute) }} @lang('Minutes')
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $window }}" data-modal_title="@lang('Edit Payment Window')" data-has_status="1">
                                <i class="la la-pencil"></i>@lang('Edit')
                            </button>
                            <button class="btn btn-sm btn-outline--danger confirmationBtn" type="button" data-action="{{ route('admin.window.remove', $window->id) }}" data-question="@lang('Are you sure to remove this payment window?')">
                                <i class="la la-trash"></i> @lang('Remove')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-lg-12">
                <div class="card overflow-hidden">
                    <div class="card-body p-3">
                        <p class="text-center">{{ __($emptyMessage) }}</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Add Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.window.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Minutes')</label>
                            <input type="number" class="form-control" name="minute" value="{{ old('minute') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save')</button>
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
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Payment Window')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    </div>
@endpush
