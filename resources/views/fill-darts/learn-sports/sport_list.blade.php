@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)
@section('content')

<?php $sty1 = 'display:block'; ?>
        <div class="all-chaptr-cards mt-5">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12">
                    <div class="heading-rw mt-3 mt-md-1 mb-0 p-0">
                        @if(auth()->guard('web')->check())
                        <a href="{{route('filldart.dashboard')}}" class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg></span> 
                        </a>
                        @elseif(auth()->guard('sstudent')->check())
                        <a href="{{route('student.dashboard')}}" class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg></span> 
                        </a>
                        @endif

                        <h1 class="heading-rw mt-md-0 mt-0 mb-3 mb-0 pt-md-0 pt-0">{{$title}}</h1>
                    </div>
                </div>
            </div>

            <div class="form-row mt-4 mt-md-5 mb-5 sports-list" style="justify-content: center;">
            @foreach($sports as $sport)
                <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
                    <a href="{{ route('sports.videos', $sport->id) }}" target="_self" class="img-grid">
                        <span>{{ $sport->name }}</span>
                        <img src="{{ asset('public/change-sports/' . $sport->img) }}" class="img-fluid rounded" alt="{{ $sport->name }}">
                    </a>
                </div>
            @endforeach
            </div>
        </div>
    </div>




@endsection