@extends('layouts.filldart-app')
@section('title','Edit Profile')
@section('content')
<!--<style>
.center{
	width:40%;
	margin:0 auto;
	align-items: center; 
}
</style>-->
<div class="wrapper">


	<div id="content">



		<div class="card card-primary">
			@if (session('msg'))
			<div class="alert alert-success">
				{{ session('msg') }}
			</div>
			@endif
			@if ($errors->any())
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			@endif

		</div>
		<form method="POST" action="{{ url('update-profile') }}/{{ Auth::user()->id }}">
			@csrf
			@method('PUT')
			<input type="hidden" name="user_id" value="{{ $result->id }}">
			<div class="container">
				<div class="row">

					<div class="col-12 col-md-10 offset-md-1 col-lg-6 offset-lg-3 my-5">

						<div class="mb-4" data-card-widget="collapse" title="Collapse">
							<h3 class="card-title">Update Profile</h3>
						</div>
						<!-- <div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-6  col-form-label px-0">Region</label>
										<select id="region" name="region" aria-required="true"
											class="form-control @error('region') is-invalid @enderror" value="{{ old('region') }}">
											<option value="">Choose Region</option>
											<?php
											foreach ($regions as $region) {
												if ($region->id == $result->region_id) {
													$sel = 'selected';
												} else {
													$sel = '';
												}
											?>

												<option value="{{$region->id}}" <?= $sel ?>>{{ $region->name }}</option>
											<?php  } ?>
										</select>

										{!!$errors->first("region", "<span class='text-danger'>:message</span>")!!}
									</div>

								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="state" class="col-md-6  col-form-label px-0">School Name</label>

										<select class="form-control" name="school_id" class="form-control @error('school_id') is-invalid @enderror" aria-required="true">
											<option selected="selected">Select Schools</option>

											<?php
											foreach ($schools as $school) {
												if ($school->id == $result->school_id) {
													$ksel = 'selected';
												} else {
													$ksel = '';
												}
											?>
												<option value="{{$school->id}}" <?= $ksel ?>>{{ $school->school_name }} </option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div> -->

						<div class="form-group mb-0">
							<div class="form-row">

								<div class="col-md-6 mb-2">

									<label for="name" class="col-md-6 col-form-label px-0">{{ __('Name') }}</label>
									<input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$result->name}}" placeholder="name" required autocomplete="name" autofocus>

									@error('name')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>

								<div class="col-md-6 mb-2">

									<label for="email" class="col-md-6 col-form-label px-0">{{ __('Email') }}</label>


									<input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$result->email}}" placeholder="email" required autocomplete="email" autofocus readonly>

									@error('email')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror


								</div>

							</div>
						</div>
						<div class="form-group mb-0">
							<div class="form-row">
								<div class="col-6 col-md-3 mb-2">
									<label for="dob" class="col-md-6  col-form-label px-0">{{ __('DOB') }}</label>
									<input id="dob" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{$result->dob}}" placeholder="DOB" autocomplete="DOB" autofocus>

									@error('logo')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								<div class="col-6 col-md-3 mb-2">
									<label for="dob" class="col-md-6  col-form-label px-0">{{ __('Gender') }}</label>
									<select id="gender-id" name="gender" aria-required="true"
										class="form-control @error('gender') is-invalid @enderror" value="{{ old('gender') }}">
										<option value="">Select</option>
										<option value="male" {{$result->gender == 'male' ?'selected':''}}>Male</option>
										<option value="female" {{$result->gender == 'female' ?'selected':''}}>Female</option>
										<option value="other" {{$result->gender == 'other' ?'selected':''}}>Other</option>

									</select>


								</div>
								<div class="col-12 col-md-6 mb-2">

									<label for="phone" class="col-md-6 col-form-label px-0">{{ __('Mobile No.') }}</label>


									<input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{$result->phone}}" placeholder="Mobile No." required autocomplete="phone" autofocus>

									@error('phone')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror


								</div>
							</div>
						</div>

						<div class="form-group mb-0">
							<div class="form-row">
								<div class="col-md-6 mb-2">

									<label for="qualification" class="col-md-6  col-form-label px-0">{{ __('Highest Qualification') }}</label>

									<input id="qualification" type="text" class="form-control @error('qualification') is-invalid @enderror" name="qualification" value="{{$result->qualification}}" placeholder="Enter Qualification" required autocomplete="qualification" autofocus>

									@error('qualification')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror


								</div>
								<div class="col-md-6 mb-2">
									<label for="experience" class="col-md-6 col-form-label px-0">{{ __('Experience') }}</label>
									<input id="experience" type="text" class="form-control @error('experience') is-invalid @enderror" name="experience" value="{{$result->experience}}" placeholder="Experience in numbers" required autocomplete="experience" autofocus>

									@error('experience')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								<!-- <div class="col-md-6">
									<div class="form-group">
										<label class="col-md-6  col-form-label px-0">Subject</label>
										<select id="subject" name="subject" aria-required="true"
											class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}">
											<option value="">Choose Subject</option>
											<?php
										/*	foreach ($subjects as $subject) {
												if (strtolower($subject->name) == strtolower($result->subject)) {
													$sel = 'selected';
												} else {
													$sel = '';
												}
											?>

												<option value="{{$subject->name}}" <?= $sel ?>>{{$subject->name }}</option>
											<?php  } */ ?>
										</select>

										{!!$errors->first("subject", "<span class='text-danger'>:message</span>")!!}
									</div>

								</div> -->

							</div>
						</div>






						<!-- @if(\Auth::user()->role_id == 4)
						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<label for="logo" class="col-md-6  col-form-label px-0">{{ __('Logo') }}</label>
									<input id="logo" type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" placeholder="Logo">

									@error('logo')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
							</div>
						</div>
						@endif -->




						<div class="clearfix"><br></div>
						<div class="form-group mb-0">
							<div class="action-btns add-act-btn-div justify-content-end">
								<input type="reset" value="Cancel" class="add-act-link btn btn-md btn-secondary">
								<button type="submit" class="add-act-link btn btn-md btn-primary add-act-btn mr-2">Update</button>
							</div>
						</div>


					</div>
				</div>
			</div>
		</form>



	</div>
</div>



@endsection