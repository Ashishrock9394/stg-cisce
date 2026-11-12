@extends('layouts.filldart-login-app-header')
@section('title', 'CISCE | ' . $title)
@section('content')

    <style>
        .preview-img {
            max-width: 100px;
            height: auto;
        }

        .cred-bx {
            background-color: white;
            padding: 10px 0;
        }
    </style>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="cred-bx text-center">
        <a href="/">
        <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" width="100" height="60"
            class="d-inline-block align-top mb-2" alt="logo"></a>
    </div>
    <div class="container">
        <div class="t-mrg2 mb-5 pb-5">
            <!-- <div class="text-center mb-3">
                <button class="btn btn-primary toggle-btn active" id="schoolBtn">School</button>
                <button class="btn btn-secondary toggle-btn" id="trainerBtn">Trainer</button>
            </div> -->
            <div class="row school-registraion">
                <div class="col-12">
                    <div class="all-chaptr-cards filter-bx mt-4">
                        <h5 class="card-title text-center mb-0">School Registration Form</h5>
                        <div id="schoolFormSection">
                            <form id="schoolRegistration" method="post" enctype="multipart/form-data">
                                @csrf

                                <!-- School Details -->
                                <h5 class="mb-3 text-primary">🏫 School Details</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="schoolCode">School Code (U-DISE Code) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="schoolCode" name="school_code"
                                            placeholder="Enter School Code" required>
                                        <span class="text-danger error-text school_code_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="schoolName">School Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="schoolName" name="school_name"
                                            placeholder="Enter School Name" required>
                                        <span class="text-danger error-text school_name_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="schoolEmail">School Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="schoolEmail" name="school_email"
                                            placeholder="school@example.com" required>
                                        <span class="text-danger error-text school_email_error"></span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="shift">Shift <span class="text-danger">*</span></label>
                                        <select class="form-control" id="shift" name="shift" required>
                                            <option value="">-- Select Shift --</option>
                                            <option value="Day">Morning</option>
                                            <option value="Day/Residential">Residential</option>

                                        </select>
                                        <span class="text-danger error-text shift_error"></span>
                                    </div>
                                </div>

                                <!-- Principal Details -->
                                <h5 class="mb-3 mt-4 text-primary">👨‍🏫 Principal Details</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="principalName">Principal Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="principalName" name="principal_name"
                                            placeholder="Enter Principal Name" required>
                                        <span class="text-danger error-text principal_name_error"></span>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="principalEmail">Principal Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="principalEmail"
                                            name="principal_email" placeholder="principal@example.com" required>
                                        <span class="text-danger error-text principal_email_error"></span>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="principalMobile">Principal Mobile *</label>
                                        <input type="text" class="form-control" id="principalMobile"
                                            name="principal_mobile" placeholder="9876543210" required>
                                        <span class="text-danger error-text principal_mobile_error"></span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="position">Position <span class="text-danger">*</span></label>
                                        <select class="form-control" name="position" id="position" required>
                                            <option value="Principal">Principal</option>
                                            {{-- <option value="HM">HM</option> --}}
                                        </select>
                                        <span class="text-danger error-text position_error"></span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="gender">gender <span class="text-danger">*</span></label>
                                        <select class="form-control" name="gender" id="gender" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <span class="text-danger error-text gender_error"></span>
                                    </div>
                                </div>

                                <!-- Location Details -->
                                <h5 class="mb-3 mt-4 text-primary">📍 Location Details</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="region">Region Name</label>
                                        <select class="form-control" id="region" name="region" required>
                                            <option value="">-- Select Region --</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->name }}|{{ $region->id }}">
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-text region_error"></span>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="schoolState">State <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <select id="schoolState" name="state"
                                            class="form-control @error('state') is-invalid @enderror">
                                            <option value="">-- Select State --</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id ?? '' }}|{{ $state->name }}">
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="schoolDistrict">District <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <select id="schoolDistrict" name="district"
                                            class="form-control @error('district') is-invalid @enderror">
                                            <option value="">-- Select District --</option>
                                        </select>
                                        @error('district')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="pincode">Pincode</label>
                                        <input type="text" class="form-control" id="pincode" name="pincode"
                                            inputmode="numeric" maxlength="6" pattern="\d{6}"
                                            placeholder="Enter Pincode">
                                    </div>

                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="zone">Zone Name</label>
                                        <input type="text" class="form-control" id="zone" name="zone"
                                            placeholder="Enter Zone">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            placeholder="Enter City">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            placeholder="Enter Address">
                                    </div>

                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-end mt-3">
                                    <a type="button" class="btn btn-secondary mr-2" href="/">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>

                        <div id="trainerFormSection" style="display: none;">

                            <form id="trainerRegistration" method="POST" enctype="multipart/form-data">
                                @csrf

                                <h5 class="mb-3 mt-4 text-primary">👨‍🏫 Trainer's Details</h5>

                                <div class="form-row">

                                    <div class="form-group col-md-4">
                                        <label for="trainerName">Trainer Name <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <input type="text" class="form-control" id="trainerName" name="trainer_name"
                                            required>
                                        <span class="text-danger error-text trainer_name_error"></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="trainerEmail">Trainer Email <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <input type="email" class="form-control" id="trainerEmail"
                                            name="trainer_email" required>
                                        <span class="text-danger error-text trainer_email_error"></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="trainerMobile">Trainer Mobile <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <input type="text" class="form-control" id="trainerMobile"
                                            name="trainer_mobile" required>
                                        <span class="text-danger error-text trainer_mobile_error"></span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="trainerGender">Gender <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <select class="form-control" name="gender" id="trainerGender" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <span class="text-danger error-text gender_error"></span>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="trainerDob">DOB <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <input type="date" class="form-control" id="trainerDob" name="trainer_dob"
                                            required>
                                        <span class="text-danger error-text trainer_dob_error"></span>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="qualification">Qualification <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <input type="text" class="form-control" id="qualification"
                                            name="qualification" required>
                                        <span class="text-danger error-text qualification_error"></span>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="experience">Experience</label>
                                        <input type="text" class="form-control" id="experience" name="experience"
                                            required>
                                        <span class="text-danger error-text experience_error"></span>
                                    </div>

                                </div>

                                <!-- Location -->
                                <h5 class="mb-3 mt-4 text-primary">📍 Location Details</h5>

                                <div class="form-row">

                                    <div class="form-group col-md-3">
                                        <label for="trainerRegion">Region</label>
                                        <select class="form-control" id="trainerRegion" name="region" required>
                                            <option value="">-- Select Region --</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->name }}|{{ $region->id }}">
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="trainerState">State <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <select id="trainerState" name="state" class="form-control">
                                            <option value="">-- Select State --</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id ?? '' }}|{{ $state->name }}">
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="trainerDistrict">District <span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <select id="trainerDistrict" name="district" class="form-control">
                                            <option value="">-- Select District --</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="trainerPincode">Pincode<span
                                                style="color: red; font-weight: bold;">*</span></label>
                                        <input type="text" class="form-control" id="trainerPincode" name="pincode" inputmode="numeric" maxlength="6" pattern="\d{6}"
                                            placeholder="Enter Pincode" require>
                                    </div>
                                </div>
                                <div class="form-row">
                                
                                    <div class="form-group col-md-4">
                                        <label for="trainerZone">Zone</label>
                                        <input type="text" class="form-control" id="trainerZone" name="zone">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="trainerCity">City</label>
                                        <input type="text" class="form-control" id="trainerCity" name="city">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="trainerAddress">Address</label>
                                        <input type="text" class="form-control" id="trainerAddress" name="address">
                                    </div>

                                </div>

                                <!-- CHECKBOXES -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="trainer_privacy"
                                        name="privacy_policy" value="1">
                                    <label class="form-check-label" for="trainer_privacy">
                                        I am accepting terms and conditions and privacy policy (i) <span style="color: red; font-weight: bold;" >*</span>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="trainer_terms"
                                        name="term_condition" value="1">
                                    <label class="form-check-label" for="trainer_terms">
                                        I hereby affirm that I'm more than 18 years of age and the information given by me is true and correct. <span style="color: red; font-weight: bold;" >*</span>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="trainer_marketing"
                                        name="marketing_consent" value="1" checked>
                                    <label class="form-check-label" for="trainer_marketing">
                                        I agree to receive newsletters, or marketing and promotional content. <span style="color:red; font-weight:bold;" >*</span>
                                    </label>
                                </div>

                                <!-- SUBMIT -->
                                <div class="d-flex justify-content-end mt-3">
                                    <a type="button" class="btn btn-secondary mr-2" href="/">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>




        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $("#schoolBtn").click(function() {
                $("#schoolFormSection").show();
                $("#trainerFormSection").hide();

                $("#schoolBtn").addClass("btn-primary active").removeClass("btn-secondary");
                $("#trainerBtn").addClass("btn-secondary").removeClass("btn-primary active");
            });

            $("#trainerBtn").click(function() {
                $("#schoolFormSection").hide();
                $("#trainerFormSection").show();

                $("#trainerBtn").addClass("btn-primary active").removeClass("btn-secondary");
                $("#schoolBtn").addClass("btn-secondary").removeClass("btn-primary active");
            });

            function loadDistrict(stateId, dropdown) {
                dropdown.empty().append('<option value="">Select District</option>');

                if (!stateId) return;

                $.ajax({
                    type: 'POST',
                    url: "{{ route('school.getDistrict') }}",
                    data: {
                        stateId: stateId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $.each(response, function(index, district) {
                            dropdown.append(
                                `<option value="${district.id}|${district.name}">${district.name}</option>`
                            );
                        });
                    }
                });
            }

            // SCHOOL
            $("#schoolState").change(function() {
                loadDistrict($(this).val(), $("#schoolDistrict"));
            });

            // TRAINER
            $("#trainerState").change(function() {
                loadDistrict($(this).val(), $("#trainerDistrict"));
            });

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            // Schools registration

            $("#schoolRegistration").on("submit", function(e) {
                e.preventDefault();
                $(".error-text").text("");

                $.ajax({
                    url: "{{ route('register.school.self') }}",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: "json",

                    success: function(response) {
                        if (response.status === 0) {
                            $.each(response.errors, function(key, val) {
                                $("." + key + "_error").text(val[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: "success",
                                title: "Registration Successful",
                                html: `
                                    <p><strong>${response.message}</strong></p>
                                    <hr>
                                    <p><b>User ID:</b> ${response.credentials.user_id}</p>
                                    <p><b>Password:</b> ${response.credentials.password}</p>
                                    <hr>
                                    <p style="font-size:13px;">
                                        Please contact the administration for activation of the school.
                                    </p>
                                `,
                                confirmButtonText: "OK",
                                allowOutsideClick: false
                            });

                            $("#schoolRegistration")[0].reset();
                        }
                    },

                    error: function(xhr) {
                        console.log("Error Response:", xhr.responseText);

                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: xhr.responseJSON?.message || "Something went wrong! Please try again."
                        });
                    }
                });
            });

            // Trainers registration 

            $("#trainerRegistration").on("submit", function(e) {
                e.preventDefault();
                $(".error-text").text("");

                $.ajax({
                    url: "{{ route('register.trainer.self') }}",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: "json",

                    success: function(response) {
                        if (response.status === 0) {
                            $.each(response.errors, function(key, val) {
                                $("." + key + "_error").text(val[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: "success",
                                title: "Registration Successful",
                                html: `
                                    <p><strong>${response.message}</strong></p>
                                    <hr>
                                    <p><b>User ID:</b> ${response.credentials.user_id}</p>
                                    <p><b>Password:</b> ${response.credentials.password}</p>
                                    <p><b>Self-Reg. ID:</b> ${response.credentials.password}</p>
                                    <hr>
                                    <p style="font-size:13px;">
                                        Please contact school for mapping.
                                    </p>
                                `,
                                confirmButtonText: "OK",
                                allowOutsideClick: false
                            });

                            $("#schoolRegistration")[0].reset();
                        }
                    },

                    error: function(xhr) {
                        console.log("Error Response:", xhr.responseText);

                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: xhr.responseJSON?.message || "Something went wrong! Please try again."
                        });
                    }
                });
            });
        });
    </script>


@endsection
