@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


@if (session('warning') || isset($warning))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Browser Recommendation',
            width: '600px',
            html: `
            <div style="background-color:#f8f9fa; border:1px solid #ddd; border-radius:8px; padding:16px; max-width:470px; margin:auto; font-family:Arial, sans-serif; color:#333;">
                    <strong>{{ $warning ?? session('warning') }}</strong>
                    <ul style="margin:0 0 10px 0; text-align:left; padding:5px;">
                        <li class="pb-2">For the best experience, please use <strong>Google Chrome</strong> as your browser.</li>
                        <li class="pb-2">Make sure that <strong>JavaScript</strong> and <strong>cookies</strong> are enabled.</li>
                    </ul>

                    <div style="margin-top:15px; align-item:center;">
                        <p style="margin:0 0 8px 0; font-weight:500;">Download Google Chrome</p>

                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;">

                            <!-- Play Store -->
                            <a href="https://play.google.com/store/apps/details?id=com.android.chrome" target="_blank"
                            style="display:inline-flex; align-items:center; text-decoration:none;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                                    alt="Download on Google Play" style="height:35px;">
                            </a>

                            <!-- App Store -->
                            <a href="https://apps.apple.com/app/google-chrome/id535886823" target="_blank"
                            style="display:inline-flex; align-items:center; text-decoration:none;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Download_on_the_App_Store_RGB_blk.svg/2560px-Download_on_the_App_Store_RGB_blk.svg.png"
                                    alt="Download on App Store" style="height:35px;">
                            </a>

                            <!-- Desktop Chrome -->
                            <a href="https://www.google.com/chrome/" target="_blank"
                            style="display:inline-flex; align-items:center; text-decoration:none;">
                                <img src="https://www.google.com/chrome/static/images/fallback/chrome-logo-2023.png"
                                    alt="Get Chrome for Desktop" style="height:35px; border-radius:6px;">
                            </a>

                        </div>
                    </div>
                </div>

            `,
            confirmButtonText: 'OK',

            confirmButtonColor: '#3085d6',
            allowOutsideClick: false,
        });
    </script>
@endif

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">

                        @if(auth()->guard('web')->check())

                        <a href="{{ route('filldart.dashboard') }}" class="back-button">
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                </svg></span> </a>

                        @elseif(auth()->guard('sstudent')->check())

                        <a href="{{ route('student.dashboard') }}" class="back-button">
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                </svg></span> </a>
                        @endif

                        <div class="heading-rw mt-2 mb-0 pt-0 pr-0 d-flex justify-content-between">
                            <h1 class="px-0 px-lg-0 mr-0">{{$title}}</h1>
                            <div class="btn-group toggle-btns" role="group" aria-label="Test Status Toggle">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-all" data-value="all">All</button>
                                <button type="button" class="btn btn-outline-primary btn-sm active" id="btn-remaining" data-value="remaining">Incomplete</button>
                            </div>
                        </div>
                    </div>
                </div>


                @include('assessor.categorisationlist')
       
                {{--
                <div class="row text-center justify-content-md-center mt-3 mt-lg-4">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="all-tests mb-5">
                            <h4 class="test-cat">Development Skills for Age 5-8 (Class 1-3)</h4>
                            <ul class="list-group mt-0">
                            
                            @foreach($juniorData as $key => $val)
                                <li>
                                    <a href="{{ route('assessor-app-test', ['TestcategoryId' => $val->TestCategoryID]) }}"><span>{{ $val->TestCategoryName }}</span><span class="arrow-i"><i class="bi bi-arrow-right"></i></span></a>
                                </li>
                            @endforeach     
                             
                           
                            </ul>
                            <br>
                            <h4 class="test-cat">Physical Fitness Assessment for Age 5-8 (Class 1-3)</h4>
                            <ul class="list-group mt-0">
                            
                            @foreach($juniorData1 as $key1 => $val1)
                                <li>
                                    <a href="{{ route('assessor.app.physical.test', ['TestcategoryId' => $val1->TestCategoryID]) }}"><span>{{ $val1->TestCategoryName }}</span><span class="arrow-i"><i class="bi bi-arrow-right"></i></span></a>
                                </li>
                            @endforeach 
                        
                            </ul>
                             <br>
                             <h4 class="test-cat">Physical Fitness Assessment for Age 9-18 (Class 4-12)</h4>
                            <ul class="list-group mt-0">
                            
                            @foreach($seniorData as $keys => $vals)
                                <li>
                                    @if($vals->TestCategoryID == 3) 
                                     <a href="{{ route('senior.bmi.test', ['TestcategoryId' => $vals->TestCategoryID, 'SeniorBMI'=>True]) }}"><span>{{ $vals->TestCategoryName }} </span><span class="arrow-i"><i class="bi bi-arrow-right"></i></span></a>
                                    @else
                                    <a href="{{ route('assessor.app.physical.test', ['TestcategoryId' => $vals->TestCategoryID]) }}"><span>{{ $vals->TestCategoryName }} </span><span class="arrow-i"><i class="bi bi-arrow-right"></i></span></a>  
                                    @endif                                   

                                </li>
                            @endforeach

                            </ul>

                          
                           <br>
                            <h4 class="test-cat">Physical Fitness Assessment for CWSN (Class 5-12)</h4>
                            <ul class="list-group mt-0">
                                @foreach($cwsnCategory as $category)
                                    @php $encrypted = encrypt($category->TestCategoryID); @endphp

                                    <li>
                                        <a 
                                        href="{{ route('assessor.cwsn.category', ['TestcategoryId' => $encrypted]) }}">
                                            <span>{{ $category->TestCategoryName }}</span>
                                            <span class="arrow-i"><i class="bi bi-arrow-right"></i></span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                          
                        </div>
                    </div>
                </div>
                --}}

            </div>
        </div>
    </div>



</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        localStorage.removeItem('selected_class');
        const buttons = document.querySelectorAll('[data-value]');
        const testStatusKey = "testStatus";

        // If no value is saved yet, default to "remaining" and store it
        let savedStatus = localStorage.getItem(testStatusKey);
        if (!savedStatus) {
            savedStatus = "remaining";
            localStorage.setItem(testStatusKey, savedStatus);
            console.log("Default testStatus set to:", savedStatus);
        }

        buttons.forEach(btn => {
            if (btn.dataset.value === savedStatus) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const selectedValue = this.dataset.value;
                localStorage.setItem(testStatusKey, selectedValue);

                buttons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                console.log("testStatus set to:", selectedValue);
            });
        });
    });
</script>


@endsection