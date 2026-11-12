

@extends('layouts.app')

@section('content')
<style>
    .card { transition: all 0.3s ease; }
    .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem;  width: 100%; }
    .step { width: 30px;  height: 30px; border-radius: 50%;background-color: #e9ecef; display: flex;  align-items: center;  justify-content: center;
        margin: 0 10px; font-weight: bold; }
    .step.active { background-color: #ff7800; color: white;}
    .step.completed { background-color: #198754; color: white; }
    .step-line {  flex: 1;  height: 2px; background-color: #e9ecef; margin: 0 5px;  align-self: center; }
    .step-line.active {  background-color: #ff7800; }
    .step-line.completed {  background-color: #198754; }
    .list-group-item { cursor: pointer; transition: all 0.2s;}
    .list-group-item:hover { background-color: #f8f9fa;}
    .card-header.bg-warning.text-white { background: linear-gradient(135deg, #ff9800, #ffc107) !important; }
    button#continue-method { background: linear-gradient(135deg, #ff9800, #ffc107) !important;}
    .list-group-item.active { background: #ff9800 !important;  border-color: #ff9800 !important; color: white !important;}
    .list-group-item.active .form-check-label,.list-group-item.active .form-check-label strong,.list-group-item.active .form-check-label small,
    .list-group-item.active .text-muted { color: white !important; }
    .form-check-input { margin-right: 10px; }
    .password-input-group { position: relative; }
    .password-toggle { position: absolute; right: 10px; top: 50%;transform: translateY(-50%); background: none; border: none; color: #6c757d;
        cursor: pointer;  z-index: 10; }
    .alert { margin-bottom: 1rem;}
    .btn-group { display: flex;   gap: 10px; border-radius: 6px; }
    .btn-group .btn { flex: 1; border-radius: 6px;}
    .text-muted {  color: #6c757d !important;  font-size: 14px !important; }
    label.form-check-label.w-100 strong { font-size: 14px; }
    .security-code-container { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 1rem;
        margin: 1.5rem 0; position: relative;}
    .security-code { font-family: 'Courier New', monospace; font-size: 1.1rem; font-weight: bold; color: #198754; margin-bottom: 0.5rem;
        word-break: break-all; }
    .copy-btn {  position: absolute; top: 0.5rem;  right: 0.5rem; background: none; border: none; color: #6c757d; cursor: pointer; padding: 0.25rem;
        border-radius: 4px; transition: all 0.2s; }
    .copy-btn:hover { background-color: #e9ecef; color: #495057; }
    .copy-feedback { position: absolute; top: -30px; right: 0; background-color: #198754;  color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;  opacity: 0; transition: opacity 0.3s; }
    .copy-feedback.show {  opacity: 1; }
    .password-toggle { position: absolute; right: 10px;top: 72%; transform: translateY(-50%); background: none; border: none; color: #6c757d;  cursor: pointer;  z-index: 10; }

    div#email-alert { text-align: center;}
</style>

<div class="py-4" style="margin-top:4%">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-8 col-sm-10 col-12">
            <!-- Step Indicator -->
            <div class="step-indicator mt-4">
                <div class="step active">1</div>
                <div class="step-line"></div>
                <div class="step">2</div>

                <div class="step-line"></div>
                <div class="step">3</div>

                <div class="step-line line-4"></div>
                <div class="step step-4">4</div>

                <div class="step-line line-5"></div>
                <div class="step step-5">5</div>

            </div>

            <!-- Method Selection Step -->
            <div class="card shadow mb-5" id="method-selection-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Password Recovery - Select Method</h5>
                </div>
                <div class="card-body">
                    <div id="method-alert"></div>
                    <p class="text-muted mb-2 form-label">Choose how you want to reset your password:</p>
                    
                    <div class="list-group">
                        <div class="list-group-item list-group-item-action px-3 method-option" data-method="backup_code">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recoveryMethod" id="backupCodeMethod" value="backup_code">
                                <label class="form-check-label w-100" for="backupCodeMethod">
                                    <strong>Backup Code</strong>
                                    <small class="d-block text-muted">Use your security code that was generated when you last updated your password</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="list-group-item list-group-item-action px-3 method-option" data-method="security_questions">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recoveryMethod" id="securityQuestionsMethod" value="security_questions">
                                <label class="form-check-label w-100" for="securityQuestionsMethod">
                                    <strong>Security Questions</strong>
                                    <small class="d-block text-muted">Answer your security questions that you set up previously</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="list-group-item list-group-item-action px-3 method-option" data-method="email">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recoveryMethod" id="emailMethod" value="email">
                                <label class="form-check-label w-100" for="emailMethod">
                                    <strong>Request Password Reset Link</strong>
                                    <small class="d-block text-muted">Receive a password reset link via email</small>
                                </label>
                            </div>
                        </div>
                        <div class="list-group-item list-group-item-action px-3 method-option" data-method="otp">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recoveryMethod" id="otpMethod" value="otp">
                                <label class="form-check-label w-100" for="otpMethod">
                                    <strong>Request OTP</strong>
                                    <small class="d-block text-muted">Receive a One-Time Password (OTP) to reset your password.</small>
                                </label>
                            </div>
                        </div>

                        {{--
                        <div class="list-group-item list-group-item-action px-3 method-option" data-method="headquarter">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recoveryMethod" id="headquarterMethod" value="headquarter">
                                <label class="form-check-label w-100" for="headquarterMethod">
                                    <strong>Request to Headquarter</strong>
                                    <small class="d-block text-muted">Send a reset request to system administrators</small>
                                </label>
                            </div>
                        </div>
                        --}}

                    </div>

                    <div class="btn-group mt-4">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Back to Login</a>
                        <button type="button" id="continue-method" class="btn btn-warning text-white" disabled>Continue</button>
                    </div>
                   
                </div>
            </div>

            <!-- Email Input Step -->
            <div class="card shadow" id="email-input-card" style="display: none;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Enter Your Email</h5>
                </div>
                <div class="card-body">
                    <div id="email-alert"></div>

                    <!-- <p class="text-muted mb-4">Please enter your registered email address to continue.</p> -->
                    
                    <form id="email-form">
                        <p class="text-muted mb-4">Please enter your registered email address to continue.</p>
                        @csrf
                        <div class="form-group mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email" required>
                        </div>

                        <div class="btn-group">
                            <button type="button" id="back-to-method" class="btn btn-outline-secondary">Back</button>
                            <button type="submit" class="btn btn-warning text-white" id="submit-email">Continue</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Backup Code Verification Step -->
            <div class="card shadow" id="backup-code-card" style="display: none;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Verify Backup Code</h5>
                </div>
                <div class="card-body">
                    <div id="backup-code-alert"></div>
                    <p class="text-muted mb-4">Enter the security code that was generated when you last updated your password.</p>
                    
                    <form id="backup-code-form">
                        @csrf
                        <input type="hidden" name="email" id="backup-code-email">
                        <div class="form-group mb-4">
                            <label for="security_code" class="form-label">Security Code</label>
                            <input type="text" class="form-control" id="security_code" name="security_code" placeholder="Enter your security code" required>                           
                        </div>

                        <div class="btn-group">
                            <button type="button" id="back-from-backup" class="btn btn-outline-secondary">Back</button>
                            <button type="submit" class="btn btn-warning text-white" id="submit-backup-code">Verify Code</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Questions Step -->
            <div class="card shadow" id="security-questions-card" style="display: none;">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Answer Security Questions</h5>
                </div>
                <div class="card-body">
                    <div id="questions-alert"></div>
                    <p class="text-muted mb-4">Please answer your security questions to verify your identity.</p>
                    
                    <form id="security-questions-form">
                        @csrf
                        <input type="hidden" name="email" id="questions-email">
                        
                        <div class="form-group mb-4">
                            <label class="form-label" id="question1-label">Security Question 1</label>
                            <input type="text" class="form-control" name="answer1" placeholder="Enter your answer" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label" id="question2-label">Security Question 2</label>
                            <input type="text" class="form-control" name="answer2" placeholder="Enter your answer" required>
                        </div>

                        <div class="btn-group">
                            <button type="button" id="back-from-questions" class="btn btn-secondary">Back</button>
                            <button type="submit" class="btn btn-warning" id="submit-questions">Verify Answers</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Verify password reset otp  -->
             <div class="card shadow" id="otp-card" style="display:none;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Verify OTP</h5>
                </div>
                <div class="card-body">
                    <div id="otp-alert"></div>
                    <p class="text-muted">
                        Enter the OTP sent to your registered email.
                    </p>
                    <form id="otp-form">
                        @csrf
                        <input type="hidden" id="otp-email" name="email">
                        <div class="form-group mb-4">
                            <label>OTP</label>
                            <input type="text" maxlength="6" class="form-control" id="otp" name="otp" placeholder="Enter 6 digit OTP">
                        </div>

                        <div class="btn-group">
                            <button type="button" id="back-from-otp" class="btn btn-secondary">Back</button>
                            <button type="submit" class="btn btn-warning text-white" id="submit-otp"> Verify OTP</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reset Password Step -->
            <div class="card shadow" id="reset-password-card" style="display: none;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Reset Your Password</h5>
                </div>
                <div class="card-body">
                    <div id="reset-password-alert"></div>
                    <p class="text-muted mb-4">Create a new password for your account.</p>
                    
                    <form id="reset-password-form">
                        @csrf

                        <!-- <input type="hidden" name="verification_method" id="verification-method"> -->
                        <input type="hidden" name="email" id="reset-password-email">
                        <input type="hidden" name="token" id="reset-token">

                        <div class="form-group mb-3 password-input-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" id="new-password" required>
                            
                            <button type="button" class="password-toggle" data-target="new-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>

                        <div class="form-group mb-4 password-input-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="confirm-password" required>

                            <button type="button" class="password-toggle" data-target="confirm-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>

                        <div class="btn-group">
                            <button type="button" id="back-from-reset" class="btn btn-outline-secondary">Back</button>
                            <button type="submit" class="btn btn-warning text-white" id="submit-reset-password">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Send Verification link Step -->

            {{--
            <div class="card shadow" id="backup-code-card" style="display: block;">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Verify Backup Code</h5>
                </div>
                <div class="card-body">
                    <div id="backup-code-alert"></div>
                    
                    <form id="backup-code-form">
                        @csrf
                        <input type="hidden" name="reset_link" id="reset-link-email">
                        <div class="form-group mb-4">
                            <label for="security_code" class="form-label">Get Reset link via email</label>

                            <input type="radio" class="form-control" id="reset_link" name="reset_link"required>                           
                        </div>
                        <div class="btn-group">
                            <button type="button" id="back-from-backup" class="btn btn-outline-secondary">Back</button>
                            <button type="submit" class="btn btn-warning text-white" id="submit-backup-code">Send Link</button>
                        </div>
                    </form>
                </div>
            </div>
            --}}


            <!-- Success Step -->
            <div class="card shadow" id="success-card" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Password Reset Successful</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="success-icon mb-4" style="width: 80px; height: 80px; border-radius: 50%; background-color: #198754; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: white; font-size: 2.5rem;">
                        ✓
                    </div>
                    <h4 class="mb-3">Password Reset Successful!</h4>
                    <p class="text-muted mb-4">Your password has been reset successfully. You can now log in with your new password.</p>
                    

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


                    <a href="{{ route('login') }}" class="btn btn-success">Go to Login</a>
                </div>
            </div>


            <!-- Success Step -->
            <div class="card shadow" id="reset-success-card" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Email Sent Successfully</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="success-icon mb-4" style="width: 80px; height: 80px; border-radius: 50%; background-color: #198754; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: white; font-size: 2.5rem;">
                        ✓
                    </div>
                    <h4 class="mb-3">Password Reset Link Sent Successfully!</h4>
                    <p class="text-muted mb-4">We have sent a password reset link to your registered email address. Please follow the link to update your password.</p>
                    
                    <a href="{{ route('login') }}" class="btn btn-success">Go to Login</a>
                </div>
            </div>


            <!-- Headquarter Request Step -->
            <div class="card shadow" id="headquarter-card" style="display: none;">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Request Sent to Headquarter</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-envelope-check text-secondary" viewBox="0 0 16 16">
                            <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z"/>
                            <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686Z"/>
                        </svg>
                    </div>
                    <h4 class="mb-3">Request Submitted</h4>
                    <p class="text-muted mb-4">Your password reset request has been sent to the headquarter. You will receive an email with further instructions within 24 hours.</p>
                    
                    <a href="{{ route('login') }}" class="btn btn-secondary">Back to Login</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const methodSelectionCard = document.getElementById('method-selection-card');
        const emailInputCard = document.getElementById('email-input-card');
        const backupCodeCard = document.getElementById('backup-code-card');
        const securityQuestionsCard = document.getElementById('security-questions-card');
        const resetPasswordCard = document.getElementById('reset-password-card');
        const successCard = document.getElementById('success-card');
        const emailRestLink = document.getElementById('reset-success-card');

        const otpCard = document.getElementById('otp-card');
        const otpForm = document.getElementById('otp-form');
        const backFromOtpBtn = document.getElementById('back-from-otp');

        const headquarterCard = document.getElementById('headquarter-card');
        
        const continueMethodBtn = document.getElementById('continue-method');
        const backToMethodBtn = document.getElementById('back-to-method');
        const backFromBackupBtn = document.getElementById('back-from-backup');
        const backFromQuestionsBtn = document.getElementById('back-from-questions');
        const backFromResetBtn = document.getElementById('back-from-reset');
        
        const methodOptions = document.querySelectorAll('.method-option');
        const emailForm = document.getElementById('email-form');
        const backupCodeForm = document.getElementById('backup-code-form');
        const securityQuestionsForm = document.getElementById('security-questions-form');
        const resetPasswordForm = document.getElementById('reset-password-form');
        
        const securityCode = document.getElementById('security-code');
        const copyBtn = document.getElementById('copy-btn');
        const copyFeedback = document.getElementById('copy-feedback');

        // Step indicator elements
        const step1 = document.querySelectorAll('.step')[0];
        const step2 = document.querySelectorAll('.step')[1];
        const step3 = document.querySelectorAll('.step')[2];
        const step4 = document.querySelectorAll('.step')[3];
        const step5 = document.querySelectorAll('.step')[4];

        const line1 = document.querySelectorAll('.step-line')[0];
        const line2 = document.querySelectorAll('.step-line')[1];
        const line3 = document.querySelectorAll('.step-line')[2];
        const line4 = document.querySelectorAll('.step-line')[3];

        
        // State
        let selectedMethod = null;
        let userEmail = null;
        
        /* Initialize password visibility toggles */
         initPasswordToggles();
        

        // Method selection
        methodOptions.forEach(option => {
            option.addEventListener('click', function() {


                clearAllAlerts();
                document.getElementById("backup-code-form").reset();
                document.getElementById("email-form").reset(); 


                methodOptions.forEach(opt => {
                    opt.classList.remove('active');
                    const radio = opt.querySelector('input[type="radio"]');
                    radio.checked = false;
                });
                
                // Add active class to selected option
                this.classList.add('active');
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                selectedMethod = radio.value;

                if (selectedMethod === 'email' || selectedMethod === 'otp') {
                    document.querySelector('.step-4').style.display = 'none';
                    document.querySelector('.step-5').style.display = 'none';
                    document.querySelector('.line-4').style.display = 'none';
                    document.querySelector('.line-5').style.display = 'none';
                } else {
                    document.querySelector('.step-4').style.display = 'flex';
                    document.querySelector('.step-5').style.display = 'flex';
                    document.querySelector('.line-4').style.display = 'block';
                    document.querySelector('.line-5').style.display = 'block';
                }
                
                // Enable continue button
                continueMethodBtn.disabled = false;
            });
        });
        
        // Continue to email input
        continueMethodBtn.addEventListener('click', function() {
            if (selectedMethod === 'headquarter') {
                // Skip email for headquarter request
                proceedToHeadquarter();
            }
            else {
                methodSelectionCard.style.display = 'none';
                emailInputCard.style.display = 'block';
                updateStepIndicator(2, 1);
            }
        });


        
        // Back to method selection
        backToMethodBtn.addEventListener('click', function() {
            emailInputCard.style.display = 'none';
            methodSelectionCard.style.display = 'block';
            updateStepIndicator(1, 2);
        });
        
        // Back from backup code
        backFromBackupBtn.addEventListener('click', function() {
            backupCodeCard.style.display = 'none';
            emailInputCard.style.display = 'block';
            updateStepIndicator(2, 3);
        });
        
        // Back from security questions
        backFromQuestionsBtn.addEventListener('click', function() {
            securityQuestionsCard.style.display = 'none';
            emailInputCard.style.display = 'block';
            updateStepIndicator(2, 3);
        });
        
        // Back from reset password
        backFromResetBtn.addEventListener('click', function() {
            resetPasswordCard.style.display = 'none';            
            if (selectedMethod === 'backup_code') {
                backupCodeCard.style.display = 'block';
            } else if (selectedMethod === 'security_questions') {
                securityQuestionsCard.style.display = 'block';
            }            
            updateStepIndicator(3, 4);
        });

        
        // Backup code form submission
        backupCodeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBackupCode();
        });
        
        // Security questions form submission
        securityQuestionsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitSecurityQuestions();
        });
        
        // Reset password form submission
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitResetPassword();
        });
        

        /* Check Users Existence */
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            userEmail = document.getElementById('email').value;
            
            if (!userEmail) {
                showAlert('email-alert', 'Please enter your email address', 'danger');
                return;
            }
            
            document.getElementById('backup-code-email').value = userEmail;
            document.getElementById('questions-email').value = userEmail;
            document.getElementById('reset-password-email').value = userEmail;
            checkUserRecoveryMethods(userEmail);
        });


        function checkUserRecoveryMethods(email) {
            const submitBtn = document.getElementById('submit-email');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
            
            axios.post('{{ route("check.email.existance") }}', { email: email }, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                const data = response.data;
                if (!data.exists) {
                    throw new Error('No account found with this email address');
                }

                switch (selectedMethod) {

                    case 'backup_code':
                        proceedToBackupCode();
                        return null;
                        break;

                    case 'security_questions':
                        return axios.post('{{ route("security.get-questions") }}', { email: email }, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        return null;
                        break;

                    case 'email':
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                        return axios.post('{{ route("password.email") }}', { email: email }, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })

                    case 'otp':
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                        return axios.post('{{ route("password.sendOtp") }}', { email: email }, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })


                    break;


                    case 'headquarter':
                        return axios.post('{{-- route("password.recovery.headquarter") --}}', { email: email }, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                    default:
                        console.log('Invalid option');
                }
            })
            .then(response => {
                if (!response) return;

                const data = response.data;
                if (data.message) {                    
                    emailInputCard.style.display = 'none'; 
                    emailRestLink.style.display = 'block';
                    updateStepIndicator(3, 2);

                }
                if(data.message && selectedMethod=="otp"){
                        
                    emailInputCard.style.display='none';
                    emailRestLink.style.display = 'none';
                    otpCard.style.display='block';
                    document.getElementById('otp-email').value=userEmail;
                } 

                if (data.success) {     


                    if (selectedMethod === 'backup_code') {
                        //proceedToBackupCode();

                    } else if (selectedMethod === 'security_questions') {                    
                        if (data.questions) {
                            document.getElementById('question1-label').textContent = data.questions[0].question1;
                            document.getElementById('question2-label').textContent = data.questions[1].question2;
                        }
                        proceedToSecurityQuestions();
                        return null;
                    }

                } else {
                    showAlert('email-alert', data.message || 'This recovery method is not available for your account', 'danger');
                }
            })
            .catch(error => {

                let errorMessage = 'An error occurred while checking your email';

                if (error.response && error.response.data) {
                    const data = error.response.data;
                    if (data.errors && data.errors.email) {
                        errorMessage = data.errors.email[0];
                    } 
                    else if (data.message) {
                        errorMessage = data.message;
                    }
                } else if (error.message) {
                    errorMessage = error.message;
                }              
                               
                showAlert('email-alert', errorMessage, 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
        

        /* selection -1 */
        function proceedToBackupCode() {
            emailInputCard.style.display = 'none';
            backupCodeCard.style.display = 'block';
            updateStepIndicator(3, 2);
        }

        /* selection -2 */
        function proceedToSecurityQuestions() { 
            emailInputCard.style.display = 'none';
            securityQuestionsCard.style.display = 'block';
            updateStepIndicator(3, 2);
        }


        function submitBackupCode() {

            const formData = new FormData(backupCodeForm);
            const submitBtn = document.getElementById('submit-backup-code');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';
            
            axios.post('{{ route("verify.security-code") }}', formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {

                const data = response.data;
                if (data.success) {
                    // document.getElementById('verification-method').value = 'backup_code';
                    document.getElementById('reset-password-email').value = data.email;
                    document.getElementById('reset-token').value = data.token;
                    proceedToResetPassword();

                } else {
                    showAlert('backup-code-alert', data.message || 'Invalid security code', 'danger');
                }
            })
            .catch(error => {
                //console.error('Error-12:', error);
                if (error.response && error.response.data) {
                    const data = error.response.data;
                    let errorMessage = data.message || 'Verification failed';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
                    }

                    showAlert('backup-code-alert', errorMessage, 'danger');
                } else {
                    showAlert('backup-code-alert', 'An error occurred during verification', 'danger');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }


        function submitSecurityQuestions() {
            const formData = new FormData(securityQuestionsForm);
            
            const submitBtn = document.getElementById('submit-questions');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';
            
            axios.post('{{ route("security.verify-questions") }}', formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                const data = response.data;
                if (data.success) {
                    //document.getElementById('verification-method').value = 'security_questions';
                    document.getElementById('reset-password-email').value = data.email;
                    document.getElementById('reset-token').value = data.token;
                    proceedToResetPassword();
                } else {
                    showAlert('questions-alert', data.message || 'Security answers are incorrect', 'danger');
                }

            })
            .catch(error => {                               
                if (error.response && error.response.data) {
                    const data = error.response.data;
                    let errorMessage = data.message || 'Verification failed';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
                    }
                    showAlert('questions-alert', errorMessage, 'danger');
                } else {
                    showAlert('questions-alert', 'An error occurred during verification', 'danger');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }


        function proceedToResetPassword() {
            if (selectedMethod === 'backup_code') {
                backupCodeCard.style.display = 'none';
            } else if (selectedMethod === 'security_questions') {
                securityQuestionsCard.style.display = 'none';
            }
            resetPasswordCard.style.display = 'block';
            updateStepIndicator(4, 3);
        }


        function submitResetPassword() {
            const formData = new FormData(resetPasswordForm);

            // Show loading state
            const submitBtn = document.getElementById('submit-reset-password');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Resetting...';
            
            
            axios.post('{{ route("password.update") }}', formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                const data = response.data;

                if (data.success) {
                     if (data.backup_code) {
                        document.getElementById('security-code').textContent = data.backup_code;
                    }
                    resetPasswordCard.style.display = 'none';
                    successCard.style.display = 'block';
                    updateStepIndicator(5, 4);

                } else {
                    let errorMessage = data.message || 'Failed to reset password';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
                    }
                    showAlert('reset-password-alert', errorMessage, 'danger');
                }

            })
            .catch(error => {
                // console.error('Error:', error);
                if (error.response && error.response.data) {
                    const data = error.response.data;
                    let errorMessage = data.message || 'Password reset failed';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
                    }
                    showAlert('reset-password-alert', errorMessage, 'danger');
                } else {
                    showAlert('reset-password-alert', 'An error occurred while resetting password', 'danger');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
        

        function updateStepIndicator(currentStep, previousStep) {
            // Reset all steps
            step1.classList.remove('active', 'completed');
            step2.classList.remove('active', 'completed');
            step3.classList.remove('active', 'completed');
            step4.classList.remove('active', 'completed');
            step5.classList.remove('active', 'completed');

            line1.classList.remove('active', 'completed');
            line2.classList.remove('active', 'completed');
            line3.classList.remove('active', 'completed');
            line4.classList.remove('active', 'completed');
            

            // Update steps based on current step
            if (currentStep >= 1) {

                step1.classList.add('completed');
            }
            if (currentStep >= 2) {
                step2.classList.remove('completed');
                line1.classList.add('completed');
            }
            if (currentStep >= 3) {
                step3.classList.remove('completed');
                step2.classList.add('completed');
                line2.classList.add('completed');
            }

            if (currentStep >= 4) {
                step4.classList.remove('completed');
                step3.classList.add('completed');
                line3.classList.add('completed');
            }

            if (currentStep >= 5) {
                step5.classList.remove('completed');
                step4.classList.add('completed');
                line4.classList.add('completed');
            }
            
            // Set active step
            if (currentStep === 1) {
                step1.classList.add('active');
            } else if (currentStep === 2) {
                step2.classList.add('active');
            } else if (currentStep === 3) {
                step3.classList.add('active');
            } else if(currentStep === 4){
                step4.classList.add('active');
            }else if(currentStep === 5){
                step5.classList.add('active');
            }
        }
        

        function showAlert(containerId, message, type) {
            const container = document.getElementById(containerId);
            container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert"> ${message}</div>`;
        }
        

        backFromOtpBtn.addEventListener('click', function () {
            otpCard.style.display = 'none';
            emailInputCard.style.display = 'block';
            updateStepIndicator(2,3);
        });


        // Verify Otp

        otpForm.addEventListener('submit',function(e){
            e.preventDefault();
            verifyOtp();
        });

        function verifyOtp(){
            let formData=new FormData(otpForm);
            const submitBtn=document.getElementById('submit-otp');
            const originalText=submitBtn.innerHTML;
            submitBtn.disabled=true;
            submitBtn.innerHTML='Verifying...';
            axios.post(
                "{{ route('verify.reset.otp') }}",
                formData,
                {
                    headers:{
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    }
                }
            ).then(response=>{
                const data=response.data;
                if(data.success){
                    window.location.href = data.redirect_url;
                    return;
                }
                else{
                    showAlert(
                        'otp-alert',
                        data.message,
                        'danger'
                    );
                }
            }).catch(error=>{
                showAlert(
                    'otp-alert',
                    error.response.data.message,
                    'danger'
                );
            }).finally(()=>{
                submitBtn.disabled=false;
                submitBtn.innerHTML=originalText;

            });

        }

        
        /* Show Password */
        function initPasswordToggles() {

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
    

        // Handle copy button click
        copyBtn.addEventListener('click', function() {
        
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

        /*
        
        function proceedToHeadquarter() {
            methodSelectionCard.style.display = 'none';
            headquarterCard.style.display = 'block';
            updateStepIndicator(3, 1);
        }
        
        
        function simulateEmailReset() {
            // Show loading state
            const submitBtn = document.getElementById('submit-email');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            
            // Simulate sending email
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                emailInputCard.style.display = 'none';
                successCard.style.display = 'block';
                updateStepIndicator(3, 2);
                
                // In a real application, you would show a message about checking email
                successCard.querySelector('h4').textContent = 'Check Your Email';
                successCard.querySelector('p').textContent = 'We have sent a password reset link to your email address. Please check your inbox and follow the instructions to reset your password.';
            }, 2000);
        }
        */

    });

    function clearAllAlerts() {
        const alertIds = [
            'method-alert',
            'email-alert',
            'backup-code-alert',
            'questions-alert',
            'reset-password-alert'
        ];

        alertIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '';
        });
    }

</script>
@endsection