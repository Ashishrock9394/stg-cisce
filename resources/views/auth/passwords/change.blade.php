@extends('layouts.filldart-app')

@section('content')
<style>
    .card { transition: all 0.3s ease;}

    .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem; width: 100%; }

    .step { width: 30px; height: 30px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;
        margin: 0 10px; font-weight: bold; }

    .step.active { background-color: #ff7800; color: white; }

    .step.completed { background-color: #198754; color: white; }

    .step-line { flex: 1;  height: 2px; background-color: #e9ecef; margin: 0 5px; align-self: center; }

    .step-line.active { background-color: #0d6efd; }

    .step-line.completed { background-color: #198754; }

    .skip-btn { margin-left: 10px; }

    .btn-group { display: flex; gap: 10px;}

    .btn-group .btn { flex: 1; }

    .success-icon { width: 60px; height: 60px; border-radius: 50%; background-color: #198754; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;  color: white; font-size: 2.5rem; }

    .security-code-container { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 1rem;
        margin: 1.5rem 0; position: relative;}

    .security-code { font-family: 'Courier New', monospace; font-size: 1.1rem; font-weight: bold; color: #198754; margin-bottom: 0.5rem;
        word-break: break-all; }

    .copy-btn {  position: absolute; top: 0.5rem;  right: 0.5rem; background: none; border: none; color: #6c757d; cursor: pointer; padding: 0.25rem;
        border-radius: 4px; transition: all 0.2s; }

    .copy-btn:hover { background-color: #e9ecef; color: #495057; }

    .copy-feedback { position: absolute; top: -30px; right: 0; background-color: #198754;  color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;  opacity: 0; transition: opacity 0.3s; }

    .copy-feedback.show {  opacity: 1; }

    .alert { margin-bottom: 1rem;  }

    .password-toggle { position: absolute; right: 10px;top: 72%; transform: translateY(-50%); background: none; border: none; color: #6c757d;  cursor: pointer;  z-index: 10; }

    .password-input-group {  position: relative;}

    .password-toggle:hover {  color: #495057; }
</style>

<div class="container py-2">


    <h3 class="mb-4 text-center py-2">Security Settings</h3>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active">1</div>
                    <div class="step-line "></div>
                    <div class="step ">2</div>
                    <div class="step-line"></div>
                    <div class="step">3</div>
                </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- Security Questions -->
        <div class="col-md-10 mb-4" id="security-questions-card">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0 text-white">Step 1: Security Questions</h5>
                </div>
                <div class="card-body">
                    <div id="security-questions-alert"></div>
                    <form id="security-questions-form" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Select Question 1</label>

                            @php
                             $parts = $SecurityQuestions->split(2);
                             $firstPart = $parts[0];
                             $secondPart = $parts[1];
                            @endphp
                            
                            <select name="question1" class="form-control" required>
                                <option value="">Select Question</option>
                                @foreach($firstPart as $value)
                                    <option value="{{ $value->id }}">{{ $value->question }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="answer1" class="form-control mt-2" placeholder="Your Answer" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Select Question 2</label>
                            <select name="question2" class="form-control" required>
                                <option value="">Select Question</option>
                                @foreach($secondPart as $value)
                                    <option value="{{ $value->id }}">{{ $value->question }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="answer2" class="form-control mt-2" placeholder="Your Answer" required>
                        </div>

                        <div class="btn-group">
                            @if($isSet)
                            <button type="submit" class="btn btn-warning text-white" id="submit-questions">Update Security Questions</button>
                            @else
                            <button type="submit" class="btn btn-warning text-white" id="submit-questions">Save Security Questions</button>
                            @endif
                            <button type="button" id="skip-questions" class="btn btn-outline-secondary">Skip and Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-10 mb-4" id="change-password-card" style="display: none;">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0 text-white">Step 2: Change Password</h5>
                </div>
                <div class="card-body">
                    <div id="password-alert"></div>


                    <form id="change-password-form" method="POST">
                        @csrf
                        <div class="form-group mb-3 password-input-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" id="current-password" required>

                            <button type="button" class="password-toggle" data-target="current-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>

                        </div>

                        <div class="form-group mb-3 password-input-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" id="new-password" required>

                            <button type="button" class="password-toggle" data-target="new-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>

                        </div>

                        <div class="form-group mb-3 password-input-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" id="confirm-password" required>

                            <button type="button" class="password-toggle" data-target="confirm-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>

                        </div>

                        <div class="btn-group">
                            <button type="button" id="back-to-questions" class="btn btn-outline-secondary">Back</button>
                            <button type="submit" class="btn btn-primary" id="submit-password">Update Password</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- Confirmation Step -->
        <div class="col-md-10 mb-4" id="confirmation-card" style="display: none;">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 text-white">Step 3: Confirmation</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="success-icon"> ✓ </div>                        
                        <h3 class="mb-3">Password Updated Successfully!</h3>
                        <p class="text-muted mb-4">Your security questions and password have been updated successfully.</p>
                        
                        <!-- Security Code Section -->
                        <div class="security-code-container">
                            <div class="copy-feedback" id="copy-feedback">Copied!</div>
                            <div class="security-code" id="security-code">Fitness365@ToTUser123</div>
                            <p class="small text-muted mb-0">Please save this security code for future reference</p>
                            <button class="copy-btn" id="copy-btn" title="Copy to clipboard">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                    <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                    <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                </svg>
                            </button>
                        </div>
                        
                        <button type="button" id="finish-btn" class="btn btn-success">Finish</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const securityQuestionsCard = document.getElementById('security-questions-card');
        const changePasswordCard = document.getElementById('change-password-card');
        const confirmationCard = document.getElementById('confirmation-card');
        const securityQuestionsForm = document.getElementById('security-questions-form');
        const skipQuestionsBtn = document.getElementById('skip-questions');
        const changePasswordForm = document.getElementById('change-password-form');
        const backToQuestionsBtn = document.getElementById('back-to-questions');
        const finishBtn = document.getElementById('finish-btn');
        const copyBtn = document.getElementById('copy-btn');
        const copyFeedback = document.getElementById('copy-feedback');
        const securityCode = document.getElementById('security-code');
        const securityQuestionsAlert = document.getElementById('security-questions-alert');
        const passwordAlert = document.getElementById('password-alert');
        const submitQuestionsBtn = document.getElementById('submit-questions');
        const submitPasswordBtn = document.getElementById('submit-password');
        
        // Step indicator elements
        const step1 = document.querySelectorAll('.step')[0];
        const step2 = document.querySelectorAll('.step')[1];
        const step3 = document.querySelectorAll('.step')[2];
        const line1 = document.querySelectorAll('.step-line')[0];
        const line2 = document.querySelectorAll('.step-line')[1];
        
        // Store form data to preserve when navigating back
        let securityQuestionsData = {
            question1: '',
            answer1: '',
            question2: '',
            answer2: ''
        };
        
        // Initialize password toggle functionality
        initPasswordToggles();

        // Handle security questions form submission
        securityQuestionsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitSecurityQuestions();
        });
        
        // Handle skip button click
        skipQuestionsBtn.addEventListener('click', function() {
            saveSecurityQuestionsData();
            proceedToPasswordChange();
        });
        
        // Handle change password form submission
        changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitPasswordChange();
        });
        
        // Handle back to questions button
        backToQuestionsBtn.addEventListener('click', function() {
            goBackToQuestions();
        });
        
        // Handle finish button
        finishBtn.addEventListener('click', function() {
            window.location.href = '/dashboard';
        });
        
        // Handle copy button click
        copyBtn.addEventListener('click', function() {
            // Copy security code to clipboard
            navigator.clipboard.writeText(securityCode.textContent)
                .then(() => {
                    // Show feedback
                    copyFeedback.classList.add('show');
                    setTimeout(() => {
                        copyFeedback.classList.remove('show');
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = securityCode.textContent;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    // Show feedback
                    copyFeedback.classList.add('show');
                    setTimeout(() => {
                        copyFeedback.classList.remove('show');
                    }, 2000);
                });
        });
        

        function initPasswordToggles() {
            // Add event listeners to all password toggle buttons
            const toggleButtons = document.querySelectorAll('.password-toggle');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('svg');
                    
                    // Toggle password visibility
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        // Change to eye-slash icon
                        icon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
                    } else {
                        passwordInput.type = 'password';
                        // Change back to eye icon
                        icon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
                    }
                });
            });
        }


        function submitSecurityQuestions() {
            const formData = new FormData(securityQuestionsForm);
            
            // Show loading state
            submitQuestionsBtn.disabled = true;
            submitQuestionsBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            
            fetch('{{ route("security.update-questions") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saveSecurityQuestionsData();
                    showAlert(securityQuestionsAlert, 'Security questions saved successfully!', 'success');
                    proceedToPasswordChange();
                } else {
                    showAlert(securityQuestionsAlert, data.message || 'Failed to save security questions', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(securityQuestionsAlert, 'An error occurred while saving security questions', 'danger');
            })
            .finally(() => {
                // Reset button state
                submitQuestionsBtn.disabled = false;
                submitQuestionsBtn.innerHTML = 'Save Security Questions';
            });

        }
        

        function submitPasswordChange() {

            const formData = new FormData(changePasswordForm);
            // Show loading state
            submitPasswordBtn.disabled = true;
            submitPasswordBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status"></span> Updating...';
            axios.post('/school/security-settings/password', formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {

                if (response.data.success) {
                    showAlert(passwordAlert, 'Password updated successfully!', 'success');
                    if (response.data.backup_code) {
                        document.getElementById('security-code').textContent = response.data.backup_code;
                    }
                    proceedToConfirmation();
                    return;
                }

                let message = response.data.message || 'Failed to update password';
                if (response.data.errors) {
                    message = Object.values(response.data.errors).flat().join('<br>');
                }
                showAlert(passwordAlert, message, 'danger');
            })

            .catch(error => {
                console.error("Axios Error:", error);
                // Laravel validation / 4xx errors
                if (error.response) {
                    const data = error.response.data;
                    let message = data.message || 'Something went wrong';
                    if (data.errors) {
                        message = Object.values(data.errors).flat().join('<br>');
                    }
                    showAlert(passwordAlert, message, 'danger');
                }
                // No response returned
                else if (error.request) {
                    showAlert(passwordAlert, 'No response from server. Check your connection.', 'danger');
                }
               
                else {
                    showAlert(passwordAlert, 'Error: ' + error.message, 'danger');
                }
            })
            .finally(() => {
                submitPasswordBtn.disabled = false;
                submitPasswordBtn.innerHTML = 'Update Password';
            });
        }



        function submitPasswordChange1() {
            const formData = new FormData(changePasswordForm);
            
            // Show loading state
            submitPasswordBtn.disabled = true;
            submitPasswordBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
            
            fetch('{{ route("security.update-password") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(passwordAlert, 'Password updated successfully!', 'success');
                    proceedToConfirmation();
                } else {
                    let errorMessage = data.message || 'Failed to update password';
                    if (data.errors) {
                        // Handle validation errors
                        errorMessage = Object.values(data.errors).flat().join('<br>');
                    }
                    console.error('Error:', errorMessage);
                    showAlert(passwordAlert, errorMessage, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(passwordAlert, 'An error occurred while updating password', 'danger');
            })
            .finally(() => {
                // Reset button state
                submitPasswordBtn.disabled = false;
                submitPasswordBtn.innerHTML = 'Update Password';
            });

          
        }
        
        function showAlert(container, message, type) {
            container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        }
        
        function saveSecurityQuestionsData() {
            // Save the security questions data
            const formData = new FormData(securityQuestionsForm);
            securityQuestionsData.question1 = formData.get('question1');
            securityQuestionsData.answer1 = formData.get('answer1');
            securityQuestionsData.question2 = formData.get('question2');
            securityQuestionsData.answer2 = formData.get('answer2');
        }
        
        function restoreSecurityQuestionsData() {
            // Restore the security questions data when going back
            document.querySelector('select[name="question1"]').value = securityQuestionsData.question1;
            document.querySelector('input[name="answer1"]').value = securityQuestionsData.answer1;
            document.querySelector('select[name="question2"]').value = securityQuestionsData.question2;
            document.querySelector('input[name="answer2"]').value = securityQuestionsData.answer2;
        }
        
        function proceedToPasswordChange() {
            // Hide security questions card
            securityQuestionsCard.style.display = 'none';
            
            // Show change password card
            changePasswordCard.style.display = 'block';
            confirmationCard.style.display = 'none';
            
            // Update step indicator
            step1.classList.add('completed');
            step2.classList.add('active');
            step3.classList.remove('active');
            line1.classList.add('completed');
            line2.classList.remove('active', 'completed');
        }
        
        function proceedToConfirmation() {
            // Hide change password card
            changePasswordCard.style.display = 'none';
            
            // Show confirmation card
            confirmationCard.style.display = 'block';
            
            // Update step indicator
            step2.classList.remove('active');
            step2.classList.add('completed');
            step3.classList.add('active');
            line2.classList.add('completed');
        }
        
        function goBackToQuestions() {
            // Hide change password card
            changePasswordCard.style.display = 'none';
            
            // Show security questions card
            securityQuestionsCard.style.display = 'block';
            
            // Restore the security questions data
            restoreSecurityQuestionsData();
            
            // Update step indicator
            step1.classList.remove('completed');
            step2.classList.remove('active',);
            line1.classList.remove('active', 'completed');
            line2.classList.remove('active', 'completed');

        }
    });
</script>
@endsection