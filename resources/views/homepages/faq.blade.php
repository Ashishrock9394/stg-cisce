@extends('layouts.app')
@section('title', 'CISCE | ' . $title)
@section('content')


   <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --yellow: #F5C200;
        --yellow-light: #FDF3CC;
        --yellow-border: #E8B800;
        --navy: #1E1E5A;
        --navy-soft: #2D2D7A;
        --text: #1A1A1A;
        --text-soft: #444;
        --text-muted: #888;
        --bg: #F4F5F9;
        --white: #FFFFFF;
        --border: #E0E2EC;
        --orange-link: #CC5500;
    }

    body {
        background: var(--bg);
        font-family: 'Inter', system-ui, sans-serif;
        color: var(--text);
    }

    .wrap {
        margin: 0 auto;
        padding: 2rem 1.25rem 4rem;
    }

    .faqt {
        margin-top: 80px;
        margin-bottom: 1.5rem;
    }

    /* ── PAGE TITLE ── */
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--navy);
        margin-bottom: 4px;
        letter-spacing: -0.3px;
    }

    .page-sub {
        font-size: 14px;
        color: var(--text-muted);
    }

    /* ── HEADER ROW: title left, search right on desktop ── */
    .faqt {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .faqt-left {
        flex: 1;
    }

    .search-box {
        width: 340px;
        flex-shrink: 0;
    }

    /* ── SEARCH ── */
    .search-row {
        position: relative;
    }

    .search-row input {
        width: 100%;
        padding: 11px 16px 11px 40px;
        border: 1.5px solid var(--border);
        border-radius: 0;
        background: var(--white);
        font-family: inherit;
        font-size: 14px;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
    }

    .search-row input:focus {
        border-color: var(--yellow-border);
        box-shadow: 0 0 0 3px rgba(245, 194, 0, 0.15);
    }

    .search-row svg {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
    }

    .result-count {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 6px;
        min-height: 18px;
    }

    /* ── TABS ── */
    .tabs {
        display: flex;
        gap: 0;
        margin-bottom: 1.5rem;
        border: 1.5px solid var(--border);
        border-radius: 0;
        overflow: hidden;
        background: var(--white);
    }

    .tab-btn {
        flex: 1;
        padding: 11px 8px;
        border: none;
        border-right: 1.5px solid var(--border);
        background: var(--white);
        font-family: inherit;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-soft);
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .tab-btn:last-child {
        border-right: none;
    }

    .tab-btn:hover:not(.active) {
        background: var(--yellow-light);
        color: var(--navy);
    }

    .tab-btn.active {
        background: var(--yellow);
        color: var(--navy);
        font-weight: 700;
    }

    .tab-icon {
        font-size: 14px;
    }

    /* ── PANELS ── */
    .panel {
        display: none;
    }

    .panel.on {
        display: block;
    }

    /* ── CATEGORY HEADER ── */
    .cat-group {
        margin-bottom: 1.6rem;
    }

    .cat-label {
        background: var(--yellow);
        color: var(--navy);
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.3px;
        padding: 8px 16px;
        border-radius: 0;
        border: 1px solid var(--yellow-border);
        display: block;
    }

    .cat-items {
        border: 1px solid var(--border);
        border-top: none;
        border-radius: 0;
        overflow: hidden;
        background: var(--white);
    }

    /* ── FAQ ROWS ── */
    .faq-row {
        border-bottom: 1px solid var(--border);
    }

    .faq-row:last-child {
        border-bottom: none;
    }

    .faq-q {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        padding: 14px 16px;
        font-family: inherit;
        font-size: 14.5px;
        font-weight: 500;
        color: var(--text);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        line-height: 1.5;
        transition: background 0.1s;
    }

    .faq-q:hover {
        background: #FAFBFF;
    }

    .faq-row.open .faq-q {
        background: var(--yellow-light);
        color: var(--navy);
        font-weight: 600;
    }

    .chevron {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        transition: transform 0.18s, color 0.15s;
    }

    .faq-row.open .chevron {
        transform: rotate(180deg);
        color: var(--navy);
    }

    .faq-a {
        display: none;
        padding: 12px 16px 16px;
        font-size: 14.5px;
        line-height: 1.7;
        color: var(--text-soft);
        background: #FAFBFF;
        border-top: 1px solid var(--border);
    }

    .faq-row.open .faq-a {
        display: block;
    }

    .faq-a strong {
        color: var(--navy);
        font-weight: 600;
    }

    .faq-a a {
        color: var(--orange-link);
    }

    .faq-a code {
        background: var(--yellow-light);
        color: var(--navy);
        padding: 1px 5px;
        border-radius: 0;
        font-size: 13px;
        border: 1px solid #e0d080;
    }

    .faq-a ol,
    .faq-a ul {
        padding-left: 20px;
        margin: 8px 0;
    }

    .faq-a li {
        margin-bottom: 4px;
        font-size: 14.5px;
    }

    .warn-note {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        background: var(--yellow-light);
        border-left: 3px solid var(--yellow-border);
        padding: 9px 12px;
        border-radius: 0;
        font-size: 14px;
        margin-top: 8px;
        color: #5A4400;
    }

    mark {
        background: var(--yellow);
        color: var(--navy);
        border-radius: 0;
        padding: 0 3px;
        font-weight: 500;
    }

    /* ── COMING SOON ── */
    .coming-soon {
        background: var(--white);
        border: 1.5px dashed var(--border);
        border-radius: 0;
        text-align: center;
        padding: 3rem 1.5rem;
        color: var(--text-muted);
        font-size: 14px;
    }

    .coming-soon em {
        font-style: normal;
        font-size: 26px;
        display: block;
        margin-bottom: 10px;
    }

    .no-results {
        text-align: center;
        padding: 3rem;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    /* ── MOBILE ── */
    @media (max-width: 768px) {

        /* Stack title + search vertically, full width */
        .faqt {
            flex-direction: column;
            gap: 12px;
            margin-top: 60px;
        }

        .faqt-left {
            width: 100%;
        }

        .search-box {
            width: 100%;
        }

        .page-title {
            font-size: 20px;
        }

        .page-sub {
            font-size: 13px;
        }

        .tab-btn {
            font-size: 13px;
            padding: 10px 4px;
        }

        .tab-icon {
            display: none;
        }

        .cat-label {
            font-size: 13px;
            padding: 7px 12px;
        }

        .faq-q {
            font-size: 13.5px;
            padding: 12px 12px;
        }

        .faq-a {
            font-size: 13.5px;
            padding: 10px 12px 14px;
        }

        .faq-a li {
            font-size: 13.5px;
        }

        .warn-note {
            font-size: 13px;
        }
    }
</style>

    <div class="container">
    <div class="wrap">

        <div class="faqt">
            <!-- Title + subtitle -->
            <div class="faqt-left">
                <div class="page-title">Frequently Asked Questions</div>
                <div class="page-sub">Support &amp; help for schools, trainers and parents</div>
            </div>

            <!-- Search -->
            <div class="search-box">
                <div class="search-row">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="6.5" cy="6.5" r="4.5" />
                        <path d="M10 10l3.5 3.5" />
                    </svg>
                    <input type="text" id="si" placeholder="Search questions..." oninput="doSearch(this.value)" />
                </div>
                <div class="result-count" id="rc"></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" id="tb-s" onclick="goTab('s',this)">
                <span class="tab-icon">🏫</span> Schools &amp; Principal
            </button>
            <button class="tab-btn" id="tb-t" onclick="goTab('t',this)">
                <span class="tab-icon">👩‍🏫</span> Assessor / Trainer
            </button>
            <button class="tab-btn" id="tb-p" onclick="goTab('p',this)">
                <span class="tab-icon">👨‍👩‍👧‍👦</span> Parents
            </button>
        </div>

        <!-- Panels -->
        <div id="ps" class="panel on"></div>
        <div id="pt" class="panel"></div>
        <div id="pp" class="panel"></div>
        <div id="pq" class="panel"></div>

    </div>
</div>

    

    <script>
        const D = {
            s: [{
                    cat: "Login & Access",
                    items: [{
                        q: "How do I log in as a Principal / HM?",
                        a: `Visit <strong><a href="https://active.cisce.org/">active.cisce.org</a></strong> and click Login. From the dropdown select <strong>School</strong>, enter your credentials, tick the checkbox and click Login.<br>If still unable to log in, go to <a href="https://active.cisce.org/contact-us">Contact Us </a>, find your cluster's contact details and reach out by phone or email.`
                    }]
                },
                {
                    cat: "Manage Students",
                    items: [{
                        q: "How do I upload student data?",
                        a: `<ol><li>Go to <strong>Manage Student</strong> module. New schools see the upload page automatically; others click <strong>Upload Data</strong>.</li>
                            <li>Click <strong>Choose File</strong> and select your <code>StudentDataUploadFormat.xlsx</code>.</li>
                            <li>Click <strong>Upload</strong> → confirm popup by clicking <strong>Import</strong>.</li>
                           <li>Check status via <strong>View History</strong>: <br><em style="color: green;">Completed</em> : File uploaded successfully  <br><em style="color: orange;">Processing</em> : Wait a few minutes and refresh  <br><em style="color: red;">Failed</em> : Try uploading the file again 
                            .</li></ol>`
                    },
                    {
                        q: "How do I get the student upload template?",
                        a: `Go to the <strong>Manage Student</strong> module and click <strong>Upload Data</strong>. A modal will open (for new schools, the upload page opens automatically).<br>
                            Click <strong>Download Template</strong> to download the file <code>StudentDataUploadFormat.xlsx</code>.<br>
                            <span class="warn-note">⚠ Do not rename or change the file format; the system will reject altered files.</span>`
                    },
                    {
                        q: "What details are required in the template?",
                        a: `<b>The template requires: </b> <br>· School Code<br> · Admission Number (unique) <br> · Student Name <br> · Gender (M/F, capitals)<br> · Class (Roman numerals: I, II, III…) <br>· Section<br> · Roll Number (numeric, unique per class) <br>· Date of Birth (DD-MM-YYYY) <br>· Email <br>· RPWD <br>· AAPAR ID (optional)`
                    },
                    {
                        q: "How should I enter gender and class?",
                        a: `<strong>Gender:</strong> Capital letters only — M for male, F for female.<br><strong>Class:</strong> Capital Roman numerals (Class 1 = I, not "i").`
                    },
                    {
                        q: "What are the rules for roll and admission numbers?",
                        a: `<ul><li>Each student must have a <strong>unique admission number</strong>.</li><li>Roll numbers should be numeric and cannot be repeated within the same class and section. (e.g., two students in Class 1 Section A cannot have roll number 1).</li></ul>`
                    },
                    {
                        q: "How should I enter date of birth and email?",
                        a: `<strong>Date of Birth:</strong> DD-MM-YYYY format.<br><strong>Email:</strong> Student's or a parent's email address.`
                    },
                    {
                        q: "How do I submit the completed template? ",
                        a: `After completing the template, navigate to <strong>Manage Student</strong> → <strong>Upload Data</strong>, select the file, and click <strong>Upload</strong>. The system will then process the data.`
                    },
                    {
                        q: "Can I add a single student without Excel?",
                        a: `Yes. Go to <strong>Manage Student</strong> → <strong>Add Student</strong>, fill all * fields and save. No Excel needed.`
                    },
                    {
                        q: "Can I update student details? <span>OR</span> I have uploaded Student Personal Profile Data incorrectly. What should I do?",
                        a: `Yes — all fields except <strong>admission number</strong> can be updated.
                            <p><b>Method 1: Update from Manage Students (for Single Student)</b></p>
                        <ol>
                            <li>Go to <strong>Manage Student</strong>.</li>
                            <li>In the <strong>Action</strong> column, click the <strong>pencil icon</strong> next to the student.</li>
                            <li>The update form will open. Modify the required details.</li>
                            <li>Click on the <strong>Update</strong> button.</li>
                            <li>A success message will appear after updating.</li>
                        </ol>

                            <p><b>Method 2: Update using Excel (for Multiple Students)</b></p>
                        <ol>
                            <li>Edit your <code>StudentDataUploadFormat.xlsx</code> with the correct data.</li>
                            <li>Go to <strong>Manage Student</strong> → <strong>Upload Data</strong>.</li>
                            <li>Select the updated file and choose <strong>Overwrite</strong>.</li>
                            <li>Click <strong>Upload</strong>. The system will update the records within a few minutes.</li>
                        </ol>

                        <span class="warn-note">⚠ Never change the admission number — it will create a new record instead of updating the existing one.</span>`
                    },
                    {
                        q: "What happens if I change the admission number?",
                        a: `A <strong>new student record</strong> is created instead of updating the existing one. Always keep admission numbers unchanged.`
                    },
                    
                    {
                        q: "How do I delete a duplicate or wrongly added student?",
                        a: `If a student has been added by mistake or a duplicate record exists, you can delete it as follows:
                                <ol>
                                    <li>Go to <strong>Manage Student</strong>.</li>
                                    <li>Locate the incorrect or duplicate student record.</li>
                                    <li>Select the checkbox next to the student's name.</li>
                                    <li>Click <strong>Select Action</strong> → <strong>Delete</strong>.</li>
                                    <li>In the confirmation popup, click <strong>Confirm</strong>.</li>
                                </ol>
                              <span class="warn-note">⚠  The selected student record will be permanently removed from the system. </span>`
                    },
                    {
                        q: "What should I do if a student has left the school?",
                        a: `Go to <strong>Manage Student</strong>, find the student, change status from <strong>Active</strong> to <strong>Transfer</strong>.`
                    },
                    {
                        q: "Can I download uploaded student records?",
                        a: `Yes, you can download the uploaded student records. Go to the <strong>Manage Student</strong> module, 
                            choose the number of records to download, or select <strong>All</strong> to download all students (default 
                            is 100). To download class- and section-wise data, click <strong>Select Class/Section</strong> and choose the desired 
                            class and Section (by default, all classes and sections are selected). After applying the filters, 
                            click <strong>Export</strong> and select <strong> Bulk Student Download </strong> to download the file.`
                    },
                    {
                        q: "How do I download a student's ID card?",
                        a: `You can download student ID cards from the <strong>Manage Student</strong> module. Choose the 
                            number of student records you want, select <strong>All</strong> to include all students (default is 100). To 
                            download class- or section-wise I-cards, click the <strong>Select Class/Section</strong> filter and choose the 
                            desired class and Section (by default, all classes and sections are selected). After applying the filters, 
                            click <strong>Export</strong> and select <strong>Generate I-Card</strong>. The ID cards will be downloaded as a ZIP file.`
                    },
                    {
                        q: "What should I do if the upload fails or shows errors?",
                        a: `<ul>
                                <li>Check that the template name and file format are correct.</li>
                                <li>Verify that all required fields are filled correctly (unique admission numbers, unique roll numbers, correct date format DD-MM-YYYY, and capital letters where needed).</li>
                                <li>Ensure there are no extra spaces or special characters.</li>
                                <li>If issues persist, contact your cluster support via <a href="https://active.cisce.org/contact-us" target="_blank">Contact Us</a>.</li>
                            </ul>`
                    },
                    {
                        q: "How do I promote students to the next academic session?",
                        a: `There are two methods available:
                            <p><strong>Method A — Excel Sheet Upload</strong></p>
                            <ol>
                                <li>Go to <strong>Manage Students</strong> and change the display dropdown to <strong>All</strong>.</li>
                                <li>Filter by Class and Section, check the student checkboxes, click <strong>Export</strong> → <strong>Bulk Student Download</strong>. Or click <strong>Upload Data</strong> and use the orange <strong>Download All Students</strong> link to download all records at once.</li>
                                <li>Open the Excel file and update <strong>Class</strong>, <strong>Section</strong>, <strong>Roll No.</strong>, and <strong>RPWD</strong> columns for each student.</li>
                                <li>Go back to <strong>Upload Data</strong>, choose the file and click <strong>Upload</strong>.</li>
                                <li>Confirm the prompt. Data will appear in the <strong>2026-27 session</strong> after processing.</li>
                            </ol>
                            <p><strong>Method B — Bulk Action (no file needed)</strong></p>
                            <ol>
                                <li>Go to <strong>Manage Students</strong>, change display to <strong>All</strong>.</li>
                                <li>Filter by Class and Section, then tick the checkboxes of students to promote.</li>
                                <li>Click <strong>Bulk Action</strong> → <strong>Promote Students</strong>.</li>
                                <li>Click <strong>Yes, Proceed</strong> in the confirmation dialog.</li>
                            </ol>
                            <span class="warn-note">⚠ Promotion cannot be undone or reverted. Ensure all data is correct before proceeding.</span>`
                    },
                    {
                        q: "Which columns do I need to update in the Excel file for promotion?",
                        a: `Update the following columns for each student in the downloaded Excel:
                            <ul>
                                <li><strong>Class</strong> — the new class the student is being promoted to</li>
                                <li><strong>Section</strong> — the new section for the 2026-27 session</li>
                                <li><strong>Roll No.</strong> — the new roll number for the 2026-27 session</li>
                                <li><strong>RPWD</strong> — enter <strong>YES</strong> (capitals) if the student is under the RPWD category, or <strong>NO</strong> if not</li>
                            </ul>
                            <span class="warn-note">⚠ Do not change the admission number or column headers under any circumstances. Changing the admission number will create a duplicate record instead of updating the existing one.</span>`
                    },
                    {
                        q: "What is the RPWD column in the promotion template?",
                        a: `The <strong>RPWD</strong> column indicates whether a student falls under the Rights of Persons with Disabilities category. Enter <strong>YES</strong> (in capitals) if the student is under RPWD, or <strong>NO</strong> if not. This field must not be left blank.`
                    },
                    {
                        q: "What happens to Class 12 students when promoted?",
                        a: `Upon promotion, all <strong>Class 12 students are automatically moved to the Transfer list</strong>. They will no longer appear in the active student list for the new session.`
                    },
                    {
                        q: "Can I undo or reverse a promotion?",
                        a: `<strong>No.</strong> Promotion cannot be undone or reverted once confirmed. Always verify all student data is correct before proceeding with promotion.`
                    },
                    {
                        q: "How do I check if the promotion was successful?",
                        a: `<p><strong>For Method B (Bulk Action):</strong></p>
                            <ol>
                                <li>Click <strong>View History</strong> in the top-right of Manage Students.</li>
                                <li>Select <strong>Promotion History</strong>.</li>
                                <li>The list shows number of students Promoted and Transferred with a status of Completed.</li>
                            </ol>
                            <p><strong>For Method A (Excel Upload):</strong></p>
                            <ol>
                                <li>Click <strong>View History</strong> → <strong>Upload History</strong>.</li>
                                <li>Status shows <em style="color:green;">Completed</em>, <em style="color:orange;">Queued</em>, or <em style="color:red;">Failed</em>.</li>
                                <li>If Failed, download the error file, fix the issues, and re-upload.</li>
                            </ol>
                            You can also verify by clicking the <strong>2026-27 session</strong> tab in the Manage Students header to view promoted records.`
                    },
                    {
                        q: "How do I switch between the 2025-26 and 2026-27 sessions in Manage Students?",
                        a: `In the <strong>Manage Students</strong> table header, click the session toggle in the centre — you'll see tabs for <strong>2025-2026</strong> and <strong>2026-2027</strong>. Click the relevant tab to switch and verify records for that session.`
                    },
                    {
                        q: "The downloaded Excel file shows 2025-26 data. Is that correct?",
                        a: `Yes, that is expected. The downloaded Excel file always contains <strong>2025-26 session data</strong>. When you update the class, section, roll number, and RPWD columns and upload it back, the data will automatically appear under the <strong>2026-27 session</strong>.`
                    },
                    {
                        q: "Can I update a student's class, section, or roll number after promotion?",
                        a: `Yes. You can update individual student details before or after promotion:
                            <ol>
                                <li>Go to <strong>Manage Students</strong> and locate the student.</li>
                                <li>Click the <strong>pencil (Edit) icon</strong> in the Edit column on the right.</li>
                                <li>Update the required details (class, section, roll number, etc.).</li>
                                <li>Click <strong>Save / Update</strong> to confirm.</li>
                            </ol>`
                    },
                    {
                        q: "How long is upload history kept in the system?",
                        a: `Upload history is retained for <strong>15 days only</strong>. After 15 days, all upload history is permanently deleted from the system. Make sure to verify and save your uploaded data within this period.`
                    },
                    {
                        q: "Can I add new students while doing a promotion upload?",
                        a: `Yes. You can add new student records directly in the same promotion Excel template. New entries in the file will appear in the <strong>2026-27 session</strong> after upload, just like the promoted students.`
                    }
                    ]
                },
                {
                    cat: "Manage Trainers",
                    items: [{
                                q: "How do I add a trainer to my school?",
                                a: `Go to the <strong>Manage Trainers</strong> module and click <strong>Add Trainers</strong>. A form will open where you need to enter the trainer’s details (all fields marked with * are mandatory). After filling in the information and checking all required boxes, click the <strong>Submit</strong> button. The trainer will be added, and you will see a confirmation message: <strong>“Trainer Saved Successfully!”</strong> By default, the newly added trainer is <strong>unmapped</strong>, so make sure to map the trainer to your school to complete the process.`
                            },
                        {
                            q: "Submit shows 'Email ID or Mobile Number is already registered.' What do I do?",
                            a: `This message means the trainer already exists in the main database. First, check your trainer list to see if the trainer is already mapped to your school. If the trainer is not in your list, search for the trainer using their email ID, trainer ID, or name. Once you find the trainer’s existing record, review the details and click <strong>Map</strong> to connect the trainer to your school.`
                        },
                        {
                            q: "How do I map or unmap a trainer?",
                            a: `
                            <p>Click <strong>Registered With School</strong> to view the complete trainer list. You can map or unmap trainers using the following methods:</p>

                            <b>1. Using the Search Box</b>
                            <ul>
                                <li>Click the search box.</li>
                                <li>Enter the trainer’s <strong>Name</strong>, <strong>Email ID</strong>, or <strong>Trainer ID</strong>.</li>
                                <li>Click <strong>Search</strong> to view matching trainers.</li>
                                <li>Go to the <strong>Action</strong> column and click <strong>Map</strong> or <strong>Unmap</strong>.</li>
                            </ul>

                            <b>2. Using the Trainer List</b>
                            <ul>
                                <li>Scroll through the trainer list to find the trainer.</li>
                                <li>Check the <strong>Action</strong> column next to the trainer’s name.</li>
                                <li>Click <strong>Map</strong> or <strong>Unmap</strong> as needed.</li>
                            </ul>`
                        },
                        {
                            q: "Why should I map a trainer?",
                            a: `Only mapped trainers can: log in with your school, access the Take Test module, and check student reports.`
                        },
                        {
                            q: "Is there a limit on how many trainers I can add?",
                            a: `No maximum limit. Add as many trainers as needed.`
                        },
                        {
                            q: "Can I edit a trainer's details?",
                            a: `Only the <strong>trainer themselves</strong> can edit details via <strong>Edit Profile</strong>. Schools cannot edit trainer details.`
                        },
                        {
                            q: "How do I download login credentials for all trainers?",
                            a: `In <strong>Manage Trainers</strong>, click the <strong>Credential</strong> button. An Excel sheet with all credentials downloads automatically.`
                        }
                    ]
                },
                {
                    cat: "Fitness Test Status & Assessment Report",
                    items: [{
                                q: "What is the Fitness Test Status module?",
                                a: `<p>The <strong>Fitness Test Status</strong> module allows schools to view and track student fitness test data and completion status.</p>`
                            },
                            {
                            q: "Which classes are available in the Fitness Test Status module?",
                            a: `
                            <p>The Fitness Test Status module is divided into two sections:</p>
                            <ul>
                                <li><strong>Classes 1 to 3:</strong> Separate Fitness Test Status section</li>
                                <li><strong>Classes 4 to 12:</strong> Separate Fitness Test Status section</li>
                            </ul>`
                        },
                        {
                            q: "Can I export fitness test data?",
                            a: `
                            <p><strong>Yes.</strong> Click on the <strong>Export</strong> button and select <strong>Excel</strong> to download the displayed fitness test data for record keeping, analysis, or sharing.</p>`
                        },
                        {
                            q: "What do 'Complete' and 'Incomplete' statuses mean?",
                            a: `<strong>Complete:</strong> All assigned fitness tests taken successfully.<br><strong>Incomplete:</strong> Some or all tests still pending.`
                        },
                        {
                            q: "Can I check the status of individual tests?",
                            a: `Yes. Select or unselect specific tests in the Fitness Test Status module.`
                        },
                        {
                            q: "What is the Assessment Report module?",
                            a: `
                            <p>The <strong>Assessment Report</strong> module allows schools to generate and download student fitness assessment report cards.</p>`
                        },
                        {
                            q: "How do I view a student's report?",
                            a: `Go to <strong>Assessment Report / Fitness Test Status</strong>, select the class, find the student and click <strong>View</strong>.`
                        },
                        {
                            q: "The report shows 'NA' in the Level field. What does that mean?",
                            a: `NA means the student's DOB is incorrect or not within the age criteria:<ul><li><strong>Class 1–3:</strong> Age 3–8 years</li><li><strong>Class 4–12:</strong> Age 9–18 years</li></ul>Correct the DOB to resolve this.`
                        },
                        {
                            q: "How can I request bulk report card downloads?",
                            a: `
                            <p>Follow these steps to request bulk report card downloads:</p>
                            <ul>
                                <li>Select students using checkboxes.</li>
                                <li>Click <strong>Export</strong>.</li>
                                <li>Select <strong>Request Report Cards</strong>.</li>
                                <li>Confirm the request.</li>
                            </ul>`
                        },
                        {
                            q: "How can I check report generation status?",
                            a: `<p>Click on <strong>Available Downloads</strong> to view the report generation status.</p>`
                        },
                        {
                            q: "What do the report generation statuses mean?",
                            a: `<ul><li><strong>Processing:</strong> Report generation is in progress.</li>
                                <li><strong>Completed:</strong> Report is ready for download.</li>
                                <li><strong>Failed:</strong> Report generation failed and may need to be retried.</li></ul>`
                        },
                        {
                            q: "How can I download bulk report cards?",
                            a: `
                            <p>When the report status changes to <strong>Completed</strong>, click on <strong>Ready to Download</strong> to download the bulk report cards.</p>`
                        },
                        {
                            q: "How long are generated reports available?",
                            a: `
                            <p>Generated reports remain available for <strong>7 days</strong> only. After that, they are permanently deleted from the system.</p>`
                        },
                        
                    ]
                },
                {
                    cat: "Upload Fitness Assessment Data",
                    items: [{
                                q: "What is the Upload Test Data module?",
                                a: `The <strong>Upload Test Data</strong> module is used to upload student assessment and fitness test data in bulk using Excel templates.`
                            },
                        {
                            q: "Where can I find the Upload Test Data option?",
                            a: `You can find the <strong>Upload Test Data</strong> option on the Landing Page. Click on <strong>Upload Test Data</strong> to access the module.<br><br><span class="warn-note">⚠ Please note that this module is accessible only through the School Login.</span>`
                        },
                        {
                            q: "What is the difference between Template and Sample files?",
                            a: `
                            <ul>
                                <li><strong>Template File:</strong> Official Excel file used to enter and upload actual student data.</li>
                                <li><strong>Sample File:</strong> Reference file showing the correct format and example entries.</li>
                            </ul>`
                        },
                        {
                            q: "Can I change the Excel file name or column headings?",
                            a: `<strong>No.</strong> Do not change the file name, sheet structure, or column headings. Doing so may cause upload failure.`
                        },
                        {
                            q: "What are the Template Options available?",
                            a: `
                            <p>There are two template options available:</p>
                            <ul>
                                <li><strong>Incomplete:</strong> Downloads only students who have not participated.</li>
                                <li><strong>All:</strong> Downloads all student records.</li>
                            </ul>`
                        },
                        {
                            q: "Can I download multiple templates at once?",
                            a: `
                            <p><strong>Yes.</strong> You can download multiple templates at once by following these steps:</p>
                            <ul>
                                <li>Select multiple skills using checkboxes.</li>
                                <li>Click <strong>Export</strong>.</li>
                                <li>Choose <strong>Download Selected Template</strong>.</li>
                                <li>The selected templates will be downloaded in a <strong>ZIP file</strong>.</li>
                            </ul>`
                        },
                        {
                            q: "What values are allowed in FMS tests?",
                            a: `
                            <p>Only the following values are allowed in FMS tests:</p>
                            <ul>
                                <li><strong>Y</strong></li>
                                <li><strong>y</strong></li>
                                <li><strong>Blank cell</strong></li>
                            </ul>
                            <p>Any other value such as numbers, letters, or special characters will be rejected.</p>`
                        },
                        {
                            q: "Why is my FMS data not saving?",
                            a: `
                            <p>Ensure the student status is changed from <strong>Not Attempted</strong> to <strong>Attempted</strong> before entering observations. Otherwise, the data may not save correctly.</p>`
                        },
                        {
                            q: "What should I do if a student did not attempt the test?",
                            a: `
                            <p>If a student did not attempt the test, leave the required fields blank and mark the status as <strong>Not Attempted</strong> wherever applicable.</p>`
                        },
                        {
                            q: "What values are allowed for count-based tests?",
                            a: `
                            <p>For count-based tests such as <strong>Flamingo Balance Test</strong>, <strong>Partial Curl Up</strong>, and <strong>Push Ups</strong>, only non-negative numeric values are allowed.</p>
                            <p>Examples of valid values: <strong>0, 1, 2, 3, etc.</strong></p>
                            <p>Negative numbers, letters, or special characters are not allowed.</p>`
                        },
                        {
                            q: "What values are allowed for time-based tests?",
                            a: `
                            <p>For time-based tests such as <strong>Plate Tapping</strong>, <strong>50 mt Dash</strong>, and <strong>600 Meter Run/Walk</strong>, only numeric values are allowed in the following format:</p>
                            
                            <ul>
                                <li><strong>Minutes:</strong> 0–59</li>
                                <li><strong>Seconds:</strong> 0–59</li>
                                <li><strong>Milliseconds:</strong> 0–999</li>
                            </ul>`
                        },
                        {
                            q: "What values are allowed for BMI data?",
                            a: `
                            <p>For BMI data, enter only non-negative numeric values for the following fields:</p>
                            <ul>
                                <li><strong>Height:</strong> cm/mm</li>
                                <li><strong>Weight:</strong> kg/gm</li>
                            </ul>
                            <p>If the student did not attempt the test, leave the field blank.</p>`
                        },
                        {
                            q: "How do I upload the completed Test template?",
                            a: `
                            <p>Follow these steps to upload the completed template:</p>
                            <ul>
                                <li>Click the <strong>Upload Data</strong> button on the top right corner in the Upload Test Data module.</li>
                                <li>Drag and drop or browse the completed template file.</li>
                                <li>Click <strong>Upload File</strong>.</li>
                                <li>Confirm the import action.</li>
                                <li>The file will be processed. You can check <strong>View History</strong> to track the file status.</li>
                            </ul>`
                        },
                        {
                            q: "Can I upload the Sample file directly?",
                            a: `
                            <p><strong>No.</strong> The Sample file is only for reference. Always upload the official <strong>Template file</strong> after filling in the data.</p>`
                        },
                        {
                            q: "How can I check whether my upload was successful?",
                            a: `
                            <p>You can use the <strong>View History</strong> option to check the upload status and track your files. It will show whether uploads are <strong>completed</strong>, <strong>queued</strong>, or <strong>failed</strong>.</p>`
                        },
                        {
                            q: "What do the upload statuses mean?",
                            a: `
                            <ul>
                                <li><strong>Completed:</strong> Upload processed successfully.</li>
                                <li><strong>Queued:</strong> Upload is waiting for processing.</li>
                                <li><strong>Failed:</strong> Upload contains errors and was not processed successfully.</li>
                            </ul>`
                        },

                        {
                            q: "How can I identify upload errors?",
                            a: `
                            <p>In the <strong>View History</strong> section, failed uploads provide an <strong>Error File</strong> that helps you identify formatting or data entry mistakes. You can review this file, correct the errors, and re-upload the template.</p>`
                        },
                        {
                            q: "Do schools need to upload test data through Excel if the PE Teacher has already taken the test on the portal?",
                            a: `
                            <p><strong>No.</strong> If the PE Teacher has already conducted the test through the <strong>Take Test</strong> module and saved the data on the portal, there is no need to upload the same test data again through Excel.</p>
                            <p>The <strong>Upload Test Data</strong> module should only be used when the PE Teacher has recorded the assessment data offline and the school wants to upload that data in bulk using the Excel template.</p>`
                        },
                        
                    ]
                },
                {
                    cat: "Profile Settings",
                    items:[
                        {
                            q: "How can I access the profile settings?",
                            a: `
                            <p>Click on the <strong>Profile Avatar</strong> available at the top-right corner of the dashboard and select <strong>Edit Profile</strong> from the dropdown menu.</p>`
                        },
                        {
                            q: "What details can be updated in the profile section?",
                            a: `
                            <p>You can update the following details in the profile section:</p>
                            <ul>
                                <li>Principal Name</li>
                                <li>Designation</li>
                                <li>Contact Number</li>
                                <li>Email ID</li>
                                <li>Other school-related details</li>
                            </ul>`
                        },
                        {
                            q: "Can schools upload their logo on the portal?",
                            a: `
                            <p><strong>Yes.</strong> Schools can upload their official school logo from the profile section.</p>`
                        },
                        {
                            q: "Which image formats are supported for school logo upload?",
                            a: `
                            <p>Schools can upload image files in the following formats:</p>
                            <ul>
                                <li><strong>PNG</strong></li>
                                <li><strong>JPEG</strong></li>
                            </ul>`
                        },
                        {
                            q: "Can the Principal and PE Head signatures be uploaded?",
                            a: `
                            <p><strong>Yes.</strong> Both the <strong>Principal’s signature</strong> and <strong>PE Head’s signature</strong> can be uploaded through the profile page.</p>`
                        },
                        {
                            q: "How can I upload the school logo?",
                            a: `
                            <p>Follow these steps to upload the school logo:</p>
                            <ul>
                                <li>Open <strong>Edit Profile</strong>.</li>
                                <li>Click on the <strong>Upload</strong> button beside the <strong>School Logo</strong> field.</li>
                                <li>Select the image file from your device.</li>
                                <li>Click <strong>Update Profile</strong> to save the changes.</li>
                            </ul>`
                        },
                        {
                            q: "How can I upload the Principal and PE Head signatures?",
                            a: `
                            <p>Follow these steps to upload the Principal and PE Head signatures:</p>
                            <ul>
                                <li>Go to the <strong>Profile</strong> page.</li>
                                <li>Click on the <strong>Upload</strong> button beside the signature field.</li>
                                <li>Select the signature image file from your device.</li>
                                <li>Click <strong>Update Profile</strong> to save the changes.</li>
                            </ul>`
                        },
                        {
                            q: "How can I verify whether the logo was uploaded successfully?",
                            a: `
                            <p>Once the logo is uploaded successfully, the new school logo will become visible on the <strong>Profile</strong> page.</p>`
                        },
                        {
                            q: "Where will the uploaded logo and signatures appear?",
                            a: `
                            <p>The uploaded school logo and the signatures of the <strong>Principal</strong> and <strong>PE Head</strong> are displayed on the student assessment report cards.</p>`
                        },
                    ]
                }
            ],
            t: [{
                    cat: "Login & Access",
                    items: [{
                            q: "How do I log in as an Assessor / Trainer?",
                            a: `Visit <strong><a href="https://active.cisce.org/">active.cisce.org</a></strong> and click Login. From the dropdown select <strong>Trainer</strong>, enter your credentials, tick the checkbox and click Login.<br>If still unable to log in, go to <a href="https://active.cisce.org/contact-us">Contact Us </a>, find your cluster's contact details and reach out by phone or email.`
                        },
                        {
                            q: "I can't see the 'Take Test' or 'Fitness Test Status' module after logging in.",
                            a: `Your school may not be mapped to you yet, or the current term may have ended. Please contact your school administrator for assistance.`
                        },
                        {
                            q: "How do I get my login credentials?",
                            a: `Contact your school administrator. They can download credentials from <strong>Manage Trainer</strong> via the <strong>Credential</strong> button.`
                        },
                        {
                            q: "Can I edit my profile if incorrect details were added?",
                            a: `Yes. Log in → click profile avatar → <strong>Edit Profile</strong> → update details.<br><span class="warn-note">⚠ Changing email or DOB changes your login credentials. Use the new ones to log in.</span>`
                        },
                        {
                            q: "I forgot my credentials after updating email and DOB. What do I do?",
                            a: `Contact your school administrator — they can retrieve your new credentials from the <strong>Manage Trainer</strong> module.`
                        }
                    ]
                },
                {
                    cat: "Taking Assessments",
                    items: [{
                            q: "How do I take an assessment through the portal?",
                            a: `<p>Follow these steps to start taking an assessment on the <strong>Active CISCE</strong> portal:</p>
                                <ul>
                                    <li>Click on the <strong>Take Test</strong> module.</li>
                                    <li>A page will open showing all test categories class-wise and age-group-wise.</li>
                                    <li>Each category contains related tests along with demonstration videos.</li>
                                    <li>Click on the <strong>Play</strong> icon to watch how the test should be performed.</li>
                                    <li>Select the <strong>Class</strong>, <strong>Section</strong>, and <strong>Student</strong> for whom you want to conduct the assessment.</li>
                                    <li>Open the required test and perform the assessment.</li>
                                    <li>Enter the observations or test data.</li>
                                    <li>Click <strong>Submit</strong> to save the test data successfully.</li>
                                </ul>`
                        },
                        {
                            q: "Some students are not visible while taking a test. How can I view them?",
                            a: `By default, the test filter is set to <strong>Incomplete</strong>, so students who have already completed the test will not appear. Go to the main page of the Test Module and change the toggle from <strong>Incomplete</strong> to <strong>All</strong>. This will display all students, including completed and incomplete tests.`
                        },
                        {
                            q: "I took the wrong test for a student. Can I retake it?",
                            a: `Yes. Go to the main page of the Take Test Module and change the filter from <strong>Incomplete</strong> to <strong>All</strong>. Then open the test, select the student, and click <strong>Retake</strong>.<br><span class="warn-note">⚠ Retake permanently deletes the previous test data and cannot be undone. The student must complete the test again to save new results.</span>`
                        },
                        {
                            q: "Test data was visible before but is now missing.",
                            a: `This usually happens when the <strong>Retake</strong> option was selected.<br> Once <strong>Retake</strong> is clicked, the previous test record is permanently deleted and cannot be restored.<br> To save data again, complete the test and submit the new test results.`
                        },
                       
                        {
                            q: "How do I confirm test data has been saved?",
                            a: `Go to <strong>Fitness Test Status</strong>, filter by class or test status, search student name, click <strong>View</strong>.`
                        },
                        {
                        q: "Can I filter data in the Fitness Test Status module?",
                        a: `Yes. You can filter by <strong>class</strong>, <strong>test status</strong>, and <strong>test type</strong> to narrow down results and find specific information quickly.`
                    },
                    {
                        q: "How can I view a detailed report of a specific student's test results?",
                        a: `<ol>
                                <li>Go to the <strong>Fitness Test Status</strong> module.</li>
                                <li>Select the appropriate class group.</li>
                                <li>Find the student's name and click <strong>View</strong>.</li>
                            </ol>
                            This opens the full test report for that student.`
                    },
                    {
                        q: "What if I want to check test data for multiple students at once?",
                        a: `Use the filter and search options within the <strong>Fitness Test Status</strong> module to access data for groups of students based on class, test status, or other criteria.`
                    },
                    {
                        q: "What general equipment is required for conducting the fitness tests?",
                        a: `<ul>
                            <li>Measuring tape or marked track</li>
                            <li>Cones or markers</li>
                            <li>Stopwatch or timer</li>
                            <li>Digital weighing scale</li>
                            <li>Stadiometer or measuring tape for height</li>
                            <li>Beam/brick for balance testing</li>
                            <li>Sit and Reach box</li>
                            <li>Mobile device with internet access for portal entry</li>
                        </ul>`
                    },
                    {
                        q: "What precautions should be taken before conducting fitness tests?",
                        a: `<ul>
                            <li>Ensure the testing area is clean, flat, and non-slippery.</li>
                            <li>Students should wear comfortable sports clothing.</li>
                            <li>Proper warm-up should be completed before testing.</li>
                            <li>Equipment should be checked before use.</li>
                            <li>Students should follow correct technique during all tests.</li>
                        </ul>`
                    },
                    ]
                },
                {
                    cat: "Test Procedures",
                    items: [{
                        q: "In the Plate Tapping Test, should tapping be done with the palm or fingers?",
                        a: `The tapping must be done using the <strong>palm only</strong>, at the centre of each disc.`
                    },
                    {
                        q: "What does the Plate Tapping Test measure?",
                        a: `The Plate Tapping Test measures <strong>speed and coordination of limb movement</strong>, specifically hand-eye coordination and upper-limb agility.`
                    },
                    {
                        q: "How is the Plate Tapping Test performed?",
                        a: `<ul>
                                <li>Adjust the table height so the participant can stand comfortably.</li>
                                <li>Place two discs (20 cm diameter) on the table with centres 60 cm apart.</li>
                                <li>Position a rectangle (30 × 20 cm) midway between the discs.</li>
                                <li>The participant places their <strong>non-preferred hand</strong> on the rectangle.</li>
                                <li>Using the preferred hand, they tap alternately between the two discs as quickly as possible, moving over the stationary hand.</li>
                                <li>The participant must complete <strong>25 full cycles (50 taps)</strong>.</li>
                            </ul>`
                    },
                    {
                        q: "How is the Plate Tapping Test scored?",
                        a: `The score is the <strong>total time taken to complete 25 full cycles (50 taps)</strong>.`
                    },
                    {
                        q: "How is the Flamingo Balance Test performed?",
                        a: `<ul>
                                <li>The participant stands on the beam. If needed, they may hold the instructor's hand for initial support.</li>
                                <li>While balancing on the preferred leg, the free leg is flexed at the knee with the foot held close to the buttocks.</li>
                                <li>Start the stopwatch when the instructor lets go.</li>
                                <li>Pause the timer each time the participant loses balance; resume when they regain it.</li>
                                <li>Count total falls or loss of balance within <strong>60 seconds</strong>.</li>
                                <li>If the participant falls more than <strong>15 times in the first 30 seconds</strong>, the test is terminated.</li>
                            </ul>`
                    },
                    {
                        q: "What equipment is required for the Flamingo Balance Test?",
                        a: `<ul>
                                <li>Non-slippery, even surface</li>
                                <li>Beam or raised surface (brick) to stand on</li>
                                <li>Mobile device with internet to record on the Active CISCE portal</li>
                            </ul>`
                    },
                    {
                        q: "Can I use the Stork Test instead of the Flamingo Balance Test?",
                        a: `No. As per the Active CISCE Test Battery, <strong>only the Flamingo Balance Test</strong> is permitted. Please use the portal and conduct the test exactly as shown in the instructional video.`
                    },
                    {
                        q: "Why is a raised beam or brick used for the Flamingo Balance Test?",
                        a: `The raised beam/brick is essential for accurately assessing static balance. It helps measure loss of balance more effectively, which cannot be evaluated properly on a flat surface.`
                    },
                    {
                        q: "How is the Flamingo Balance Test scored?",
                        a: `The score is the <strong>total number of falls or loss of balance during 60 seconds</strong>. If the participant falls more than 15 times in the first 30 seconds, the test is terminated.`
                    },
                    {
                        q: "What does the Body Composition Test measure?",
                        a: `The Body Composition Test measures the distribution of muscle and fat in the body, using <strong>Body Mass Index (BMI)</strong> calculated from weight and height.<br><br>
                            <strong>BMI = W ÷ (H × H)</strong><br>
                            Where W = body weight (kg) and H = height (m).<br>
                            A higher BMI generally indicates higher body fat levels.`
                    },
                    {
                        q: "How is height measured accurately for the Body Composition Test?",
                        a: `<ol>
                                <li>Remove shoes, bulky clothing, and hair ornaments.</li>
                                <li>Stand on a flat, uncarpeted surface against a wall.</li>
                                <li>Feet flat together, legs straight, arms at sides, shoulders level, looking straight ahead.</li>
                                <li>Head, shoulders, buttocks, and heels touching the wall where possible.</li>
                                <li>Lower a flat headpiece to the crown of the head and mark the wall.</li>
                                <li>Measure from floor to the mark to the nearest <strong>0.1 cm</strong>.</li>
                                <li>Enter the value in the portal under <strong>Body Composition → BMI → Height</strong>.</li>
                            </ol>`
                    },
                    {
                        q: "How is weight measured accurately for the Body Composition Test?",
                        a: `<ol>
                                <li>Use a <strong>digital scale</strong> (not a spring-loaded bathroom scale).</li>
                                <li>Place the scale on firm flooring (tile or wood, not carpet).</li>
                                <li>Participant removes shoes and heavy clothing.</li>
                                <li>Stand with both feet centred on the scale.</li>
                                <li>Record weight to the nearest decimal (e.g., 25.1 kg).</li>
                                <li>Enter the value in the portal under <strong>Body Composition → BMI → Weight</strong>.</li>
                            </ol>`
                    },
                    {
                        q: "What equipment is required for the Body Composition Test?",
                        a: `<ul>
                                <li>Flat, clean surface</li>
                                <li>Digital weighing machine</li>
                                <li>Stadiometer or measuring tape attached to the wall</li>
                                <li>Mobile device with internet to access the portal and record data</li>
                            </ul>`
                    },
                    
                    {
                        q: "What equipment is required for the 50m Dash?",
                        a: `<ul>
                                <li>Measuring tape or marked track (50m distance)</li>
                                <li>Cone markers for start and finish lines</li>
                                <li>Flat, clear surface of at least 60 metres</li>
                                <li>Mobile device with internet to record on the portal</li>
                            </ul>`
                    },
                    {
                        q: "How is the 50m Dash Test recorded?",
                        a: `<ol>
                                <li>Log in as a trainer → go to <strong>Take Test</strong>.</li>
                                <li>Select <strong>Speed</strong> from Physical Fitness Assessment Tests (Classes 4–12).</li>
                                <li>Click <strong>50m Dash</strong>.</li>
                                <li>Select number of lanes (max 8) — this determines how many students run simultaneously.</li>
                                <li>Click <strong>Start Timer</strong>.</li>
                                <li>Click <strong>Split Time</strong> as each student crosses the finish line.</li>
                                <li>Click <strong>Add</strong> → select class and student for each time.</li>
                                <li>Click <strong>Save</strong>.</li>
                            </ol>`
                    },
                    {
                        q: "How is the 600m Run/Walk performed?",
                        a: `<ol>
                                <li>Participants begin on the signal <strong>"Ready, Start"</strong>.</li>
                                <li>The goal is to cover 600 metres at the fastest possible pace.</li>
                                <li>Walking is permitted, but the objective is to finish in the shortest time.</li>
                                <li>The trainer announces elapsed time as participants cross the finish line.</li>
                            </ol>`
                    },
                    {
                        q: "We don't have a 600m ground for the cardiovascular endurance test. What can we do?",
                        a: `Students can run/walk <strong>150m × 4 rounds</strong> or <strong>200m × 3 rounds</strong> — both equal 600m.`
                    },
                    
                    {
                        q: "How is the 600m Run/Walk recorded?",
                        a: `<ol>
                                <li>Log in as trainer → go to <strong>Take Test</strong>.</li>
                                <li>Select <strong>Cardiovascular Endurance</strong> from Physical Fitness Assessment Tests (Classes 4–12).</li>
                                <li>Click <strong>600m Run/Walk</strong>.</li>
                                <li>Select number of lanes (max 8).</li>
                                <li>Click <strong>Start Timer</strong>.</li>
                                <li>Click <strong>Split Time</strong> as each student crosses the finish line.</li>
                                <li>Click <strong>Add</strong> → select class and student for each time.</li>
                                <li>Click <strong>Save</strong>. Score is recorded in minutes, seconds and milliseconds.</li>
                            </ol>`
                    },
                    {
                        q: "How is the Sit and Reach Test performed?",
                        a: `<ol>
                                <li>Participant sits on the floor with legs stretched straight. Shoes removed.</li>
                                <li>Soles of feet flat against the Sit and Reach box.</li>
                                <li>Knees must remain locked and pressed flat against the floor (trainer may assist).</li>
                                <li>Hands stacked on top of each other, palms facing down — reach forward as far as possible.</li>
                                <li>Both hands must remain at the same level.</li>
                                <li>Hold the position for 1–2 seconds while the distance is recorded. No jerky movements.</li>
                                <li>Repeat if hands are uneven or knees are flexed.</li>
                            </ol>`
                    },
                    {
                        q: "How is the Sit and Reach Test recorded?",
                        a: `<ol>
                                <li>Log in as trainer → go to <strong>Take Test</strong>.</li>
                                <li>Select <strong>Flexibility</strong> from Physical Fitness Assessment Tests (Classes 4–12).</li>
                                <li>Click <strong>Sit and Reach</strong> → select class and student.</li>
                                <li>Enter <strong>initial position</strong> (starting point) and <strong>final position</strong> (hands reached) in cm and mm.</li>
                                <li>The score is the difference between initial and final positions.</li>
                                <li>Click <strong>Save</strong>.</li>
                            </ol>`
                    },
                    {
                        q: "How is the Partial Curl Up test performed?",
                        a: `<ol>
                                <li>Participant lies on a flat, cushioned surface with knees flexed at approximately 90°.</li>
                                <li>Arms positioned straight at the sides, palms facing downward, parallel to the body.</li>
                                <li>Participant raises the trunk smoothly, curling up at least <strong>6 inches above the ground</strong> towards the parallel strip.</li>
                                <li>Lower the trunk back until shoulder blades or upper back touch the surface.</li>
                            </ol>`
                    },
                    {
                        q: "How is the Partial Curl Up test recorded?",
                        a: `<ol>
                                <li>Log in as trainer → go to <strong>Take Test</strong>.</li>
                                <li>Select <strong>Abdominal/Core Strength</strong> from Physical Fitness Assessment Tests (Classes 4–12).</li>
                                <li>Click <strong>Partial Curl Up</strong> → select class and student.</li>
                                <li>Start the timer — it runs for <strong>30 seconds</strong>.</li>
                                <li>Count the number of correctly completed curl-ups during this period.</li>
                                <li>Enter the count and click <strong>Save</strong>.</li>
                            </ol>`
                    },
                    {
                        q: "How are Push-Ups / Modified Push-Ups performed?",
                        a: `<p><strong>Boys:</strong> Start with hands and toes on the floor, body and legs in a straight line, feet slightly apart, arms shoulder-width apart. Lower until elbows form 90°, then push back up.</p>
                            <p><strong>Girls (Modified):</strong> Knees rest on the floor with similar alignment. Lower body to the predetermined point, then push back up.</p>
                            <p>Repeat in controlled rhythm until exhaustion or until the target count is reached.</p>`
                    },
                    {
                        q: "How is the Push-Up test recorded?",
                        a: `<ol>
                                <li>Log in as trainer → go to <strong>Take Test</strong>.</li>
                                <li>Select <strong>Muscular Endurance</strong> from Physical Fitness Assessment Tests (Classes 4–12).</li>
                                <li>Click <strong>Push-Ups</strong> → select class and student.</li>
                                <li>Start the timer.</li>
                                <li>Count and enter the number of correctly completed push-ups.</li>
                                <li>Click <strong>Save</strong>.</li>
                            </ol>`
                    },
                    ]
                }
            ],
            p: [
                {
                cat: "Login & Access",
                items: [
                    {
                        q: "How do I log in as a Parent?",
                        a: `<ol>
                                <li>Go to <a href="https://active.cisce.org/">active.cisce.org</a> from your mobile (Android or iOS).</li>
                                <li>Tap the <strong>Login</strong> button.</li>
                                <li>From the dropdown, select <strong>Parent</strong>.</li>
                                <li>Enter your <strong>User ID</strong> and <strong>Password</strong>.</li>
                                <li>Tick the <strong>"I am not a robot"</strong> checkbox.</li>
                                <li>Click <strong>Login</strong>.</li>
                            </ol>
                            <span class="warn-note">⚠ If you're having trouble logging in, make sure Cookies and JavaScript are enabled in your mobile browser.</span>`
                    },
                    {
                        q: "How do I get my login credentials?",
                        a: `Your child's <strong>User Name and Password</strong> are generated by the school through the <strong>Manage Students</strong> module. The school shares these with students via their Class Teacher or through official school communication. Please contact your child's school if you haven't received them.`
                    },
                    {
                        q: "I am unable to log in. What should I do?",
                        a: `<ul>
                                <li>Make sure you are selecting <strong>Parent</strong> from the Login As dropdown.</li>
                                <li>Check that your User ID and Password are entered correctly.</li>
                                <li>Ensure <strong>Cookies and JavaScript</strong> are enabled in your mobile browser.</li>
                                <li>If the issue persists, contact your child's school to verify or regenerate your credentials.</li>
                            </ul>`
                    }
                ]
            },
            {
                cat: "Dashboard",
                items: [
                    {
                        q: "What can I see on the Dashboard?",
                        a: `The <strong>Dashboard</strong> gives you an overview of your child's fitness and health data, including:
                            <ul>
                                <li>Overall Fitness Level</li>
                                <li>BMI (Body Mass Index)</li>
                                <li>Performance insights</li>
                                <li>FMS Development Tests summary</li>
                                <li>Fitness Tests summary</li>
                            </ul>
                            You can also access <strong>View Report</strong>, <strong>Download Report</strong>, and <strong>National Benchmark</strong> directly from the Dashboard.`
                    },
                    {
                        q: "How do I open the Dashboard?",
                        a: `After logging in, tap the <strong>Dashboard</strong> icon on the home screen. It will display your child's personal profile — name, class, section, roll number, DOB, gender — along with their fitness summary.`
                    },
                    {
                        q: "What is the National Benchmark option?",
                        a: `The <strong>National Benchmark</strong> button lets you compare your child's fitness performance against national standards for their age and gender group.`
                    },
                    {
                        q: "Can I download my child's report card?",
                        a: `Yes. From the <strong>Dashboard</strong>, tap <strong>Download Report</strong> to save your child's fitness report card to your device.`
                    }
                ]
            },
            {
                cat: "Progress Report",
                items: [
                    {
                        q: "How do I view my child's fitness report?",
                        a: `After logging in, tap the <strong>Progress Report</strong> module. The student's full Health and Fitness Assessment report card will open. Scroll down to review all sections.`
                    },
                    {
                        q: "What does the Progress Report show?",
                        a: `There are two report cards depending on your child's class:<br><br>
                            <strong>Junior Report Card (Class 1–3):</strong>
                            <ul>
                                <li>Developmental Skills — Locomotor, Object Control, Body Management</li>
                                <li>Physical Fitness results — Plate Tapping, Flamingo Balance, BMI</li>
                                <li>Fitness benchmarks and WHO physical activity guidelines</li>
                                <li>PE Teacher observations and recommendations</li>
                            </ul>
                            <strong>Senior Report Card (Class 4–12):</strong>
                            <ul>
                                <li>Physical Fitness results — 50m Dash, Partial Curl Up, Sit and Reach, Push Ups, 600m Run/Walk, BMI</li>
                                <li>Performance level, fitness benchmarks, and recommended sports</li>
                                <li>WHO physical activity guidelines and PE Teacher observations</li>
                            </ul>`
                    },
                    {
                        q: "What do the performance levels mean?",
                        a: `<ul>
                                <li><strong>L7 (Excellent) / L6 (High):</strong> Top 25% of performers — well above average.</li>
                                <li><strong>L5 (Good) / L4 (Moderate):</strong> Between 60–80 percentile — competent range.</li>
                                <li><strong>L3 (Developing) / L2 (Low) / L1 (Very Low):</strong> Used for identifying gaps or support needs.</li>
                            </ul>`
                    },
                    {
                        q: "What are the Developmental Stages shown in the Junior report (Class 1–3)?",
                        a: `Each skill in the Junior report shows one of four stages:
                            <ul>
                                <li><strong>Emerging:</strong> Taking first steps with willingness to try and improve.</li>
                                <li><strong>Developing:</strong> Making great progress with noticeable improvement and effort.</li>
                                <li><strong>Acquired:</strong> Confidently performing skills with consistency and joy.</li>
                                <li><strong>Accomplished:</strong> Masterful execution with precision and adaptability.</li>
                            </ul>`
                    },
                    {
                        q: "The report shows 'NA' in the Level field. What does that mean?",
                        a: `NA means the student's date of birth is incorrect or falls outside the valid age range:
                            <ul>
                                <li><strong>Class 1–3:</strong> Age 3–8 years</li>
                                <li><strong>Class 4–12:</strong> Age 9–18 years</li>
                            </ul>
                            Please contact your child's school to correct the date of birth.`
                    }
                ]
            },
            {
                cat: "Battery of Tests",
                items: [
                    {
                        q: "What is the Battery of Tests module?",
                        a: `The <strong>Battery of Tests</strong> module lets you watch <strong>test demonstration videos</strong> and read assessment instructions for each fitness test — so you can understand exactly how each test is conducted at school.`
                    },
                    {
                        q: "How do I watch the test demonstration videos?",
                        a: `<ol>
                                <li>After logging in, tap the <strong>Battery of Tests</strong> module.</li>
                                <li>A toggle will appear for two age groups — <strong>5–8 Years</strong> and <strong>9–18 Years</strong>.</li>
                                <li>Select the appropriate age group for your child.</li>
                                <li>Choose your preferred language — <strong>English</strong> or <strong>Hindi</strong>.</li>
                                <li>Tap on any test to watch the video (e.g., BMI, 50m Dash, Partial Curl Up, Sit and Reach, Push Ups).</li>
                            </ol>`
                    },
                    {
                        q: "Which tests have videos available?",
                        a: `Videos are available for both age groups.<br><br>
                            <strong>Classes 4–12 (9–18 years):</strong> BMI, 50m Dash, Partial Curl Up (30 sec), Sit and Reach, Push Ups, 600m Run/Walk, and more.<br><br>
                            <strong>Classes 1–3 (5–8 years):</strong> Developmental Skills and Fundamental Movement tests.`
                    },
                    {
                        q: "Are the videos available in Hindi?",
                        a: `Yes. All test demonstration videos are available in both <strong>English</strong> and <strong>Hindi</strong>. Select your preferred language after choosing the age group.`
                    }
                ]
            }
            ]
        };

        let curTab = 's',
            searching = false;

        function build(id, cats, hl) {
            const el = document.getElementById(id);
            
            let h = '';
            cats.forEach(c => {
                h += `<div class="cat-group"><div class="cat-label">${c.cat}</div><div class="cat-items">`;
                c.items.forEach((item, i) => {
                    const rowId = id + '_' + c.cat.replace(/\W/g, '') + '_' + i;
                    const qh = hl ? hi(item.q, hl) : item.q;
                    h += `<div class="faq-row" id="${rowId}">
        <button class="faq-q" onclick="tog('${rowId}')">
          <span>${qh}</span>
          <svg class="chevron" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
        </button>
        <div class="faq-a">${item.a}</div>
      </div>`;
                });
                h += `</div></div>`;
            });
            el.innerHTML = h;
        }

        function tog(id) {
            document.getElementById(id).classList.toggle('open');
        }

        function hi(t, q) {
            return t.replace(new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'), '<mark>$1</mark>');
        }

        function goTab(tab, btn) {
            document.getElementById('si').value = '';
            doSearch('');
            curTab = tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            ['ps', 'pt', 'pp', 'pq'].forEach(p => document.getElementById(p).classList.remove('on'));
            document.getElementById('p' + tab).classList.add('on');
        }

        function doSearch(val) {
            const q = val.trim().toLowerCase();
            const rc = document.getElementById('rc');
            if (!q) {
                searching = false;
                document.getElementById('pq').classList.remove('on');
                document.getElementById('p' + curTab).classList.add('on');
                rc.textContent = '';
                return;
            }
            searching = true;
            ['ps', 'pt', 'pp'].forEach(p => document.getElementById(p).classList.remove('on'));
            document.getElementById('pq').classList.add('on');

            let results = [];
            [
                ['s', D.s],
                ['t', D.t]
            ].forEach(([tab, cats]) => {
                cats.forEach(c => {
                    c.items.forEach(item => {
                        if (item.q.toLowerCase().includes(q) || item.a.replace(/<[^>]+>/g, '')
                            .toLowerCase().includes(q)) {
                            results.push({
                                tab,
                                cat: c.cat,
                                q: item.q,
                                a: item.a
                            });
                        }
                    });
                });
            });

            const pq = document.getElementById('pq');
            rc.textContent = results.length ? `${results.length} result${results.length !== 1 ? 's' : ''} found` : '';

            if (!results.length) {
                pq.innerHTML =
                    `<div class="no-results">No results for "<strong>${q}</strong>"<br><span style="font-size:11px;margin-top:4px;display:block;">Try different keywords or contact your cluster helpdesk below</span></div>`;
                return;
            }

            let h = '',
                lastTab = null;
            results.forEach((r, i) => {
                if (r.tab !== lastTab) {
                    if (lastTab !== null) h += '</div></div>';
                    const tl = r.tab === 's' ? '🏫 Schools & Principal' : '👩‍🏫 Assessor / Trainer';
                    h +=
                        `<div class="cat-group"><div class="cat-label">${tl} › ${r.cat}</div><div class="cat-items">`;
                    lastTab = r.tab;
                }
                const rid = 'sq' + i;
                h += `<div class="faq-row" id="${rid}">
      <button class="faq-q" onclick="tog('${rid}')">
        <span>${hi(r.q, q)}</span>
        <svg class="chevron" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
      </button>
      <div class="faq-a">${r.a}</div>
    </div>`;
            });
            if (lastTab !== null) h += '</div></div>';
            pq.innerHTML = h;
        }

        build('ps', D.s);
        build('pt', D.t);
        build('pp', D.p);
    </script>





@endsection
