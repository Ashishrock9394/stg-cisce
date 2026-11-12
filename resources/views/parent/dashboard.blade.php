@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')
<div class="pg-yallow-color">
    <div class="container">
        <div class="navbar-expand-lg">
            <div id="fillter" class="" role="group" aria-label="Basic example">
            </div>
        </div>
    </div>
</div>
<style>

</style>

<div class="container">
    <div class="t-mrg">


        <div class="row">
            <div class="col">
                <a href="#a" onclick="history.back()" class="back-button">
                    <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" /></svg></span>
                </a>
                <div class="heading-rw mt-0">
                    <h1>{{$title}}</h1>
                </div>
            </div>
        </div>


        <div class="row text-center justify-content-md-center">


            <div class="col-12 col-md-10 col-lg-10 offset-0 offset-md-0 offset-lg-0">
                <div class="form-row" style="justify-content: center;">                    

                    

                </div>
            </div>

        </div>

    </div>





</div>
</div>



@endsection