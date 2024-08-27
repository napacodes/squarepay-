@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-lg-7">
            <div class="chat-box">
                <div class="chat-box__header bg--primary b-radius--10 p-3">
                    <div class="content">
                        <h5 class="pb-2 text-white">@lang('The Amount Is') {{ __($title) }} {{ __($title2) }}</h5>
                    </div>
                </div>
                <div class="chat-box__body">
                    <div class="chat-main position-relative">
                        <div class="bg-el position-absolute" style="background-image: url({{ getImage($activeTemplateTrue . 'images/chat-pattern.png', '1380x930') }});"></div>

                        @foreach ($tradeDetails->chats()->get() as $chat)
                            @if ($chat->user_id == $tradeDetails->buyer_id)
                                <div class="single-chat chat--left">
                                    <div class="content">
                                        <div class="message">
                                            <h6 class="mb-2 fs--16px">{{ $tradeDetails->buyer->fullname }}</h6>
                                            <p>{{ __($chat->message) }}</p>
                                            <div class="chat-attachment">
                                                @if ($chat->file)
                                                    <a href="{{ route('admin.trade.chat.download', $chat->id) }}" class="single-attachment"><i class="fas fa-download"></i> {{ __($chat->file) }} </a>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="chat-time"><i class="far fa-clock"></i> {{ $chat->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($chat->user_id == $tradeDetails->seller_id)
                                <div class="single-chat chat--left">
                                    <div class="content">
                                        <div class="message">
                                            <h6 class="mb-2 fs--16px">{{ $tradeDetails->seller->fullname }}</h6>
                                            <p>{{ __($chat->message) }}</p>
                                            <div class="chat-attachment">
                                                @if ($chat->file)
                                                    <a href="{{ route('admin.trade.chat.download', $chat->id) }}" class="single-attachment"><i class="fas fa-download"></i> {{ __($chat->file) }} </a>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="chat-time"><i class="far fa-clock"></i> {{ $chat->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($chat->admin)
                                <div class="single-chat chat--right">
                                    <div class="content">
                                        <div class="message">
                                            <h6 class="mb-2 fs--16px">@lang('System')</h6>
                                            <p>{{ __($chat->message) }}</p>
                                            <div class="chat-attachment">
                                                @if ($chat->file)
                                                    <a href="{{ route('admin.trade.chat.download', $chat->id) }}" class="single-attachment"><i class="fas fa-download"></i> {{ __($chat->file) }} </a>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="chat-time"><i class="far fa-clock"></i> {{ $chat->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if ($tradeDetails->status == Status::TRADE_DISPUTED)
                    <div class="chat-box__footer position-relative">
                        <div class="bg-el position-absolute" style="background-image: url({{ getImage($activeTemplateTrue . 'images/chat-pattern.png', '1380x930') }});"></div>
                        <div class="chat-form">
                            <form action="{{ route('admin.trade.chat.store', $tradeDetails->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <textarea name="message" class="form--control" placeholder="@lang('Write message')" required></textarea>

                                <div class="bottom d-flex flex-wrap">
                                    <div class="left">
                                        <div class="attach-file-upload">
                                            <input type="file" name="file" id="file" class="attach-file" accept=".jpg , .png, ,jpeg .pdf">
                                            <button type="button" class="attach-file-remove"><i class="las la-times"></i></button>
                                            <label for="file">@lang('Attach file') <i class="las la-paperclip"></i></label>
                                        </div>
                                    </div>
                                    <div class="right">
                                        <button type="submit" class="btn btn-sm btn--success">@lang('Send') <i class="lab la-telegram-plane"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            @if ($tradeDetails->status == Status::TRADE_DISPUTED || $tradeDetails->status == Status::TRADE_CANCELED)
                <div class="chat-box__header bg--danger b-radius--10 p-3">
                    <div class="content">
                        <h5 class="pb-2 text-white">{{ __($tradeDetails->details) }}</h5>
                    </div>
                </div>
            @endif

            <div class="custom--card border">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h6><i class="las la-info-circle"></i> @lang('Trade Information')</h6>
                    @php echo $tradeDetails->statusBadge; @endphp
                </div>
                <div class="card-body">
                    <ul class="caption-list">
                        <li>
                            <span class="caption">@lang('Buyer Name')</span>
                            <span class="value">{{ __($tradeDetails->buyer->fullname) }}</span>
                        </li>
                        <li>
                            <span class="caption">@lang('Seller Name')</span>
                            <span class="value">{{ __($tradeDetails->seller->fullname) }}</span>
                        </li>
                        <li>
                            <span class="caption">@lang('Amount')</span>
                            <span class="value">{{ showAmount($tradeDetails->amount) }} {{ __($tradeDetails->fiat->code) }}</span>
                        </li>
                        <li>
                            <span class="caption">{{ __($tradeDetails->crypto->code) }}</span>
                            <span class="value">{{ showAmount($tradeDetails->crypto_amount, 8) }}</span>
                        </li>
                        <li>
                            <span class="caption">@lang('Payment Window')</span>
                            <span class="value">{{ $tradeDetails->window }} @lang('Minutes')</span>
                        </li>

                        @if ($tradeDetails->reported_by && $tradeDetails->status != Status::TRADE_DISPUTED)
                            <li>
                                <span class="caption">@lang('Reported By')</span>
                                <span class="value">
                                    {{$tradeDetails->reported_by == $tradeDetails->seller_id ? trans('Seller') : trans('Buyer')}}
                                </span>
                            </li>
                        @endif
                    </ul>

                    @if ($tradeDetails->status == Status::TRADE_DISPUTED)
                        <div class="d-flex flex-wrap gap-2 style--two text-center mt-4 border-top pt-3">
                            <button class="btn btn-md btn--info confirmationBtn flex-fill" type="button" data-question="@lang('Are you sure to return') {{ $tradeDetails->crypto->code }} @lang('to seller') ?" data-action="{{ route('admin.trade.return', $tradeDetails->id) }}"> <i class="las la-undo"></i> @lang('In Favor of Seller')</button>

                            <button class="btn btn-md btn--primary confirmationBtn flex-fill" type="button" data-question="@lang('Are you sure to release') {{ $tradeDetails->crypto->code }}?" data-action="{{ route('admin.trade.release', $tradeDetails->id) }}"> <i class="la la-check-circle" aria-hidden="true"></i> @lang('In Favor of Buyer')</button>
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-3 mt-3 pt-3 border-top justify-content-center">
                        <button type="button" class="text--white rounded bg--primary terms">
                            <i class="la la-info-circle"></i> @lang('Terms of trade')
                        </button>

                        <button type="button" class="text--white rounded bg--info payment">
                            <i class="la la-info-circle"></i> @lang('Payment details')
                        </button>
                    </div>
                </div>
            </div><!-- custom--card end -->
        </div>
    </div><!-- row end -->


    <!-- terms modal -->
    <div class="modal fade" id="termsPaymentModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Terms of trade')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="body-msz"></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/chat.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var termsPaymentModal = $('#termsPaymentModal');

            $("#file").on("change", function(e) {
                if ($("#file").val() !== "") {
                    $('.attach-file-upload').addClass('has-file');
                }
            });

            $('.attach-file-remove').on('click', function() {
                $("#file").val('');
                $('.attach-file-upload').removeClass('has-file');
            });

            $('.terms').on('click', function() {
                var header = ` @lang('Terms of trade')`;
                var mszBody = '{{ $tradeDetails->advertisement->terms }}';

                termsPaymentModal.find('.modal-title').text(header);
                termsPaymentModal.find('.body-msz').text(mszBody);
                termsPaymentModal.modal('show');
            });

            $('.payment').on('click', function() {
                var header = ` @lang('Payment Details')`;
                var mszBody = '{{ $tradeDetails->advertisement->details }}';

                termsPaymentModal.find('.modal-title').text(header);
                termsPaymentModal.find('.body-msz').text(mszBody);
                termsPaymentModal.modal('show');
            });

            document.querySelector('.chat-box__body').scrollTop = document.querySelector('.chat-box__body').scrollHeight;

        })(jQuery);
    </script>
@endpush
