@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

@section('content')


<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class=" all-chaptr-cards" style="margin: 0;">
            <div class="row">
                <div class="col">
                    <div class="heading-rw mt-0 mt-md-1 mb-0 p-0">
                        @if(auth()->guard('web')->check())

                        <a href="{{ route('all-test') }}" class="back-button">
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                </svg>
                            </span>
                        </a>

                        @elseif(auth()->guard('sstudent')->check())

                            <a href="{{ route('student.dashboard') }}" class="back-button">
                                <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                        </svg>
                                </span>
                            </a>
                        @endif                        
                        <h1 class="mt-2 mt-md-0 ml-md-4 mb-0">{{$title}}</h1>                        
                    </div>
                </div>
            </div>

            <div class="row text-center justify-content-md-center">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="all-tests mb-5">                         
                        <br><h4 class="test-cat">Physical Fitness Assessment for CWSN</h4>
                        <ul class="list-group mt-0">
                            @foreach($mappedCategories as $keys => $vals)
                                <li>
                                    <a href="{{ route('assessor.cwsn.test', ['pwd_category_id' => $pwd_category_id, 'test_category_id' => $vals->TestCategoryID]) }}"><span>{{ $vals->TestCategoryName }} </span><span class="arrow-i"><i class="bi bi-arrow-right"></i></span></a>
                                </li>
                            @endforeach                        
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection