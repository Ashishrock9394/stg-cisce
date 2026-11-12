
@extends('layouts.app') 
@section('title', 'CISCE | OTP Verification')


<!-- Login Form -->
@push('icse-style')
      <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
      <link href="{{ asset('assets/css/nvsdashboard.min.css') }}?v={{ time() }}" rel="stylesheet" media="screen">        
      <link href="{{ asset('assets/css/nvsstyle.css') }}?v={{ time() }}" rel="stylesheet" media="all">
      <link href="{{ asset('assets/css/nvscustom-style.css') }}?v={{ time() }}" rel="stylesheet">
      <link href="{{ asset('assets/css/nvsresponsive.css') }}?v={{ time() }}" rel="stylesheet" media="screen">
@endpush


@section('content')
<main class="__main">
      <div class="login_form">
         <div class="brandlog">
            <a href="{{ url('/') }}">
            <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" height="80" class="d-inline-block align-top mb-0" alt="goforfit" title="goforfit"></a>
         </div>

         <h5 class="text-center mt-3 mb-2" style="font-weight:600;">
            OTP Verification
        </h5>
        @php
            $role   = session('otp_role');
        @endphp
        <p class="text-center text-muted mb-4">
            <!-- An OTP has been sent to the Principal’s Official<br> {{ $channel === 'sms' ? 'Mobile number' : 'Email address' }}  -->
            An OTP has been sent to the {{ ucfirst($role) }}'s Official<br> {{ $channel === 'sms' ? 'Mobile number' : 'Email address' }} 
            <strong>({{ $masked }}) </strong> registered with CISCE.
            <br> 
            Please enter the OTP received to login to the Active CISCE portal.
            
        </p>
    
    
        <form method="POST" action="{{ route('otp.verify') }}"> 
            @csrf
            <div class="input_box mb-3">
                <label>One Time Password</label>
                <input type="text"  name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"  class="form-control text-center"  placeholder="Enter OTP"  required>
            </div>
            
           <button type="submit" class="btn btn-lg btn-primary btn-block">  Verify OTP  </button>

            @php  $show = false; @endphp
            @error('otp')
                <p class="error_message danger" style="margin-top: -17px;"><span>  {{ $message }} 
                    @error('route')
                       @php  $show = true; @endphp
                      <a class="link-opacity-100" style="color:red;text-decoration: underline;" href="{{ route('login') }}">Please Login Again</a></span>
                    @enderror
                </p>
            @enderror

            @if ($errors->has('email'))
                <p class="error_message danger" style="margin-top: -17px;"><span>
                    {{ $errors->first('email') }}
                </p>
            @endif

            @if (session('cooldown'))
               <p class="error_message danger" style="margin-top: -17px;"><span>
                    {{ session('cooldown') }}
                </p>
            @endif
        </form>

        <div id="otpMessage"></div>

        @if($show == false)
            <div class="text-center mt-3 otp_resend" style="display: flex; justify-content: center; column-gap: 5px;">
                <p class="text-muted mb-1">Didn’t receive OTP?</p>
                <a href="javascript:void(0)" id="resendBtn" style="font-weight:600;"> Resend OTP </a>
                <div id="timer" style="font-weight: bold; color: #ff0000; margin-left:6px;">  30 seconds </div>
            </div>
        @endif
      </div>

      <div class="text-center mt-4">
         <p style="color:#333;font-size: 12px;" class="mb-2">©2025 CISCE, All rights reserved. <a href="{{ route('privacy.policy')}}" >Privacy Policy</a></p>
         <p style="color:#333;font-size: 12px;" class="mb-3">powered by <img src="{{ asset('logo/fitness365-logo-web.jpg') }}" alt="Fitness 365" class="img-fluid" style="max-width: 90px; height: 28px;">
         </p>
      </div>
</main>

@endsection

@push('icse-footer-script')

<script>

    let countdown = Math.min({{ $remainingSeconds ?? 30 }}, 30);
    let resendBtn     = document.getElementById("resendBtn");
    let messageBox    = document.getElementById("otpMessage");
    let timerDisplay  = document.getElementById("timer");
    let interval = null;


    function startTimer() {



        disableResendBtn();

        if (interval) clearInterval(interval);
        timerDisplay.innerText = countdown + " seconds";

        interval = setInterval(() => {

            countdown--;
            timerDisplay.innerText = countdown + " seconds";

              console.log('timerDisplay' , countdown);

            if (countdown <= 0) {
                clearInterval(interval);
                enableResendBtn();
                timerDisplay.innerText = "";
            }

        }, 1000);
    }

    function disableResendBtn() {
        resendBtn.style.pointerEvents = "none";
        resendBtn.style.opacity = "0.5";
    }

    function enableResendBtn() {
        resendBtn.style.pointerEvents = "auto";
        resendBtn.style.opacity = "1";
    }

    if (countdown > 0) {
        startTimer();
    } else {
        enableResendBtn();
        timerDisplay.innerText = "";
    }

    resendBtn.addEventListener("click", function () {

        messageBox.innerHTML = "";
        disableResendBtn();
        resendBtn.innerText = "Sending...";

        fetch("{{ route('otp.resend') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(response => {

            resendBtn.innerText = "Resend OTP";
            if (response.status === "success") {
                
                messageBox.innerHTML = `<p class="error_message danger" style="margin-top: -17px;">${response.message}</p>`;
                countdown = 30;
                startTimer();
            }
            else if (response.status === "cooldown") {
                messageBox.innerHTML = `<p class="error_message danger" style="margin-top: -17px;">${response.message}</p>`;
                countdown = response.remainingSeconds;
                startTimer();
            } else {
                messageBox.innerHTML = `<p class="error_message danger" style="margin-top: -17px;">Unable to resend OTP at the moment. Please try again later.</p>`;
                enableResendBtn();
            }
        })
        .catch(() => {
            messageBox.innerHTML = `<p class="error_message danger" style="margin-top: -17px;" >Something went wrong. Try again.</p>`;
            resendBtn.innerText = "Resend OTP";
            enableResendBtn();
        });
    });

</script>

@endpush