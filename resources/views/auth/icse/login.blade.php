@extends('layouts.app') 
@section('title', 'CISCE | ' . $title)

<!-- Login Form -->
@push('icse-style')
<link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
      <link href="{{ asset('assets/css/nvsdashboard.min.css') }}?v={{ time() }}" rel="stylesheet" media="screen">        
      <link href="{{ asset('assets/css/nvsstyle.css') }}?v={{ time() }}" rel="stylesheet" media="all">
      <link href="{{ asset('assets/css/nvscustom-style.css') }}?v={{ time() }}" rel="stylesheet">
      <link href="{{ asset('assets/css/nvsresponsive.css') }}?v={{ time() }}" rel="stylesheet" media="screen">
@endpush

@section('content')

<main class="__main m-auto">
      <div class="login_form">
         <div class="brandlog">
            <a href="{{ url('/') }}">
            <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" height="80" class="d-inline-block align-top mb-0" alt="goforfit" title="goforfit"></a>
         </div>
         @php
            $user_type                = Cookie::get('user_type');
            $student_id_cookie        = Cookie::get('student_id_cookie') ?? old('student_id');
            $student_password_cookie  = Cookie::get('student_password_cookie');  
            $decodedPwd = base64_decode(base64_decode($student_password_cookie));
            $plainPassword = substr($decodedPwd, 1, -1);
            $student_remember_token   = Cookie::get('student_remember_token') ?? old('remember');  
         @endphp


         <form method="POST" action="{{ route('authenticate.login') }}"> 
            @csrf
            <div class="input-group">
               <div class="input-group-prepend">
                  <label class="input-group-text">Login As</label>
                  <input type="hidden" name="login_by" class="option_type" value="">
               </div>
               <select class="custom-select form-control" id="login_type">
                  <option selected>Choose User Type</option>
                  <option value="School">School</option>
                  <option value="Parent">Parent</option>
                  <option value="Trainer">Trainer</option>
                  <option value="Cicse">CISCE HQ</option>
                  {{-- <option value="Admin">Admin</option> --}}
               </select>
            </div>
            <!-- Parent Login Fields -->
            <div id="user_id_box" style="display: none;">
               <div class="input_box mt-3">
                  <label for="userid_input">User Id</label>
                  <input type="text" id="userid_input" name="student_id" placeholder="Enter User Id" class="form-control" value="{{ $student_id_cookie }}" />
                  @error('student_id') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
               </div>

               <div class="input_box">
                  <label for="dob_input">Password</label>
                  <input type="password" id="dob_input" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ $plainPassword }}" placeholder="Enter your password" />
                  @error('dob') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
               </div>
            </div>

            <!-- Email Login Fields (Other Users) -->
            <div id="email_box">
               <div class="input_box mt-3">
                  <label for="email_input">Email</label>
                  <input type="email" id="email_input" name="email" placeholder="Enter Email" class="form-control" value="{{ old('email') }}" />
                  @error('email') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
               </div>

               <div class="input_box" style="margin-bottom:30px;">
                  <label for="password_input">Password</label>
                  <input type="password" id="password_input" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" value="{{ old('password') }}" />
                  @error('password') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
               </div>
            </div>

            {{-- <div class="mb-4 form-check">
               <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ $student_remember_token ? 'checked' : '' }}>
               <label class="form-check-label" for="remember">Remember Me</label>
            </div>  --}}
   		
            <div class="captcha-row">
               <div id="pagecaptcha-cont">
                  <div class="captcha">
                     <input type="checkbox" aria-label="Checkbox captcha" name="g-recaptcha-response">
                     <img alt="captcha" src="{{ asset('assets/imgs/captcha-img.png') }}" class="img-fluid" usemap="#image-map">
                     <map name="image-map">
                        <area target="_blank" alt="Privacy" title="Privacy" href="https://policies.google.com/privacy?hl=en" coords="" shape="rect" class="m-p">
                        <area target="_blank" alt="Terms" title="Terms" href="https://policies.google.com/terms?hl=en" coords="" shape="rect" class="m-t">
                     </map>
                  </div>
               </div>
               @if($errors->any())
                  @foreach ($errors->all() as $error)
                     <p class="error_message">{{ $error }}</p>
                  @endforeach
               @endif

               @if(session('status') === 'error')
                   <p class="error_message">
                       {{ session('msg') }}
                   </p>
               @endif
            </div> 
            <button class="btn btn-lg btn-primary btn-block mb-0" type="submit" style="font-weight: 600;">{{ __('Login') }}</button>

            <div style="text-align: center; display: none" class="mt-1" id="forgetpass">
               <a  href="{{ route('password.request') }}" >Forgot Password</a>
            </div>            
         </form>
      </div>

      <div class="text-center mt-4">
         <p style="color:#333;font-size: 12px;" class="mb-2">©2025 CISCE, All rights reserved. <span><a href="{{ route('privacy.policy')}}">Privacy Policy</a></span></p>
         <p style="color:#333;font-size: 12px;" class="mb-3">powered by <img src="{{ asset('logo/fitness365-logo-web.jpg') }}" alt="Fitness 365" class="img-fluid" style="max-width: 90px; height: 28px;">
         </p>
      </div>
</main>

   
<!-- </div> -->
@endsection

@push('icse-footer-script')
<script src="{{ asset('resources/js/jquery.min.js') }}"></script>


<script>
$(document).ready(function () {

   function toggleLoginFields(userType) {

      $('#userid_input').val('');
      $('#birth_date').val('');
      $('#email_input').val('');
      $('#password_input').val('');
      $('#forgetpass').hide();


      if (userType === 'Parent') {
         // Show parent login (User ID + DOB)
         $('#user_id_box').show();
         $('#userid_input').prop('required', true).prop('disabled', false);
         $('#birth_date').prop('required', true).prop('disabled', false);

         // Hide email login
         $('#email_box').hide();
         $('#email_input').prop('required', false).prop('disabled', true);
         $('#password').prop('required', false).prop('disabled', true);
      } else {
         // Show email login
         $('#email_box').show();
         $('#email_input').prop('required', true).prop('disabled', false);
         $('#password').prop('required', true).prop('disabled', false);
         $('#email_input').attr('placeholder', 'Enter Email');

         if(userType === 'School'){
            $('#forgetpass').show();
            $('#email_input').attr('placeholder', 'Enter Email (Head.SchoolCode@cisceschools.org)');
         }
         
         // Hide parent login
         $('#user_id_box').hide();
         $('#userid_input').prop('required', false).prop('disabled', true);
         $('#birth_date').prop('required', false).prop('disabled', true);
      }
   }

   const savedOption = localStorage.getItem('selectedOption') || 'School';

   $('.option_type').val(savedOption);
   $('#login_type').val(savedOption);
   toggleLoginFields(savedOption);

   $('#login_type').on('change', function () {
      const selected = $(this).val();
      $('.option_type').val(selected);
      localStorage.setItem('selectedOption', selected);
      toggleLoginFields(selected);
   });
});
</script>
@endpush
