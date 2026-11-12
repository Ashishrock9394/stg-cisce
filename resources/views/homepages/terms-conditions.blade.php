@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')
<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="datatable-actions-outside d-flex justify-content-between align-items-center mb-3">
            <div class="all-chaptr-cards mb-4" style="margin: 0;">
                <div class="row">
                    <x:back-button title="{!! $title !!}" />
                </div>
            </div>            
        </div>

        <div>
            this is term and condition page
        </div>
    </div>            
</div>
@endsection