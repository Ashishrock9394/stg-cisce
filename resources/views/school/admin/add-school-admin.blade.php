<form id="school_users_form" method="POST" action="{{ route('store.admin') }}">
    @csrf

    <h5 class="card-title text-center mb-2">Add School Admin</h5>                  

    <div class="form-row">      
        <div class="form-group col-md-6">
            <label for="trainerName">Name <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('trainerName') is-invalid @enderror" id="trainerName" name="trainerName" value="{{ old('trainerName') }}" placeholder="Your name" required>
            @error('trainerName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="gender">Gender <span style="color: red; font-weight: bold;">*</span></label>
            <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Choose...</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Transgender" {{ old('gender') == 'Transgender' ? 'selected' : '' }}>Transgender</option>
            </select>

            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group col-md-3">
            <label for="designation">Designation <span style="color: red; font-weight: bold;">*</span></label>
            <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" placeholder="Enter designation" maxlength="20" required>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <!-- Qualification, Email, Mobile -->
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="qualification">Qualification <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('qualification') is-invalid @enderror" name="qualification" id="qualification" placeholder="Type here" value="{{ old('qualification') }}" required>
            @error('qualification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="trainerEmail">Email <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="trainerEmail" name="email" placeholder="sc_bosh@gmail.com" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="number">Mobile <span style="color: red; font-weight: bold;">*</span></label>
            <input 
                type="text" 
                class="form-control @error('mobile') is-invalid @enderror" 
                id="number" 
                name="mobile" 
                placeholder="Enter 10 digits mobile number" 
                value="{{ old('mobile') }}" 
                pattern="\d{10}" 
                maxlength="10" 
                required
            >
            @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group col-md-3">
            <label for="dob">DOB <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" 
                value="{{ old('dob') }}" 
                max="{{ date('Y-m-d', strtotime('-18 years')) }}" 
                required>
            @error('dob')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <!-- Module Access -->
    <div class="form-row">
        <div class="col-12">
            <h5 class="mb-3">Assign Module Access <span style="color: red; font-weight: bold;">*</span></h5>          
            <div class="row">
                @foreach($DashboardModule as $key => $label)
                    <div class="col-sm-6 col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" 
                            type="checkbox" 
                            name="module_access[]" 
                            id="roleId_{{ $label->role_id }}-{{ $key }}"
                            value="{{ $label->route_name }}" checked >
                            <label class="form-check-label" for="roleId_{{ $label->role_id }}-{{ $key }}"> &nbsp {{ $label->name }} </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('module_access')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror                                      
        </div>
    </div>

    <!-- Submit -->
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary"><span>Submit</span></button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#school_users_form').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = new FormData(this);
            let checkedModules = $('input[name="module_access[]"]:checked');
            let viewerName = $('input[name="trainerName"]').val();
            
            const validChar = /^[a-zA-Z][a-zA-Z\s.'-]*$/u;

            if (!validChar.test(viewerName)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Name',
                    text: 'Please enter a valid name (letters, spaces, dots, apostrophes, and hyphens only).',
                    allowOutsideClick: false,
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (checkedModules.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Module Selected',
                    text: 'Please select at least one module access before submitting.',
                    confirmButtonText: 'OK'
                });

                return;
            }

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we save the viewer details.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Viewer added successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Optionally reset or reload page
                        form.trigger('reset');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.close();

                    let msg = 'Something went wrong! Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = Object.values(errors).map(e => e[0]).join('<br>');
                        msg = errorMessages;
                    } else if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = Object.values(errors).map(e => e[0]).join('<br>');
                        msg = errorMessages;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        html: msg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

</script>
