@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<div class="container">
    <div class="t-mrg">

        <div class="row text-center justify-content-md-center">
            <div class="col-12 col-md-10 col-lg-10">
                <div class="form-row" style="justify-content: center;">                                      

                    <div class="col-4 col-md-3 col-lg-3 col-xl-2 mb-4">
                        <a href="{{ route('students.view.dashboard')}}" class="box">
                            <div >
                                <img class="img-fluid" alt="" src="{{asset('assets/icons/Dashboard.svg') }}">
                            </div>
                            <span >Dashboard</span>
                        </a>
                    </div>
                    
                    @php
                        $reportRoute = $student->is_pwd === '1'  ? route('cwsn.reports.view') : route('reports.view');
                    @endphp 

                    <div class="col-4 col-md-3 col-lg-3 col-xl-2 mb-4">
                        <a href="{{ $reportRoute }}" target="_blank" class="box">
                            <div>
                                <img class="img-fluid" alt="" src="{{asset('assets/icons/taketest.svg') }}">
                            </div>
                            <span>Progress Report</span>
                        </a>
                    </div>

                    <div class="col-4 col-md-3 col-lg-3 col-xl-2 mb-4">
                        <a href="{{ route('test_videos') }}" class="box"><div>
						<img class="img-fluid" alt="" src="{{asset('assets/icons/test-demo.svg') }}"></div><span>
						Battery of Tests</span></a>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>


@endsection