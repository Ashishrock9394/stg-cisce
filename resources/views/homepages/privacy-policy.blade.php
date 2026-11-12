@extends('layouts.app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0b1a2e;
            border-bottom: 4px solid #1a4b8c;
            padding-bottom: 0.6rem;
            margin-bottom: 1.8rem;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0b1a2e;
            margin-top: 2.4rem;
            margin-bottom: 0.8rem;
            padding-top: 0.4rem;
        }

        h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #1a3a6a;
            margin-top: 1.6rem;
            margin-bottom: 0.4rem;
        }

        h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #1a4b8c;
            margin-top: 1.2rem;
            margin-bottom: 0.2rem;
        }

        p {
            margin-bottom: 1rem;
        }

        ul,
        ol {
            margin: 0.6rem 0 1.2rem 1.8rem;
        }

        li {
            margin-bottom: 0.3rem;
        }

        strong {
            color: #0b1a2e;
        }

        /* ── special blocks ── */
        .highlight-box {
            background: #f0f5fe;
            border-left: 5px solid #1a4b8c;
            padding: 1.2rem 1.8rem;
            border-radius: 0 12px 12px 0;
            margin: 1.2rem 0;
        }

        .section-number {
            font-weight: 700;
            color: #1a4b8c;
        }

        /* ── lists with custom markers ── */
        .legal-list {
            list-style-type: none;
            padding-left: 0;
        }

        .legal-list li {
            padding-left: 1.8rem;
            position: relative;
            margin-bottom: 0.4rem;
        }

        .legal-list li::before {
            content: "•";
            color: #1a4b8c;
            font-weight: 700;
            position: absolute;
            left: 0;
        }

        .legal-list.alpha li::before {
            content: "◦";
        }

        /* ── inline code / small labels ── */
        .email {
            color: #1a4b8c;
            font-weight: 500;
            word-break: break-all;
        }

        .highlight {
            background: #eef3fa;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-weight: 500;
        }

    </style>

<div class="container">
    <div class="t-mrg2 pb-5 mt-5">

        <div class="datatable-actions-outside d-flex justify-content-between align-items-center mb-3 container-fluid p-0">
            <div class="all-chaptr-cards mb-4">
                <div class="row">
                    <x:back-button title="{{$title}}" />
                </div>
            </div>            
        </div>

        <div>
        <!-- ===== INTRODUCTORY PARAGRAPHS ===== -->
        <p>This privacy policy applies to the entire contents of the platform "Active CISCE" operated by CISCE ["Active CISCE"] which may be referred to hereon in as "we" or "us" or "our" (as applicable)] including, without limitation, active.cisce.org or any application or software used to access the platform ("Website" or "any other website, pop-up window or tab of CISCE").</p>

        <p>This Privacy Policy constitutes the notice provided by CISCE to the User regarding the collection, storage, use, processing and disclosure of personal data through the Active CISCE platform. By registering on or using the Active CISCE platform and by providing personal data, the User acknowledges that he/she has read and understood this Privacy Policy and, where required under applicable law, expressly consents to the processing of such personal data for the purposes set out herein.</p>

        <p>The User may withdraw consent in respect of any processing based on consent by following the procedure prescribed by CISCE, provided that such withdrawal shall not affect the lawfulness of any processing undertaken prior to such withdrawal or any processing permitted or required under applicable law.</p>

        <p>Please read the Privacy Policy below carefully before using Active CISCE. By using or registering on Active CISCE, you accept and agree to the terms of this Privacy Policy. If you do not accept these terms, you are hereby advised not to access Active CISCE.</p>

        <p>The Users are responsible for ensuring their information is current and accurate. The User may update their information through the 'My Profile' section of the platform active.cisce.org, and we will use reasonable efforts to process any change the User makes; provided, however, that requests to delete information are subject to Active CISCE's internal reporting, processing and retention policies and all applicable legal obligations and existing legislations.</p>

        <p>We at Active CISCE are committed to preserving the privacy of all visitors to our platform Active CISCE. We are committed to ensuring that all activities made available via the platform Active CISCE will be provided in a safe and secure online environment. By accepting the Active CISCE's Privacy Policy and the Terms &amp; Conditions at the time of registration, you expressly consent to our use and disclosure of your personal data in accordance with this Privacy Policy. This Privacy Policy is incorporated into and subject to the Active CISCE's Terms &amp; Conditions. This Privacy Policy is effective upon submission/upload of data for new registering Users.</p>

        <p>This Privacy Policy works in conformance to the relevant rules, amendments and notifications as promulgated by the Ministry of Communication and Information Technology, Government of India read with the prevailing sections under the Information Technology Act, 2000 (21 of 2000) read with relevant rules issued under the Information Technology Act, 2000; the Digital Data Protection Act, 2023 and the Rules made thereunder; and any other applicable law.</p>

        <p>Please read the following carefully to understand our views and practices regarding the User Information. The Privacy Policy can be found on our platform at active.cisce.org.</p>

        <!-- ===== SECTION A ===== -->
        <h2>A. Active CISCE PLATFORM</h2>

        <p>The Active CISCE Platform is an initiative by the CISCE, wherein CISCE intends to achieve the twin objectives of broad-basing of sports and promotion of Physical Health &amp; Fitness. The Active CISCE platform is designed to encourage children to make Fitness &amp; Sports an integral part of their lives.</p>

        <h2>B. Your Privacy: Our Commitment</h2>
        <p>At CISCE, we are committed to protect your privacy and recognise that you have a right to know about the information we access or collect about you, how your information is being used by us and what we do to protect your information. Please read the following policy to understand how your personal data shall be treated by Active CISCE platform. Our commitment towards privacy and protection of User data is based on the principles of lawfulness, transparency, fairness, accuracy, data minimisation, integrity and confidentiality</p>

        <h2>C.  What does this Privacy Policy Cover?</h2>
        <p>This Privacy Policy is part of the Active CISCE Portal and covers the treatment of user information, including personally identifying information, obtained by Active CISCE including information obtained when you access the Active CISCE platform or any other software provided by Active CISCE and any information that you may share through use of the Active CISCE platform.</p>       

        <!-- ===== SECTION 2 (from page 2) ===== -->
        <h2>1.  INTRODUCTION</h2>

        <p><span class="section-number">1.1</span> The Council for the Indian School Certificate Examinations (CISCE) shall act as the Data Fiduciary in respect of all personal data processed through the Active CISCE platform and shall process such personal data in accordance with applicable laws, including the Digital Personal Data Protection Act, 2023 and the rules framed thereunder, as amended from time to time.</p>

        <p><span class="section-number">1.2</span> Any grievance relating to the processing of personal data or the exercise of rights under this Privacy Policy may be addressed through Active CISCE's escalation matrix published on the Active CISCE platform. CISCE shall endeavour to acknowledge and resolve such grievances within the time prescribed under applicable law.</p>

        <p><span class="section-number">1.3</span> Active CISCE ("Active CISCE, we, us") recognizes that people who register on and subsequently use Active CISCE's platform ("you" or "Users" or "Registered Users" or "Members" or "Guardians and/or Parents and/or "Assessors" or "PE Teachers" or "Sports Coaches" and/or School Principal/Teacher of Members") value their privacy. This Privacy Policy details important information regarding the collection, use and disclosure of User information collected on Active CISCE platform, and any other features, tools, materials, or other services (including affiliated services) offered from time to time by Active CISCE or its affiliates. Active CISCE provides this Privacy Policy to help you make an informed decision about whether to use or continue using the Service.</p>

        <p><span class="section-number">1.4</span> The terms of this Privacy Policy apply and extends to Members and the Parents and/or Legal Guardians of Members and/or School Principal/Teacher of Members, whereas the rights ascribed to Users under this Privacy Policy extend and apply in equal measure to Members and the Parents/Legal Guardians of Members and/or School Principal/Teacher of Members.</p>

        <p><span class="section-number">1.5</span> The User's use of Active CISCE and any information the User may provide through the platform remains subject to the terms of this Privacy Policy and our Terms &amp; Conditions, as each may be updated from time to time.</p>

        <p><span class="section-number">1.6</span> The User hereby accepts and agrees that the Active CISCE platform shall be available and accessible only to Users associated with CISCE-affiliated schools located in India or outside India.</p>

        <p><span class="section-number">1.7</span> Any questions, comments or complaints that you might have should be emailed to <span class="email">activecisce@cisce.org</span> or forwarded in writing to our address located: Council for the Indian School Certificate Examinations, E-4, Sector - 1, Noida, Uttar Pradesh - 201301.</p>


        <h2>2.  COLLECTION OF INFORMATION</h2>
        <!-- ===== continued from page 3 ===== -->
        <p><span class="section-number">2.1</span> This Active CISCE platform may collect personal data (personal, physical health and fitness data, and other similar information) related to the school students from Class 1 to 12 ("Members") who have been registered on the Active CISCE platform by Registered Users (CISCE and/or Principals and/or Assessors and/or Parents and/or Members themselves). The information collected by the said platform shall depend on the portion of the said platform being used by the User and may vary from time to time which will be informed through changes in this policy.</p>

        <p><span class="section-number">2.2</span> CISCE shall endeavour to collect only such personal data as is reasonably necessary for the purposes specified in this Privacy Policy. Optional fields shall be clearly identified and the User shall not be compelled to furnish optional personal information unless required by law or for a specific service.</p>

        <p><span class="section-number">2.3</span> With respect to data of minors, CISCE shall collect and process such data only through CISCE affiliated Schools registered on Active CISCE. The authorised user of each school submitting information relating to a child represents and warrants that he/she is duly authorised to provide such consent to collect and process such data.</p>

        <p><span class="section-number">2.4</span> The terms of this Privacy Policy shall apply to the User as well as the Members including their lawful guardians and/or parents and/or school principals/teachers. The rights, obligations, duties and liabilities ascribed to the User shall also extend to the Members and their lawful guardians and/or parents and/or school principals/teachers.</p>

        <p><span class="section-number">2.5</span> The Active CISCE platform is available to all Users, irrespective of whether they are registered and/or unregistered Users. The access and usage of the components of "User Manual", "Battery of Tests", "FAQ", "Contact Us" shall be an open access for all the Users, whereas the "Take Test", "Fitness Reports", "Manage Students", "Manage Assessors", "Dashboard", "My/Edit Profile" etc. component shall only be available to the authorised Users, upon login.</p>

        <p><span class="section-number">2.6</span> The personal information provided by the Users include personal information of children, athletes and/or talents, which can only be provided by the Users who are the lawful guardians and/or parents and/or school principals/teachers of the concerned child, athlete and/or talent. Such Users, while adding the information of such children, athletes and/or talents shall need to specify their relationship with the child/athlete/talent and submit a declaration affirming and warranting such a relationship and the veracity of such personal information. No other third parties shall be allowed to register Members on the Active CISCE platform and provide information on the platform. CISCE shall not be liable for any breach and/or non-compliance of this provision by any User and reserves all rights to initiate any legal proceedings for such a breach and/or non-compliance against the respective Users.</p>

        <p><span class="section-number">2.7</span> The information may be collected from the Schools in a number of ways which may include collecting personal information [including but not limited to School Code, Student Name, Registration No, Class, Section, Roll No., Email Address, Date of Birth, Gender (Male/Female/Boy/Girl), Password (created by the System), etc.). When the Schools create an account with us, they agree with the Terms and Conditions and accept the Privacy Policy of Active CISCE Platform while entering the same.</p>

        <p><span class="section-number">2.8</span> In the future, when Active CISCE commences Fitness Assessment of Children with Special Needs (CWSN) and/or include Sports aptitude, we may also ask the User to provide additional relevant information.</p>

        <p><span class="section-number">2.9</span> In addition to the above, we may also collect the physical health and fitness tests of the Members including:</p>
        <ul>
            <li>Body Composition (BMI)</li>
            <li>Fundamental Movement Skills</li>
            <li>Co-ordination</li>
            <li>Balance</li>
            <li>Flexibility</li>
            <li>Cardiovascular Endurance</li>
            <li>Core Strength</li>
            <li>Muscular Endurance</li>
        </ul>
        <p>Additional Assessment Tests for Fitness or Sports Aptitude, which may be decided by the high level committee on Physical Health and Fitness from time to time, will be incorporated in due course of time.</p>

        <p><span class="section-number">2.10</span> The complete profile information on the Active CISCE platform shall be used for physical fitness analytics. The complete profile information and fitness assessment data shall also be used for further analytics by CISCE.</p>

        <!-- ===== SECTION 3 ===== -->
        <h2>3. USE OF INFORMATION</h2>

        <p><span class="section-number">3.1</span> Personal data collected through the Active CISCE platform shall be processed only for lawful purposes connected with the operation of the platform, including physical fitness assessments, educational and administrative functions, analytics, government programmes and such other purposes as are specifically communicated to the User. The information may also be used to provide to you the features and functionality of the platform, including but not limited to the following:</p>
        <ul>
            <li>providing you with regular updates;</li>
            <li>processing and responding to your enquiries or feedback;</li>
            <li>personalizing your use of the platform;</li>
            <li>alerting you to new features, special events which we think you may be of value to you;</li>
            <li>enforcing the legal terms and conditions that govern your use of the platform;</li>
            <li>investigating and protecting the integrity of Active CISCE's platform; and</li>
            <li>contacting you about your account or profile.</li>
        </ul>

        <p><span class="section-number">3.2</span> We may use your information to communicate with you about:</p>
        <ul>
            <li>Physical Health Fitness assessment reports for which you have signed up; and</li>
            <li>non-marketing or administrative purposes (such as notifying you of major changes to the platform).</li>
        </ul>

        <p><span class="section-number">3.3</span> We use your information to improve the quality and design of our platform and to create new features, and functionality such as by storing, tracking, and analyzing user preferences and trends.</p>

        <p><span class="section-number">3.4</span> We may use your information to improve the quality and performance of our talent scouting systems for elite talent across India in order to bolster and improve CISCE and India's sporting talent reservoir and deliver targeted athlete beneficial schemes and/or programmes with greater efficiency.</p>

        <p><span class="section-number">3.5</span> We may use cookies, log file information, and such as device identifiers for purposes such as (a) remembering information so that you will not have to re-enter it during your visit or the next time you visit our platform; (b) providing custom, personalized content and information; (c) monitoring the effectiveness of our programme; and (d) monitoring aggregate metrics such as total number of visitors, pages viewed, etc.</p>

        <!-- ===== SECTION 4 ===== -->
        <h2>4. DISCLOSURE OF INFORMATION</h2>

        <p><span class="section-number">4.1</span> We may disclose the information we collect or receive from you to authorized government agencies, including but not limited to the Sports Authority of India (SAI), Keho India, the Ministry of Youth Affairs &amp; Sports, the Ministry of Education, or any other competent governmental authority, where such disclosure is necessary for the implementation of government programmes, sports talent identification, analytics, policy formulation, regulatory compliance, or any other lawful purpose connected with the objectives of the Active CISCE platform.</p>

        <p><span class="section-number">4.2</span> We may disclose the information we collect or receive from you to third-party service providers engaged by CISCE to process information on our behalf for the purpose of providing, maintaining, supporting, or operating the Active CISCE platform. Such service providers may have access to your information for purposes including, but not limited to, hosting services, email SMTP services, customer and technical support, maintenance, analytics, and other related functions necessary for the operation of the platform. Under no circumstances shall such service providers use the data for any purpose other than providing services to Active CISCE, and they shall be bound by appropriate confidentiality and data protection obligations.</p>

        <p><span class="section-number">4.3</span> Where personal data is shared with authorised government agencies or service providers, such disclosure shall be limited to the extent necessary for the stated purpose and shall be subject to appropriate contractual, technical and organisational safeguards to protect the confidentiality and security of such data.</p>

        <p><span class="section-number">4.4</span> We also may share personal data in other situations only with your prior consent or where legally obligated under law.</p>

        <p><span class="section-number">4.5</span> Active CISCE also reserves the right to disclose personal data that Active CISCE believes, in good faith, is appropriate or necessary to enforce our Terms and Conditions, take precautions against liability or harm, to investigate, prevent and/or respond to third-party claims or allegations, suspected illegal activities, fraud, to respond to court orders or official requests, to comply with applicable law, including but not limited to sharing with appropriate tax authorities, to protect the security or integrity of our platform, to report violations of Active CISCE's eligibility rules to third parties or safety of Active CISCE, our users or others.</p>

        <p><span class="section-number">4.6</span> If you elect to participate in any, surveys, questionnaires, or other events during your usage of Active CISCE, the rules or terms and conditions for those events may indicate that your information may be shared with third parties as described in those rules.</p>

        <p><span class="section-number">4.7</span> In the event control of the Active CISCE platform is managed by CISCE or a new agency on behalf of CISCE, your information may be transferred to the new software and/or organization and/or government body as such a transfer may be necessary for the continued operation of the Active CISCE platform. Any changes to the privacy policy by CISCE in compliance with government of India guidelines and/or government body shall be notified on the Active CISCE platform.</p>

        <h3>4.8 CISCE Platform &amp; the User:</h3>
        <p>The Active CISCE platform may include links to websites or web pages. Such websites or pages are governed by their respective terms &amp; conditions and privacy policies, which are beyond our control. Once you exit the Active CISCE platform and enter the external website through the Active CISCE platform, use of any information you provide shall be governed by the privacy policy of the operator of the website you are visiting. Active CISCE shall not be responsible for any use of these website by such User.</p>

        <!-- ===== SECTION 5 ===== -->
        <h2>5. FEEDBACK/CUSTOMER SUPPORT</h2>

        <p><span class="section-number">5.1</span> All telephone and/or email conversations from the Users to our Feedback/Customer Support Centre may be recorded both to assist us in training and in quality management, and to allow for the speedy resolution of the queries.</p>

        <p><span class="section-number">5.2</span> All communications with Active CISCE and its Feedback/Customer Support shall be conducted in respectful and non-offensive language (language which is not offensive and/or abusive). Active CISCE prevents and prohibits any such use of offensive and/or abusive language and strictest of actions shall be taken against the User who uses such offensive and/or abusive language including but not limited to suspension and/or termination of the User Account.</p>

        <!-- ===== SECTION 6 ===== -->
        <h2>6. USER &amp; MEMBER (PARENT/LEGAL GUARDIAN) RIGHTS UNDER THIS PRIVACY POLICY</h2>

        <p>Notwithstanding anything stated under this Privacy Policy, the rights provided to the User under this Clause 6 shall extend in equal measure to the Members and their legal guardians and/or parents as well.</p>

        <h3>6.1 Right to modify information:</h3>
        <ul>
            <li>The User shall have the right to modify any personal data (like Name, email ID) or withdraw his or her consent at any time with respect to the processing and disclosure of the optional personal data/information provided to Active CISCE.</li>
            <li>Such withdrawal of consent shall not affect the lawfulness of any processing/disclosure based on User consent carried on by Active CISCE before its withdrawal.</li>
        </ul>

        <h3>6.2 Right to Transparency and Communication:</h3>
        <ul>
            <li>Active CISCE shall take appropriate measures to provide any information referred to in this Privacy Policy and any communication under this Privacy Policy relating to processing to the User in a concise, transparent, intelligible and easily accessible form, using clear and plain language. Such information shall be provided in writing, or by other means, including, where appropriate, by electronic means. When requested by the User, the information may be provided orally, provided that the identity of the User is proven by other means.</li>
            <li>Active CISCE shall facilitate the exercise of User's rights under this Clause 6 and offer full co-operation and support towards the same.</li>
            <li>Active CISCE shall provide information on action taken on a request under this Clause 6 to the User without undue delay and in any event within one month of receipt of the request. That period may be extended by two further months where necessary, taking into account the complexity and number of the requests. Active CISCE shall inform the User of any such extension within one month of receipt of the request, together with the reasons for the delay. Where the User makes the request by electronic form means, the information shall be provided by electronic means where possible, unless otherwise requested by the User.</li>
            <li>Information provided under this Clause 6 and any communication and any actions taken under this Clause and Privacy Policy shall be provided free of charge. Where requests from a User are manifestly unfounded or excessive, in particular because of their repetitive character, Active CISCE may either:
                <ul>
                    <li>charge a reasonable fee taking into account the administrative costs of providing the information or communication or taking the action requested; or</li>
                    <li>refuse to act on the request.</li>
                </ul>
            </li>
            <li>Without prejudice to this Clause, where the Active CISCE has reasonable doubts concerning the identity of the natural person making the request referred to herein, Active CISCE may request the provision of additional information necessary to confirm the identity of the User.</li>
        </ul>

        <h3>6.3 Right to rectification:</h3>
        <p>The User shall have the right to obtain from Active CISCE the rectification of inaccurate personal data concerning him or her. Taking into account the purposes of the processing, the User shall have the right to have incomplete personal data completed, including by means of providing a supplementary statement.</p>

        <p><span class="section-number">6.4</span> Nothing contained herein shall be construed as limiting or expanding any statutory rights available under applicable law.</p>

        <!-- ===== SECTION 7 ===== -->
        <h2>7. COOKIES</h2>

        <p><span class="section-number">7.1</span> Cookies Information: When you visit the Active CISCE platform, we may send one or more cookies - small files - to your computer or other device, which may enable us or others to uniquely identify you on your browser. Active CISCE may use both session cookies and persistent cookies. A persistent cookie remains after you close your browser. Persistent cookies may be used by your browser on subsequent visits to the site. Persistent cookies can be removed by following your web browser help file directions. Session cookies are temporary and typically disappear after you close your browser. You may be able to reset your web browser to refuse all cookies or to indicate when a cookie is being sent. You can choose to accept or decline cookies. Most web browsers automatically accept cookies, but you can usually modify your browser setting to decline cookies if you prefer. However, some features of the platform may not function properly if the ability to accept cookies is disabled.</p>

        <p><span class="section-number">7.2</span> A cookie is a small file which asks permission to be placed on your computer's hard drive.</p>

        <p><span class="section-number">7.3</span> Cookies help us provide you with a better Active CISCE platform, by enabling us to monitor which sections of the Active CISCE platform you find useful and not useful. A cookie in no way gives us access to your computer or other device or any information about you, other than the data you choose to share with us.</p>

        <p><span class="section-number">7.4</span> We may use traffic log cookies to identify which pages are being used. This helps us analyze data about web page traffic and improve our Active CISCE platform in order to tailor it to customer needs. We only use this information for statistical analysis purposes and then the data is removed from the system.</p>

        <p><span class="section-number">7.5</span> Log File Information: When you use the platform, the servers may record certain information that your device sends whenever you visit any website and use certain apps. These server logs may include information such as your web or app request, Internet Protocol ("IP") address, browser type, browser language, referring/exit pages and URLs, platform type, number of clicks, domain names, landing pages, pages viewed and the order of those pages, the amount of time spent on particular pages, the date and time of your request, the actions you take on a page and one or more cookies that may uniquely identify your browser.</p>

        <p><span class="section-number">7.6</span> By continuing to use the platform, Users consent to the use of cookies in accordance with this Privacy Policy, subject to browser settings and applicable law.</p>

        <!-- ===== SECTION 8 ===== -->
        <h2>8. DATA STORAGE</h2>

        <p><span class="section-number">8.1</span> All the information collected in the database by the platform shall be stored on the CISCE's Azure IT Infrastructure. Personal data shall be retained only for so long as may be necessary to fulfil the purposes for which it was collected or for compliance with applicable legal, regulatory, accounting or administrative requirements. Upon expiry of the applicable retention period or upon the purpose of processing being completed, and unless retention is required by law, CISCE shall delete, anonymise, archive or de-identify the personal data in accordance with its internal data retention and archival policies.</p>

        <!-- ===== SECTION 9 ===== -->
        <h2>9. INFORMATION SECURITY</h2>

        <p><span class="section-number">9.1</span> We are committed to protecting the security of your personal data. Protection of the User Information, including the registration details the User provides to us, is a priority for us. We use a variety of security technologies and procedures to help protect your personal data from unauthorized access, use, or disclosure.</p>

        <p><span class="section-number">9.2</span> Any personal data which identifies the User will be encrypted whilst in transit over the internet. The technology used by us encrypts any information sent by the User on secured pages. Our server then reads the information using a private key. This means that your information is turned into a code that can only be decoded with the private key or password.</p>

        <p><span class="section-number">9.3</span> Whilst neither we, nor any other organization, can guarantee the security of information processed online, we do have appropriate security measures in place to protect your personal data. For example, we store the personal data you provide on computer systems with limited access and in controlled facilities.</p>

        <p><span class="section-number">9.4</span> In the event of any personal data breach affecting personal data processed through the Active CISCE platform, CISCE shall take appropriate remedial measures to contain and mitigate the breach and shall notify the Data Protection Board of India and each affected Data Principal of such personal data breach in accordance with the Digital Personal Data Protection Act, 2023 and the rules framed thereunder. Affected users may also contact <span class="email">activecisec@cisec.org</span> for information relating to a reported breach.</p>

        <p><span class="section-number">9.5</span> To further protect your privacy and security, we reserve the right to take reasonable steps (such as requesting your password) to verify your identity before granting you profile access or making corrections.</p>

        <p><span class="section-number">9.6</span> Additionally, you should ensure your password is kept secret at all times. You will be responsible for maintaining the secrecy of your password and account information.</p>

        <!-- ===== SECTION 10 ===== -->
        <h2>10. PARENTS ACCESS</h2>

        <p><span class="section-number">10.1</span> Parents/Guardians may access student reports and information through credentials or access mechanisms provided by the respective school, but shall not independently create user accounts on the platform.</p>

        <!-- ===== SECTION 11 ===== -->
        <h2>11. PRIVACY MEASURES FOR MINORS</h2>

        <p><span class="section-number">11.1</span> Registration as a 'User' on the Active CISCE platform is intended only for persons who are 18 years of age or above and Users below the age of 18 are strictly prohibited from accessing/using the platform. If the User is under 18 years of age, he/she shall not be permitted to use the platform.</p>

        <p><span class="section-number">11.2</span> Only the legal guardian/parent of the young athletes/children/wards and/or the School Principal/teacher of the School of the young athletes/children/wards shall be treated as registered Members on the Active CISCE platform and provided with their login credential details. No other unauthorised third parties shall be allowed to register Members on the Active CISCE platform and enter their details and information. Parents shall not be allowed to register and/or create an account.</p>

        <p><span class="section-number">11.3</span> Sharing of login credentials of Parents / Legal Guardians for students (age 5-18 years) will provided by CISCE and/or the respective school to them in a secure manner.</p>

        <p><span class="section-number">11.4</span> If the User or the parent/guardian of the Members and/or the School Principal/Teacher and/or the Assessors, believe that they may have inadvertently uploaded inaccurate personal data, they can notify us and make a request for modification/correction/deletion of the same immediately by sending an email to <span class="email">activecisec@cisec.org</span>.</p>

        <!-- ===== SECTION 12 ===== -->
        <h2>12. CHANGES AND UPDATES TO INFORMATION</h2>

        <p><span class="section-number">12.1</span> This Privacy Policy may be revised from time to time and this will be reflected by the "Last update posted" date above. Please revisit this page to stay aware of any changes.</p>

        <p><span class="section-number">12.2</span> Contact Information: Please contact the platform with any questions or comments about this Privacy Policy by emailing us at <span class="email">activecisec@cisec.org</span>.</p>

        <p><span class="section-number">12.3</span> The User is responsible for keeping his/her information current. The User may update his/her information by contacting us at <span class="email">activecsice@cisce.org</span> and we will use reasonable efforts to process any change the User makes; provided, however, that requests to delete information are subject to our internal reporting, processing and retention policies and all applicable legal obligations.</p>

        <!-- ===== SECTION 13 ===== -->
        <h2>13. LANGUAGE</h2>

        <p><span class="section-number">13.1</span> All communications between the User and the platform shall be communicated in written form vide Electronic Mail (E-Mail) and in either English or Hindi language. Further, all communications between the User and the Customer Support shall also be conducted in either English or Hindi language.</p>

        <!-- ===== SECTION 14 ===== -->
        <h2>14. FEEDBACK</h2>

        <p><span class="section-number">14.1</span> Any comments or suggestions that you may have and which may contribute to improvement of the Active CISCE platform will be appreciated. Please feel free to write to us at <span class="email">activecsice@cisce.org</span> in case you want to share your feedback with us.</p>

        <p><span class="section-number">14.2</span> This Privacy Policy which replaces any other Privacy Policy for the Active CISCE platform published on the Active CISCE platform to date.</p>

        <p><span class="section-number">14.3</span> CISCE reserves the right to amend, modify or update this Privacy Policy from time to time to reflect changes in applicable laws, regulatory requirements, operational practices or the features and functionality of the Active CISCE platform. Any revised Privacy Policy shall be published on the Active CISCE platform and shall become effective from the date of such publication. Where any amendment materially affects the rights of the Users or the manner in which their personal data is collected, used, processed or disclosed, CISCE shall take reasonable steps to notify the Users through the Active CISCE platform or by such other means as it may consider appropriate, in accordance with applicable law. Continued use of the Active CISCE platform after such publication or notification shall constitute acknowledgment of the revised Privacy Policy, to the extent permitted by law.</p>

        <p><span class="section-number">14.4</span> Governing Law: This Privacy Policy shall be interpreted and administered in accordance with the laws of India, including the Digital Personal Data Protection Act, 2023, the rules framed thereunder, the Information Technology Act, 2000, and any other applicable laws, amendments or governmental directions issued from time to time.</p>

        <!-- ===== FOOTER / END ===== -->
        {{-- <div style="margin-top: 2.8rem; padding-top: 1.2rem; border-top: 1px solid #e2e8f0; font-size: 0.9rem; color: #4a5a72; text-align: center;">
            <p style="margin-bottom: 0;">&copy; 2026 CISCE &bull; Active CISCE Platform</p>
            <p style="margin-bottom: 0; font-size: 0.85rem; color: #6b7a93;">Last updated: 1 July 2026</p>
        </div> --}}

    </div>
    </div>            
</div>
@endsection