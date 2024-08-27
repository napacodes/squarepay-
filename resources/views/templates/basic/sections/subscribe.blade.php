@php
    $content = getContent('subscribe.content', true);
@endphp
<section class="newslatter-section pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="newslatter-content text-center">
                    <h2 class="title">{{ __(@$content->data_values->heading) }}</h2>
                    <p class="desc">{{ __(@$content->data_values->sub_heading) }}</p>
                    <form method="get" class="newslatter-form m-auto" id="subscribe-form">
                        <div class="form-group">
                            <input type="email" name="email" class="form--control" placeholder="Enter Your Email">
                            <button type="submit" class="btn btn--base">
                                {{ __(@$content->data_values->button_text) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#subscribe-form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                let $this = $(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: "{{ route('subscribe') }}",
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $this.find('button[type=submit]').html(`
                        <span class="right-sidebar__button-icon">
                            <i class="las la-spinner la-spin"></i>  {{ __(@$content->data_values->button_text) }}
                        </span>`).attr('disabled', true);
                    },
                    complete: function(e) {
                        setTimeout(() => {
                            $this.find('button[type=submit]').html(
                                `{{ __(@$content->data_values->button_text) }}`
                            ).attr('disabled', false);
                        }, 500);
                    },
                    success: function(resp) {
                        setTimeout(() => {
                            if (resp.success) {
                                notify('success', resp.message);
                                $($this).trigger('reset');
                            } else {
                                notify('error', resp.message || resp.error);
                            }
                        }, 500);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush



@push('style')
<style>
.newslatter-form .form--control{
    border-radius: 30px;
}
.newslatter-form button.btn{
    border-radius: 30px !important;
}
</style>
@endpush
