<div class="chat-box">
    <div class="chat-box__header">
        <div class="chat-author">
            <div class="thumb">
                <img alt="image" src="{{ getImage($profileImage->path . '/' . @$topImage, null, true) }}">
            </div>

            <div class="content">
                @if ($trade->buyer_id == $user->id)
                    <h6 class="text--base">{{ __($trade->seller->username) }}</h6>
                @elseif ($trade->seller_id == $user->id)
                    <h6 class="text--base">{{ __($trade->buyer->username) }}</h6>
                @endif
            </div>
        </div>
        <div class="trade-status flex-shrink-0">
            @if ($trade->status != Status::TRADE_COMPLETED)
                <button class="btn btn-sm btn--dark refresh" title="@lang('Click here to load new chat and trade current status')" type="button"><i class="las la-sync-alt"></i> @lang('Refresh')</button>
            @endif
        </div>
    </div>

    <div class="chat-box__thread">
        @foreach ($trade->chats as $chat)
            @php
                if ($chat->user_id == $trade->buyer_id) {
                    $senderName = null;
                    $senderImage = getImage(getFilePath('userProfile') . '/' . @$trade->buyer->image);
                } elseif ($chat->user_id == $trade->seller_id) {
                    $senderName = null;
                    $senderImage = getImage(getFilePath('userProfile') . '/' . @$trade->seller->image);
                } else {
                    $senderName = 'System';
                    $senderImage = siteFavicon();
                }
            @endphp

            <div class="single-message @if ($chat->user_id == auth()->id()) message--right @else message--left @endif  @if ($senderName == 'System') admin-message @endif">
                <div class="message-content-outer">
                    <div class="message-content">
                        <h6 class="name">{{ $senderName }}</h6>
                        <p class="message-text">{{ __($chat->message) }}.</p>

                        @if ($chat->file)
                            <div class="messgae-attachment">
                                <b class="text-sm d-block"> @lang('Attachment') </b>
                                <a class="file-demo-btn" href="{{ route('user.chat.download', [$trade->id, $chat->id]) }}">
                                    {{ __($chat->file) }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <span class="message-time d-block text-end mt-2">{{ showDateTime($chat->created_at) }}</span>
                </div>
                <div class="message-author">
                    <img alt="image" class="thumb" src="{{ $senderImage }}">
                </div>

            </div><!-- single-message end -->
        @endforeach
    </div>

    @if ($trade->status == Status::TRADE_ESCROW_FUNDED || $trade->status == Status::TRADE_BUYER_SENT || $trade->status == Status::TRADE_DISPUTED)
        <div class="chat-box__footer">
            <form action="{{ route('user.chat.store', $trade->id) }}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="chat-send-area">
                    <div class="chat-send-field">
                        <textarea class="form-control" id="chat-message-field" name="message" placeholder="@lang('Type here')" required></textarea>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center w-100">
                        <div class="chat-send-file">
                            <div class="position-relative trade-chat-file-upload">
                                <input accept=".jpg , .png, ,jpeg .pdf" class="custom-file" id="file" name="file" type="file">
                            </div>
                        </div>
                        <div class="chat-send-btn">
                            <button class="btn--base btn-sm" type="sbumit">@lang('Send')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div><!-- chat-box end -->

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.refresh').on('click', function() {
                location.reload();
            });

            document.querySelector('.chat-box__thread').scrollTop = document.querySelector('.chat-box__thread').scrollHeight;
        })(jQuery);
    </script>
@endpush
