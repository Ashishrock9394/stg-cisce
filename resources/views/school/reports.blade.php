@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
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

				<iframe src="{{ $encrypt_url }}" title="Paris 2024" style="border:none; width:100%; height:calc(100vh - 82px);" id="iFrame1"></iframe>

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