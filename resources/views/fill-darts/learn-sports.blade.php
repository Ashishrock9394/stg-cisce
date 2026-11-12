@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)
@section('content')

<?php $sty1 = 'display:block'; ?>

<div class="container">
    <div class="t-mrg2">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                     @if(auth()->guard('web')->check())

                    <a href="{{ route('filldart.dashboard') }}" class="back-button"> <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" /> </svg></span> </a>

                    @elseif(auth()->guard('sstudent')->check())                      

                    <a href="{{ route('student.dashboard') }}" class="back-button"> <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" /> </svg></span> </a>
                    @endif

                    <div class="heading-rw mt-0 mb-2 px-5">
                        <h1>{{$title}}</h1>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-5 sports-list" style="justify-content: center;">
            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/archery/" target="_self" class="img-grid"><span>archery</span><img src="{{ asset('public/change-sports/archery-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/athletics/" target="_self" class="img-grid"><span>athletics</span><img src="{{ asset('public/change-sports/athletics-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/badminton/" target="_self" class="img-grid"><span>Badminton</span><img src="{{ asset('public/change-sports/Badminton-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/basketball/" target="_self" class="img-grid"><span>Basketball</span><img src="{{ asset('public/change-sports/Basketball-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/boxing/" target="_self" class="img-grid"><span>boxing</span><img src="{{ asset('public/change-sports/boxing-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/cricket/" target="_self" class="img-grid"><span>Cricket</span><img src="{{ asset('public/change-sports/Cricket-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/football/" target="_self" class="img-grid"><span>Football</span><img src="{{ asset('public/change-sports/Football-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/gymnastics/" target="_self" class="img-grid"><span>gymnastics</span><img src="{{ asset('public/change-sports/gymnastics-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/handball/" target="_self"  class="img-grid"><span>handBall</span><img src="{{ asset('public/change-sports/handBall-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/hockey/" target="_self" class="img-grid"><span>Hockey</span><img src="{{ asset('public/change-sports/Hockey-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/judo/" target="_self" class="img-grid"><span>judo</span><img src="{{ asset('public/change-sports/judo-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/kabaddi/" target="_self" class="img-grid"><span>Kabaddi</span><img src="{{ asset('public/change-sports/Kabaddi-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/kho-kho/" target="_self" class="img-grid"><span>Kho-Kho</span><img src="{{ asset('public/change-sports/Kho-Kho-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>
            
            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="#a" target="_self" class="img-grid"><span>shooting</span><img src="{{ asset('public/change-sports/shooting-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/swimming/" target="_self" class="img-grid"><span>swimming</span><img src="{{ asset('public/change-sports/swimming-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/table-tennis/" target="_self" class="img-grid"><span>Table tennis</span><img src="{{ asset('public/change-sports/Table-tennis-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/tennis/" target="_self" class="img-grid"><span>Tennis</span><img src="{{ asset('public/change-sports/Tennis-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/volleyball/" target="_self" class="img-grid"><span>Volleyball</span><img src="{{ asset('public/change-sports/Volleyball-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/weightlifting" target="_self" class="img-grid"><span>weightlifting</span><img src="{{ asset('public/change-sports/weightlifting-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/wrestling" target="_self" class="img-grid"><span>wrestling</span><img src="{{ asset('public/change-sports/wrestling-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/sports-rules/" target="_self" class="img-grid"><span>Sports Rules</span><img src="{{ asset('public/change-sports/sportsrule-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

            <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4"><a href="https://goforfit.in/genre/indigenous-sports/" target="_self" class="img-grid"><span>Indigenous Games</span><img src="{{ asset('public/change-sports/tugwar-thumb.jpg') }}" class="img-fluid rounded" alt=""></a></div>

        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $('a.img-grid').click(function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var namedWindow = window.open('', 'myNamedWindow');
        namedWindow.location.href = url;
    });
});
</script>





@endsection