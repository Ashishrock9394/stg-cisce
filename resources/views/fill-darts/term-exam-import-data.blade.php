@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)
@section('content')

<?php $sty1 = 'display:block'; ?>

<div class="container">
    <div class="t-mrg2">
        <div class="container-fluid all-chaptr-cards filter-bx">
            <div class="row">
                <div class="col">
                    <a href="#a" onclick="history.back()" class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg></span>
                        <!-- <span class="back-txt">Back</span> -->
                    </a>
                    <div class="heading-rw mt-0">
                        <h1>{{$title}}</h1>
                    </div>
                </div>
            </div>

            
		@if ($errors->any())
		<div class="alert alert-danger" id="form-error-message">
			<ul>
				@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif



		@if(session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
		@endif

		<form action="{{ route('terms.exam.import.data') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="form-group">
				<label for="file">Choose Excel File</label>
				<input type="file" name="file" id="file" class="form-control">
			</div>
			<button type="submit" class="btn btn-primary">Import</button>
		</form>
			
	



	 </div>
    </div>
</div>
</div>







<script>
    $(document).ready(function() {
		//
    });

</script>



@endsection