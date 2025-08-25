@extends ('livewire.emails.email-layout')

@section ('content')

    <div class="email">
        <div class="email-wrapper">

            <!-- Header -->
            <div class="email-header">
                <a href="/" target="_blank" style="text-decoration: none; width: 100%;">
                    <h1 class="email-greetings">{{ $header }}</h1>
                </a>
            </div>

            <!-- Body -->
            <div class="email-body">
                <div>
                    <div class="content-cell">
                        <h1 class="email-greetings-2">{!! $greetings !!}!</h1>
                        <p class="message">
                            {!! $message_body !!}
                        </p>

                        @if($notifDetails)
                            <p class="message">Details:</p>
                            <p class="message">
                                {!! $notifDetails !!}
                            </p>
                        @endif
                        
                        @if($action_button_text && $action_button_url)
                            <div class="action-wrapper">
                                <a href="{{ $action_button_url }}" target="_blank">
                                    <button class="action-btn">
                                        {{ $action_button_text }}
                                    </button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p class="footer-content" style="width: 100%; text-align: center;">
                    {{ $footer }}
                </p>
            </div>

        </div>
    </div>

@endsection