<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\PasswordRecoveryControlller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Icse\HomeController;
use App\Http\Controllers\Icse\CicseController;
use App\Http\Controllers\FillDartController;
use App\Http\Controllers\SchoolRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AssessorAppController;
use App\Http\Controllers\CwsnController;
use App\Http\Controllers\ViewTrainerController;
use App\Http\Controllers\StudentRecordController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TOTController;


Route::get('/', function(){
    return view('index');
})->name('index');


/* Static Pages */
Route::get('/sop', [HomeController::class, 'Sop'])->name('sop');   //Ok
Route::get('/contact-us', [HomeController::class, 'ContactUs'])->name('contact_us'); //Ok
Route::get('privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');  //Ok
Route::get('/missing-records', [HomeController::class, 'UploadData'])->name('redirecturl');  //Ok
Route::get('terms-conditions', [HomeController::class, 'TermsConditions'])->name('terms.conditions');
Route::get('/demo-videos', [HomeController::class, 'DemoVideos'])->name('demo_videos');


/* Module: Training Resources (School, Trainer , HQ) */
Route::get('training', [HomeController::class, 'TrainingResources'])->name('training.resources'); //Ok
Route::get('battery-of-tests', [HomeController::class, 'BatteriesOfTests'])->name('test_videos'); //Ok
Route::get('training-manual', [HomeController::class, 'TrainingManual'])->name('training_manual'); //Ok
Route::get('training-videos', [HomeController::class, 'TrainingVideos'])->name('training.videos'); //Ok
Route::get('contact_us', [HomeController::class, 'ContactMe'])->name('contact_me'); //Ok
Route::get('faq', [HomeController::class, 'Faq'])->name('faq');  //Ok


// sent notification emails 
Route::get('send-notification', [HomeController::class,'sendNotificationView'])->name('send.notification.view');
Route::post('send-notification-email', [HomeController::class,'sendNotificationEmail'])->name('send.notification.email');


/* User Authentication */
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); //Ok
Route::post('login', [LoginController::class, 'authenticate'])->name('authenticate.login'); //Ok
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');     //Ok


/* Forget Password And Password Reset  */
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/email/otp', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.sendOtp');
Route::post('verify/reset/otp', [ForgotPasswordController::class, 'verifyResetOtp'])->name('verify.reset.otp');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::post('password-reset/check-email-exists', [PasswordRecoveryControlller::class, 'checkEmailExists'])->name('check.email.existance');
Route::post('/password-reset/verify-backup-code', [PasswordRecoveryControlller::class, 'verifySecurityCode'])->name('verify.security-code');
Route::post('/password/recovery/get-questions', [PasswordRecoveryControlller::class, 'getUserSecurityQuestions'])->name('security.get-questions');
Route::post('/password-reset/verify-security-questions',[PasswordRecoveryControlller::class, 'verifySecurityQuestions'])->name('security.verify-questions');

/* Routes For Handling OTPs */
Route::get('otp-verify', [LoginController::class, 'showOtpForm'])->middleware(['guest', 'otp.session'])->name('otp.verify.form'); //Ok
Route::post('otp/verify', [LoginController::class,'verifyOtp'])->middleware(['guest', 'otp.session', 'throttle:5,1'])->name('otp.verify'); //Ok
Route::post('otp-resend',[LoginController::class,'resendOtp'])->middleware('otp.session')->middleware('throttle:3,1')->name('otp.resend');  //Ok


/* Users Dashboard Access */
Route::get('dashboard', [FillDartController::class, 'dashboard'])->name('filldart.dashboard');


/* School And Their School Viewrs Route */
Route::middleware(['auth', 'role:4,8'])->prefix('school')->group(function() { 
    

    Route::get('manage-student', [SchoolRecordController::class,'ManageStudents'])->name('managestudent')
    ->middleware('module_access:managestudent'); //ok

    /* Module-1 : Handling Bulk Upload */
    Route::get('download-template', [SchoolRecordController::class, 'downloadTemplate'])->name('download-template'); //ok
    Route::get('sample-data', [SchoolRecordController::class, 'sampleData'])->name('sample-data');  //ok
    Route::post('download-student-profile', [SchoolRecordController::class, 'downloadStudentProfile'])->name('download.student.profile'); //ok
    Route::post('import-student-data', [SchoolRecordController::class, 'importStudentData'])->name('import-student-data'); //Ok
    Route::get('invalid-file-data', [SchoolRecordController::class, 'downloadInvalidData'])->name('downloadInvalidData');  //Ok
    Route::get('import/download-duplicates', [SchoolRecordController::class, 'downloadDuplicates'])->name('downloadDuplicates'); //Ok
    Route::get('uploaded-file/{logId}', [SchoolRecordController::class, 'downloadUploadedFile'])->name('download.uploadedfile'); //OK
    Route::get('import/existing-students', [SchoolRecordController::class, 'ExistingStudents'])->name('existingStudents');
    Route::get('import/downloadErrorList', [SchoolRecordController::class, 'downloadErrorList'])->name('downloadErrorList');   
    Route::get('download-error-file/{logId}', [SchoolRecordController::class, 'downloadErrorFile'])->name('download.errorfile');    
    Route::post('save-class-nomenclature', [SchoolRecordController::class, 'addClassNomenclature'])->name('saveclassnomenclature');
    Route::post('delete-class', [SchoolRecordController::class, 'deleteSelectedClass'])->name('class.delete');
    Route::post('reset-selected-classes', [SchoolRecordController::class, 'resetSelectedClass'])->name('classes.reset');

    /* Generate I-Card */
    Route::post('/generate-class-section-credentials', [SchoolRecordController::class, 'generateClassSectionCredentials'])->name('generate.class.section.credentials'); //Ok
    Route::post('/get-class-section-summary', [SchoolRecordController::class, 'getClassSectionSummary'])->name('get.class.section.summary');//Ok
    Route::post('generate-card', [SchoolRecordController::class, 'generateIdCard'])->name('generatecard'); //Ok


    /* Edit students data */
    Route::post('addstudent',[SchoolRecordController::class, 'addStudent'])->name('addstudent'); //ok
    Route::post('getclasssection', [SchoolRecordController::class, 'getClassSection'])->name('getclasssection'); //ok
    Route::post('upadatedob', [SchoolRecordController::class,'upadatedob'])->name('upadatedob');  //Ok
    Route::post('update-email',[SchoolRecordController::class, 'updateEmailId'])->name('update.email'); //Ok
    Route::post('updatesection', [SchoolRecordController::class,'UpdateSection'])->name('updatesection'); //Ok
    Route::post('changegender',[SchoolRecordController::class, 'changeGender'])->name('changegender');
    Route::post('changestatus',[SchoolRecordController::class, 'changeStatus'])->name('changestatus');  //Ok
    Route::post('update-rollno',[SchoolRecordController::class, 'updateRollno'])->name('update.rollno'); //Ok
    Route::get('fetchStudents',[SchoolRecordController::class, 'fetchStudents'])->name('fetchStudents');
    Route::post('del-student',[SchoolRecordController::class, 'DeleteStudent'])->name('del-student');


    /* Handling Students Promotion  */
    Route::post('promote-student-ids',[SchoolRecordController::class, 'PromoteStudentIds'])->name('promote.student.ids');
    Route::get('fetch-promotion-ids-status',[SchoolRecordController::class, 'PromotionIdsStatus'])->name('fetch.promotionIds.status'); //ok
    Route::get('students-promotion-history',[SchoolRecordController::class,'StudentPromotionHistory'])->name('students.promotion.history');
    Route::post('revert-promotion',[SchoolRecordController::class, 'RevertStudentPromotion'])->name('revert.promotion');


    /* Moduule-2: Parent's Email Update In Bulk */
    Route::get('student-email', [SchoolRecordController::class, 'studentEmail'])->name('student.email')->middleware('module_access:student.email');//Ok
    Route::post('download-email-template', [SchoolRecordController::class, 'downloadEmailTemplate'])->name('download.email.template');    //Ok
    Route::post('upload-email-template', [SchoolRecordController::class, 'uploadEmailTemplate'])->name('upload.student.emails');   //Ok
    Route::get('/download-email-error', [SchoolRecordController::class, 'downloadEmailError'])->name('download.email.error'); //Ok    
    Route::get('uploaded-email-file/{logId}', [SchoolRecordController::class, 'downloadEmailFile'])->name('download.uploaded.emailfile'); //Ok


    /* Module-3: Handle Trainers */
    Route::get('map-trainer', [SchoolRecordController::class,'MapTrainer'])->name('mapping.trainer')->middleware('module_access:mapping.trainer'); 
    //Ok
    Route::post('register-trainer', [SchoolRecordController::class, 'RegisterTrainer'])->name('register.trainer'); //Ok
    Route::get('autocomplete-trainer', [SchoolRecordController::class, 'autocomplete'])->name('autocomplete.trainer');//Ok
    Route::post('trainer-map', [SchoolRecordController::class, 'MapTrainer'])->name('trainer.map');//Ok
    Route::post('trainer-unmap', [SchoolRecordController::class, 'UnMapTrainer'])->name('trainer.unmap'); //Ok
    Route::post('remove-trainer', [SchoolRecordController::class, 'RemoveTrainer'])->name('remove.trainer'); //Ok
    Route::post('export-users', [SchoolRecordController::class, 'exportSchoolUsers'])->name('export.school.users');

    /* Module-4: Uploaad Test Data In Bulk */
    Route::get('upload-test-data', [AssessorAppController::class, 'uploadTestData'])->name('upload.test.data')->middleware('module_access:upload.test.data'); //Ok
    Route::post('/test-templete', [AssessorAppController::class, 'downloadTestTemplete'])->name('test.templete'); //Ok
    Route::get('/sample-score', [AssessorAppController::class, 'testScoreSample'])->name('test.sample'); //Ok
    Route::post('/import-test-data', [AssessorAppController::class, 'importTestData'])->name('import.test.data'); //Ok
    Route::get('download-test-file/{logId}',[AssessorAppController::class,'downloadTestUploadedFile'])->name('download.testuploadedfile'); //Ok
    Route::get('uploaded-error-file/{logId}', [AssessorAppController::class, 'downloadTestErrorFile'])->name('download.testerrorfile');// Ok
    

    /* Module-8: Generate Fitness Assessment Report Cards In Bulk */
    Route::get('fitness-report', [ReportController::class, 'FitnessReports'])->name('fitness.report')->middleware('module_access:fitness.report'); //Ok
    Route::get('sent-reportcards/status', [ReportController::class, 'CheckSentStatus'])->name('fitness.report.sent');
    Route::post('sent-reportcards', [ReportController::class, 'sentBulkReportCards'])->name('sent.reportcards');
    Route::get('downloadReport/{id?}/{term_id?}', [ReportController::class, 'downloadFitnessReport'])->name('reports.download'); //Ok
    Route::get('cwsn-downloadReport/{id?}/{term_id?}', [ReportController::class, 'downloadCWSNFitnessReport'])->name('cwsn.reports.download');


    /* Module-9: Create Delegate For Portal Access  */
    Route::get('view-school-admin', [SchoolRecordController::class, 'viewSchoolAdmin'])->name('view.school.admin')->middleware('module_access:view.school.admin'); //Ok
    Route::post('export-admin', [SchoolRecordController::class, 'exportAdminCredentials'])->name('export.school.admin'); //Ok
    Route::post('store-admin', [SchoolRecordController::class, 'storeSchoolAdmin'])->name('store.admin'); //Ok
    Route::post('admin-action', [SchoolRecordController::class, 'handleAdminAction'])->name('admin.action'); //Ok
    Route::post('update-admin-details', [SchoolRecordController::class, 'updateAdminDetails'])->name('update.admin.details'); //Ok


    /* Update School Profile & Settings*/
    Route::get('profile', [SchoolRecordController::class, 'viewProfile'])->name('school.profile'); //Ok
    Route::put('profile/{id}', [SchoolRecordController::class,'UpdateProfile'])->name('school.profile.update'); //Ok
    Route::post('get-cities', [SchoolRecordController::class, 'getDistrictList'])->name('getDistrict');
    Route::get('password/change', [ChangePasswordController::class, 'showChangeForm'])->name('password.change');  //Ok
    Route::post('security-settings/questions', [ChangePasswordController::class, 'updateQuestions'])->name('security.update-questions'); //Ok
    Route::post('security-settings/password', [ChangePasswordController::class, 'updatePassword'])->name('security.update-password')->middleware(['auth', 'check.questions']); //Ok

    //Route::post('password/change', [ChangePasswordController::class, 'update'])->name('password.change.update');


    /* Edit student details with roll number suggetion */
    Route::post('rollNoSuggestion', [SchoolRecordController::class,'rollNoSuggestion'])->name('rollNoSuggestion');
    Route::post('edit-student-details', [SchoolRecordController::class, 'EditStudentDetails'])->name('edit.student.details'); 
});

/* Trainer's Route */
Route::middleware(['auth', 'role:3'])->prefix('trainer')->group(function() {

    /* Module-1 : Take Assesment Test  */
    Route::get('assessor-app-alltests', [AssessorAppController::class, 'alltests'])->name('all-test'); //Ok
    Route::get('assessor-app-test/{TestcategoryId}', [AssessorAppController::class, 'locomotorSkills'])->name('assessor-app-test'); //Ok
    Route::get('assessor-app-fms-skills/{TestTypeId}', [AssessorAppController::class, 'FMSTypes'])->name('fms-types');    //Ok
    Route::get('senior-fms-skills/{TestTypeId}/{SeniorBMI}', [AssessorAppController::class, 'FMSTypes'])->name('fms-types-senior');
    Route::post('submit-fms-types-record',[AssessorAppController::class, 'SubmitFMSType'])->name('fms.types.submit'); //oK
    Route::post('submit-flamingo-record',[AssessorAppController::class, 'SubmitFlamingoRecord'])->name('flamingo.record.submit'); //Ok
    Route::post('submit-plate-tapping-record',[AssessorAppController::class, 'SubmitPlateTappingRecord'])->name('plate.tapping.record.submit'); //Ok
    Route::post('submit-bmi-record',[AssessorAppController::class, 'SubmitBMIRecord'])->name('bmi.record.submit'); //Ok
    
    Route::get('physical-fitness-test/{TestcategoryId}', [AssessorAppController::class, 'PhysicalSkills'])->name('assessor.app.physical.test'); //Ok
    Route::get('senior-bmi-test/{TestcategoryId}/{SeniorBMI}', [AssessorAppController::class, 'PhysicalSkills'])->name('senior.bmi.test'); //ok
    
    
    Route::post('submit-speed-record',[AssessorAppController::class, 'SubmitSpeedRecord'])->name('speed.record.submit'); //Ok
    Route::post('submit-partialcurl-up-record',[AssessorAppController::class, 'SubmitPartialCurlUpRecord'])->name('partial.curl.up.record.submit'); //Ok
    Route::post('submit-sit-and-reach-record',[AssessorAppController::class, 'SubmitSitAndReachRecord'])->name('sit.and.reach.record.submit'); //Ok
    Route::post('submit-push-up-record',[AssessorAppController::class, 'SubmitPushUpRecord'])->name('push.up.record.submit'); //Ok
    Route::get('get_students_roll', [AssessorAppController::class, 'getStudentsRoll'])->name('studentRollNo.autocomplete'); //oK
    Route::get('fetch-student', [AssessorAppController::class, 'fetchStudentDetail'])->name('fetch.student.detail');  //OK
    Route::post('delete-student-test', [AssessorAppController::class, 'clearExistingRecords'])->name('delete-student-test'); //Ok

    /* Routes for CWSN Tests. */
    Route::prefix('cwsn')->group(function(){
        Route::get('{TestcategoryId}', [CwsnController::class, 'showCWSNCategory'])->name('assessor.cwsn.category'); //Ok
        Route::get('{pwd_category_id}/test/{TestTypeId}', [CwsnController::class, 'CwsnTestTypes'])->name('cwsn.test.types'); //Ok
        Route::post('cwsn-types-submit',[CwsnController::class, 'SubmitCwsnTest'])->name('cwsn.types.submit'); //Ok
        Route::get('pdf/{testTypeId}', [CwsnController::class, 'CwsnAdminManual'])->name('cwsn_admin_manual'); //Ok
    });

    /* Profile Settings */
    Route::get('trainer-profile', [ViewTrainerController::class, 'trainerProfile'])->name('trainer.profile'); //Ok
    Route::post('update-trainer-profile',[ViewTrainerController::class, 'trainerProfileUpdate'])->name('update.trainer.profile'); //Ok
});

/*  Students/Parent Routs */
Route::middleware('auth.auth_student')->prefix('student')->group(function () {
    /* Module-1 : Students Dashboard */
    Route::get('dashboard', [StudentRecordController::class, 'index'])->name('student.dashboard'); //Ok
    Route::get('view-dashboard', [StudentDashboardController::class,'dashboard'])->name('students.view.dashboard'); //Ok

    /* Profile Settings */
    Route::get('profile', [StudentRecordController::class,'viewProfile'])->name('student.profile'); //Ok
    Route::put('profile-update', [StudentRecordController::class,'updateProfile'])->name('profile.update'); //Ok    
});

/*Module-(5,6,7): Fitness Report Summary (Common for all Logins)  */
Route::get('lowerclass/summary', [ReportController::class, 'LowerClassTestSummary'])->name('lowerclasstestsummary'); //Ok
Route::get('higherclass/summary', [ReportController::class, 'HigherClassTestSummary'])->name('higherclasstestsummary'); //Ok
Route::get('cwsn/summary', [ReportController::class, 'cwsnTestSummary'])->name('trainer.cwsn.status'); //Ok
Route::get('reports/{id?}/{term_id?}', [ReportController::class, 'ViewFitnessReport'])->name('reports.view'); //Ok
Route::get('cwsn-reports/{id?}/{term_id?}', [ReportController::class, 'ViewCWSNFitnessReport'])->name('cwsn.reports.view'); //Ok



/* HeadQuarter Route's */
Route::middleware(['auth', 'role:9,2'])->prefix('cisce')->group(function() {

    /* Module-1 : School Summary */
    Route::get('/', [CicseController::class, 'index'])->name('cicse.dashboard'); //Ok
    Route::get('schoollist', [CicseController::class, 'schoolList'])->name('cicse.schoollist'); //Ok
    Route::post('/login-as-school', [CicseController::class, 'loginAsSchool'])->name('login.as.school'); //Ok
    Route::post('/leave-school', [CicseController::class, 'leaveSchool'])->name('leave.school'); //Ok
    Route::get('cisce-school-profile/{id?}', [SchoolRecordController::class, 'viewProfile'])->name('cisce.update.profile'); //Ok
    Route::put('profile/{id}', [SchoolRecordController::class,'updateProfile'])->name('cisce.school.profile.update'); //Ok
    Route::get('school/{id}', [CicseController::class, 'schoolDetail'])->name('cicse.school.view'); //Ok
    
    /* Module-2 : Payment & Assessments Status */
    Route::get('schools-assessement-status',[CicseController::class, 'schoolAssessmentStatus'])->name('schools.assessement.status'); //Ok
   
    /* Module-3: School Registration  */
    Route::get('add-school',[CicseController::class, 'addSchoolView'])->name('cicse.addschool');//Ok
    Route::post('approve-school',[CicseController::class, 'approveSchool'])->name('cicse.approveSchool');
    Route::get('/get-districts/{state_id}', [CicseController::class, 'getDistricts'])->name('get-districts'); //Ok
    Route::post('add-school-record',[CicseController::class, 'addSchoolRecord'])->name('add-school-record');  //Ok

    /* Module-4 : Admin Roles */
    Route::get('view-superadmin',[CicseController::class, 'viewSuperAdmin'])->name('view.superadmin');  //Ok
    Route::post('add-superadmin',[CicseController::class, 'addSuperAdmin'])->name('add.superadmin');   //Ok
    Route::get('edit-superadmin', [CicseController::class, 'getSuperAdmin'])->name('get.superadmin');   //Ok
    Route::post('update-superadmin', [CicseController::class, 'updateSuperAdmin'])->name('update.superadmin'); //Ok
    Route::post('delete-superadmin', [CicseController::class, 'deleteSuperAdmin'])->name('delete.superadmin');  //Ok
    Route::post('export-credentials', [CicseController::class, 'exportCredentials'])->name('export.credentials'); //Ok
    
    /* Sending Reports and Emails */
    Route::prefix('bulk-email')->group(function (){
        Route::get('send-report-view', [CicseController::class, 'bulkReportEmailView'])->name('send.report.view');
        Route::post('validate-email', [CicseController::class, 'validateEmail'])->name('validate.email');
        Route::post('download-invalid-emails', [CicseController::class, 'downloadInvalidEmails'])->name('download.invalid.emails');
        Route::post('send-bulk-emails', [CicseController::class, 'sendBulkEmail'])->name('send.bulk.emails');
    });

    Route::get('audit-logs', [CicseController::class, 'AuditLogs'])->name('audict.logs'); //Ok

    /* Routes for restoring the data */
    Route::prefix('restoration')->group(function () {
        Route::get('/deletedStudent', [CicseController::class, 'DeletedStudent'])->name('admin.restore.index');
        Route::get('/school/{school_id}', [CicseController::class, 'showSchoolRecords'])->name('admin.restore.school');
        Route::post('/record/{id}/restore', [CicseController::class, 'restore'])->name('admin.restore.execute');
        Route::post('restore-students',[CicseController::class, 'RestoreStudents'])->name('restore.students.data');
    });


    Route::prefix('tot')->group(function() {
        Route::get('/', [TOTController::class, 'index'])->name('tot.index');
        Route::get('/create', [TOTController::class, 'create'])->name('tot.create');
        Route::post('/store', [TOTController::class, 'store'])->name('tot.store');
        Route::get('tot/{id}/manage', [TotController::class, 'manage'])->name('tot.manage');

        /* Copy Students data */
        Route::post('/tot/{program}/copy-students', [TotController::class, 'copyStudents'])->name('tot.copyStudents');
        Route::get('/tot/{program}/students', [TotController::class, 'getStudents'])->name('tot.getStudents');

        /* Manage TOT Trainer's data */
        Route::get('/template/download', [TOTController::class, 'DownloadTrainerTemplate'])->name('tot.template.download');
        Route::get('tot/{id}/export-credentials', [TotController::class, 'exportCredentials'])->name('tot.export_credentials');
        Route::post('/{totProgram}/import', [TOTController::class, 'importTrainers'])->name('tot.import');

        Route::get('/tot/{program}/trainer', [TotController::class, 'getTrainers'])->name('tot.getTrainers');
        Route::get('/trainer/{trainerMappingId}/details', [TOTController::class, 'getTrainerDetails'])->name('tot.trainer.details');

        // Bulk restoration  
        Route::post('/trainer/{trainerMappingId}/restore', [TOTController::class, 'restoreSingleTrainer'])->name('tot.trainer.restore')
        ->where('trainerMappingId', '[0-9]+');
        Route::post('/{totProgram}/restore', [TOTController::class, 'restoreTrainers'])->name('tot.restore');
        Route::post('tot/{id}/flush', [TotController::class, 'flushData'])->name('tot.flush');
    });
    

});

Route::post('get-cities', [SchoolRecordController::class, 'getDistrictList'])->name('school.getDistrict');
Route::post('get-zones', [SchoolRecordController::class, 'getZones'])->name('school.getZones'); //Ok








/* Self Registration */
Route::get('school-registration', [RegisterController::class, 'SchoolSelfRegistration'])->name('school.registration');
Route::post('register-school', [RegisterController::class, 'RegisterSchool'])->name('register.school');


/* Download Generated Report Cards */
Route::post('generate-reportcards', [ReportController::class, 'queueBulkReportCards'])->name('generate.reportcards');
Route::get('/fitness-report/available', [ReportController::class, 'CheckReportAvailablity'])->name('fitness.report.available');
Route::get('report/download/{batchId}', [ReportController::class, 'requestDownload'])->name('report.download.permanent');
Route::get('report/download/{batchId}/signed', [ReportController::class, 'downloadSigned'])->name('report.download.signed');



/*Routes need to be check and clear*/
Route::get('azure_from', [HomeController::class, 'azureFrom'])->name('azure_from');
Route::post('azure_upload', [HomeController::class, 'uploadToAzure'])->name('azure_upload');
Route::get('/move-files', [HomeController::class, 'moveFilesToAzure']);
Route::get('restore-data', [HomeController::class, 'TestArchivaleRestoration']);