<div class="chatbox">
    <div class="chatbox__inner-wrapper chat-box__thread">
        <div class="chatbox__inner">
            @foreach ($trade->chats as $chat)
                @php
                    $isSender = false;
                    if ($chat->user_id == $trade->buyer_id) {
                        $senderName = null;
                        $senderImage = getImage(getFilePath('userProfile') . '/' . @$trade->buyer->image, avatar: true);
                    } elseif ($chat->user_id == $trade->seller_id) {
                        $senderName = null;
                        $senderImage = getImage(getFilePath('userProfile') . '/' . @$trade->seller->image, avatar: true);
                    } else {
                        $senderName = 'System';
                        $senderImage = siteFavicon();
                    }
                    
                    if ($chat->user_id == auth()->id()) {
                        $isSender = true;
                    }
                @endphp
                <div class="@if ($isSender) chat-author-two-wrapper @else chat-author-wrapper @endif @if ($senderName == 'System') admin-message-wrapper @endif">

                    <div class="@if ($isSender) chat-author-two @else chat-author @endif @if ($senderName == 'System') admin-message @endif">
                        @if (!$isSender)
                            <div class="thumb">
                                <img alt="" src="{{ $senderImage }}">
                            </div>
                        @endif
                        <div class="message-wrapper">
                            <div class="message">
                                <div class="message__inner">
                                    {{ __($chat->message) }}
                                </div>
                            </div>
                            @if ($chat->file)
                                <div class="message">
                                    <div class="message-attachment">
                                        <h6 class="title"> @lang('Attachment') </h6>
                                        <a class="file-demo-btn" href="{{ route('user.chat.download', [$trade->id, $chat->id]) }}"> {{ $chat->file }}</a>
                                    </div>
                                </div>
                            @endif
                            <span class="message-time">{{ showDateTime($chat->created_at) }}</span>
                        </div>
                        @if ($isSender)
                            <div class="thumb">
                                <img alt="" src="{{ $senderImage }}">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @if ($trade->status == Status::TRADE_ESCROW_FUNDED || $trade->status == Status::TRADE_BUYER_SENT || $trade->status == Status::TRADE_DISPUTED)
        <form action="{{ route('user.chat.store', $trade->id) }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="chatbox__bottom">
                <div class="upload-img">
                    <input accept=".png, .jpg, .jpeg" class="imgUpload" id="image" name="file" type="file">
                    <label for="image">
                        <img alt="" src="{{ getImage('assets/images/chat-img.png') }}">
                    </label>
                </div>
                <div class="form-group">
                    <textarea class="form--control" name="message" placeholder="@lang('Type a message')..." required></textarea>
                    <button class="send" type="submit"><img alt="" src="{{ getImage('assets/images/chat-send.png') }}"></button>
                </div>
            </div>
            <div class="file-preview d-none"></div>
        </form>
    @endif
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            document.querySelector('.chat-box__thread').scrollTop = document.querySelector('.chat-box__thread').scrollHeight;

            var imagesPreview = function(input, placeToInsertImagePreview) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    var html = `<img src="${event.target.result}" alt="">
                             <span class="cross removeImgBtn"><i class="las la-times"></i></span>`;
                    $(placeToInsertImagePreview).append(html);
                    $(placeToInsertImagePreview).removeClass('d-none');
                }

                reader.readAsDataURL(input.files[0]);
            }

            $('.imgUpload').on('change', function() {
                imagesPreview(this, '.file-preview');
            });

            $(document).on('click', '.removeImgBtn', function() {
                $('.file-preview').html('');
                $('.file-preview').addClass('d-none');
                $('.imgUpload').val('');
            });
        })(jQuery);
    </script>
@endpush
