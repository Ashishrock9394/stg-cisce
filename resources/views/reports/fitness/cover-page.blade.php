 <!-- Cover Page -->
<table cellpadding="0" cellspacing="0" style="width: 21cm; border-collapse: collapse; margin-left: auto; margin-right: auto; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0px; background-color: #fff;">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0;">
               
                <tr>
                    <td style="border-collapse: collapse;">
                        <div style="position: relative; background-color: #fff;">
                            <div style="position: relative; background-color: #fecd0a; margin-top:0px; z-index:0; height:136px;">
                                <img src="{{ asset('assets/reports/green-dot.jpg')}}" alt="" style="width: 40px; height:40px; position: relative; left:150px; top:0;">
                            </div>
                         
                            <div style="width: auto; position:absolute; top:-30px; left: 176px; z-index:10; padding:30px 15px 0 15px; display:flex;">
                            <div style="background:#fff; padding:25px 15px 10px 15px; height:140px; float:left;">
                                <img src="{{ asset('assets/reports/cisce-logo.png')}}" alt="" style="width: auto; height:100px;">
                            </div>
                            <p style="margin-left:30px; float:left; font-size:24px; padding:34px 20px 20px 0px; width:300px; font-weight:600; line-height:24px; color:#000;">Council for the Indian School Certificate Examinations</p>
                            </div>
                           
                            <img src="{{ asset('assets/reports/report-graphic.png')}}" alt="" style="position: absolute; top:26%; right:40px; width: 200px; border-collapse: collapse; z-index:9;">
                           
                            <img src="{{ asset('assets/reports/report-cover-j-img.jpg')}}" alt="" style="width: 76%; z-index:2; position: relative; top:0;">
                        </div>
                    
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0;">
                            <tr>
                                <td style="background-color:#fecd0a;">
                                    <img src="{{ asset('assets/reports/aa-bg.png')}}" alt="" style="width:188px;">
                                </td>
                                <td style="vertical-align: top; width: 100%;">
                                    <table cellpadding="0" cellspacing="0" style="width: 90%; border: 0;">
                                        <tr>
                                            <td style="height: 30px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 1px 30px 5px 46px; font-size: 20px; background:#1c9b3e; color:#fff; font-size: 24px; font-weight: 500; position:relative;">Physical Health and Fitness Assessment<span style=" position:absolute; top:44px; right:-21px;"><img src="{{ asset('assets/reports/yellow-bg.jpg')}}" alt="" style="width:20px;"></span></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 30px 30px 10px 50px; font-size: 24px; font-weight: 500;">Personal Profile</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 10px 30px 10px 50px; font-size: 16px; color: #333;">
                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                    <tr>
                                                        <td colspan="2" style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">Name</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; font-size: 18px;">{{ $studentsData->student_name }}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px;">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">Class&nbsp;&&nbsp;Section</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->display_classname }}-{{ $studentsData->section }}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px; margin-left: 3px 10px;">Roll&nbsp;No.</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->rollno ?? ''}}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">School</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->school_name }}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">Code</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->school_code }}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px; margin-left: 3px 10px;">APAAR&nbsp;ID</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->apaarId ?? ''}}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">CISCE&nbsp;Registration&nbsp;No</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->admissionnumber }}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    @php
                                                                        use Carbon\Carbon;

                                                                        $dob = Carbon::parse($studentsData->dob); 
                                                                        $formattedDob = $dob->format('d M Y'); // 05 Jul 2019
                                                                        $age = $dob->age; // will calculate age automatically
                                                                       

                                                                        if (strtolower($studentsData->gender) === 'male') {
                                                                            $gender = 'Boy';
                                                                        } else {
                                                                            $gender = 'Girl';
                                                                        }
                                                                    @endphp
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">DOB</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;"> {{ $formattedDob }} ({{ $age }} Years)</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px; margin-left: 3px 10px;">Gender</span></td>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $gender }}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="height: 15px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="padding: 6px 0;">
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                <tr>
                                                                    <td><span style="display: inline-block; margin-right: 3px 10px;">Brief Summary</span></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                        </tr>
                                    </table>
                                </td>

                            </tr>
                            <tr>
                                <td style="background-color: #1c9b3e; height: 30px;"></td>
                                <td style="background-color: #F28F0C; height: 30px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>