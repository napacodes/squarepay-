@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pb-60">
        <div class="coin-search-area">
            <div class="container">
                <form class="coin-search-form text-center"  method="GET">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="flex-fill">
                            <select class="select select2" name="crypto">
                                <option value="">@lang('All')</option>
                                @foreach ($cryptos as $crypto)
                                    <option value="{{ $crypto->id }}" @selected(request()->crypto == $crypto->id)>{{ __($crypto->code) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill">
                            <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="@lang('TRX No.')">
                        </div>
                        <div>
                            <button type="submit" class="btn--base w-100"><i class="la la-search"></i> @lang('Search')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container pt-60">
            <div class="row justify-content-center mt-2">
                <div class="col-lg-12 ">
                    @include($activeTemplate . 'user.withdraw.withdrawals_table')
                </div>
            </div>
        </div>
    </section>

    {{-- Detail MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="withdraw-detail"></div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.approveBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
