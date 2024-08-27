@props([
    'message' => $emptyMessage,
    'bgClass' => 'bg-white',
])
<div class="no-data-message {{ $bgClass }} p-5">
    <span class="icon-wrapper">
        <span class="file-icon">
            <i class="la la-file"></i>
        </span>
        <span class="close-icon">
            <i class="la la-times-circle"></i>
        </span>
    </span>
    <p class="message">{{ __($message) }}</p>
</div>

@push('style')
    <style>
        .no-data-message {
            color: #ababab;
            text-align: center;
            padding: 10px 0;
        }

        .no-data-message .icon-wrapper {
            background-color: inherit;
            position: relative;
        }

        .no-data-message .file-icon i {
            font-size: 7rem;
        }

        .no-data-message .close-icon {
            position: absolute;
            bottom: 30px;
            left: 4px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: inherit;
            z-index: 1;
        }

        .no-data-message .close-icon i {
            font-size: 2.5rem;
        }

        .no-data-message .message {
            font-size: 1.2rem;
        }
    </style>
@endpush
