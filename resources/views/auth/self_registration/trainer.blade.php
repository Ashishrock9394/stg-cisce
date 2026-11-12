

<form id="trainerSelfRegistrationForm" method="POST" action="{{ route('register.trainer') }}">
    @csrf

    <h5 class="card-title text-center mb-0">Add Trainer</h5>                        
    <div class="form-row">      

        <div class="form-group col-md-9">
            <label for="trainerName">Fullname <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('trainerName') is-invalid @enderror" id="trainerName" name="trainerName" value="{{ old('trainerName') }}" placeholder="Your name">
            @error('trainerName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="gender">Gender <span style="color: red; font-weight: bold;" >*</span></label>
            <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror">
                <option disabled {{ old('gender') ? '' : 'selected' }}>Choose...</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Transgender" {{ old('gender') == 'Transgender' ? 'selected' : '' }}>Transgender</option>
            </select>
            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="qualification">Qualification <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('qualification') is-invalid @enderror" name="qualification" id="qualification" placeholder="Type here" value="{{ old('qualification') }}">
            @error('qualification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-5">
            <label for="inputEmail4">Email <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="trainerEmail" name="email" placeholder="sc_bosh@gmail.com" value="{{ old('email') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="inputPassword4">Mobile <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="number" class="form-control @error('mobile') is-invalid @enderror" id="number" name="mobile" placeholder="+91 00000 00000" value="{{ old('mobile') }}">
            @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="form-row">

        <div class="form-group col-sm-6 col-md-6 col-lg-3">
            <label for="selectState">State <span style="color: red; font-weight: bold;" >*</span></label>
            <select id="selectState" name="state" class="form-control @error('state') is-invalid @enderror">
                <option selected>Select</option>
                @foreach($state_list as $list)
                    <option value="{{ $list->id }}" {{ old('state') ==  $list->id ? 'selected' : '' }} >{{ $list->name }}</option>
                @endforeach
            </select>
            @error('state')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group col-sm-6 col-md-6 col-lg-3">
            <label for="selectDistrict">District <span style="color: red; font-weight: bold;" >*</span></label>
            <select id="selectDistrict" name="district" class="form-control @error('district') is-invalid @enderror">
                <option selected>Select</option>
            </select>
            @error('district')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        

        <div class="form-group col-sm-6 col-md-6 col-lg-3">
            <label for="inputPassword4">Block <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('block') is-invalid @enderror" id="number" name="block" placeholder="" value="{{ old('block') }}">
            @error('block')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group col-sm-6 col-md-6 col-lg-3">
            <label for="selectCity">City <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" placeholder="" value="{{ old('city') }}">
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        
    </div>

    <!-- Address -->


    <div class="form-row">
        <div class="form-group col-sm-6 col-md-6 col-lg-9">
            <label for="schoolAddress">Address <span style="color: red; font-weight: bold;" >*</span></label>
            <textarea id="schoolAddress" name="address" rows="2" class="form-control  @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-sm-6 col-md-6 col-lg-3">
            <label for="schoolAddress">DOB <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="date" name="dob" id="dob" class="form-control  @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
            @error('dob')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>


    <div class="">

        <div class="form-check">
            <input class="form-check-input @error('privacy_policy') is-invalid @enderror" type="checkbox" id="privacy_policy" name="privacy_policy" value="1"  {{ old('privacy_policy') ? 'checked' : '' }} >
            <label class="form-check-label" for="privacy_policy">
                I am accepting terms and conditions and privacy policy (i) <span style="color: red; font-weight: bold;" >*</span>
            </label>
            @error('privacy_policy')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class=" mb-0">
        <div class="form-check">
            <input class="form-check-input @error('term_condition') is-invalid @enderror" type="checkbox" id="term_condition" name="term_condition" value="1" {{ old('term_condition') ? 'checked' : '' }}>

            <label class="form-check-label" for="term_condition">
                I hereby affirm that I'm more than 18 years of age and the information given by me is true and correct. <span style="color: red; font-weight: bold;" >*</span>
            </label>

            @error('term_condition')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

   
    <div class=" mb-0">
        <div class="form-check">
            <input class="form-check-input " type="checkbox" id="marketing_consent" name="marketing_consent" value="1" {{ old('marketing_consent') ? 'checked' : '' }} checked>
            <label class="form-check-label" for="marketing_consent">I agree to receive newsletters, or marketing and promotional content. <span style="color:red; font-weight:bold;" >*</span>
            </label>
            @error('marketing_consent')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
   
    <!-- Submit -->
    <div class="d-flex justify-content-end">
        <!-- <a type="button" class="btn btn-secondary mr-2" ><span>Cancel</span></a> -->
        <button type="submit" class="btn btn-primary"><span>Submit</span></button>
    </div>
</form>


{{--
@if(session('registration_success'))
    <!-- Success Modal -->



    <div class="modal fade" id="registrationSuccessModal" tabindex="-1" role="dialog" aria-labelledby="registrationSuccessLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-success">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="registrationSuccessLabel">Registration Successful</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <p>Thank you for registering!</p>
            <p><strong>Your Registration Number:</strong></p>
            <h4 class="text-success font-weight-bold">{{ session('registration_success') }}</h4>
            <p>Please wait for approval from the School.</p>
          </div>
          <div class="modal-footer justify-content-center">
            <a href="{{ route('login') }}" class="btn btn-success">Go to Login</a>
          </div>
        </div>
      </div>
    </div>
@endif
--}}

<script>


  $(document).ready(function () {
    const today = new Date().toISOString().split("T")[0];
    $('#dob').attr('max', today);


    $('#dobForm').submit(function (e) {
      const dob = new Date($('#dob').val());
      const now = new Date();

      dob.setHours(0, 0, 0, 0);
      now.setHours(0, 0, 0, 0);

      if (dob > now) {
        alert("Date of birth cannot be in the future.");
        e.preventDefault(); // Stop form submission
      }
    });
  });


$(document).ready(function() {


    $('#registrationSuccessModal').modal('show');

    var oldState = '{{ old("state") }}';
    var oldDistrict = '{{ old("district") }}';

    if (oldState) {
        $('#selectState').val(oldState); 

        $.ajax({
            type: 'POST',
            url: "{{ route('school.getDistrict') }}",
            data: {
                stateId: oldState,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(response) {
                $('#selectDistrict').empty().append('<option>Select</option>');
                
                $.each(response, function (key, value) {
                    $('#selectDistrict').append('<option value="' + value.id + '">' + value.name + '</option>');
                });

                if (oldDistrict) {
                    $('#selectDistrict').val(oldDistrict);
                }
            },

        });
    }

    $('#selectState').on('change', function() {
        const stateId = $(this).val();
        const selectDistrict = $('#selectDistrict');
        selectDistrict.empty().append('<option value="">Select District</option>');

        if (stateId) {
            $.ajax({
                type: 'POST',
                url: "{{ route('school.getDistrict') }}",
                data: {
                    stateId: stateId,
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',

                success: function(response) {
                    $.each(response, function(index, city) {
                        selectDistrict.append(
                            $('<option>', {
                                value: city.id,
                                text: city.name
                            })
                        );
                    });
                },

                error: function(xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    $('#message').text('Error loading districts!');
                }
            });
        }
    });
});

</script>
