<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Student Report</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        .s1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9.5pt;
        }

        .s2 {
            color: #333;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9pt;
        }

        h1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 13pt;
        }

        h2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 9.5pt;
        }

        .s3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 9pt;
        }

        .s4, .s4 p {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9pt;
        }

        .s5 {
            color: black;
            font-family: "MS UI Gothic", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9pt;
        }

        .s6 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            vertical-align: 2pt;
        }

        .s7 {
            color: #333;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9.5pt;
        }

        .s8 {
            color: #333;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8.5pt;
        }

        p {
            color: #000000;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 6.5pt;
            margin: 0pt;
        }

        .a,
        a {
            color: #FFF;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 6.5pt;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }
    </style>
</head>

<body>
    <div style="margin: auto; width: 800px;">
        <div style="width: 100%; margin-top: 30px;">
            <!--Basic Details Start-->
            <table class="s1"
                style="text-indent: 0pt;text-align: left; border: 2px solid #000; padding: 5px; margin-bottom: 30px; width: 800px;"
                cellspacing="0" cellpadding="0">
                <tr>
                    <th style="padding: 4px;">Name</th>
                    <td style="padding: 4px;">{{ $getReport[0]->student_name }}</td>
                    <th style="padding: 4px;">Class</th>
                    <td style="padding: 4px;">{{ $getReport[0]->classname.'-'.$getReport[0]->section }}</td>
                    <!--<th style="padding: 4px;">BB</th>
                    <td style="padding: 4px;">BB</td>-->
                </tr>
                <tr>
                    <th style="padding: 4px;">Date of Birth</th>
                    <td style="padding: 4px;">{{ $getReport[0]->dob }}</td>
                    <th style="padding: 4px;">Gender</th>
                    <td style="padding: 4px;">{{ $getReport[0]->gender }}</td>
                   <!-- <th style="padding: 4px;">BB</th>
                    <td style="padding: 4px;">BB</td> -->
                </tr>
                <tr>
                    <th style="padding: 4px;">Weight (kgs)</th>
                    <td style="padding: 4px;">-</td>
                    <th style="padding: 4px;">Height (cms)</th>
                    <td style="padding: 4px;">-</td>
                    <!--<th style="padding: 4px;">BMI</th>
                    <td style="padding: 4px;">20.24</td> -->
                </tr>


            </table>
            <!--Basic Details End-->
            <h1 style="text-indent: 0pt;text-align: center; margin-bottom: 30px;">P.E. Class activities and Observations
                by Teacher </h1>
            <!-- <h2 style="padding-top: 3pt;text-indent: 0pt;text-align: left; margin-bottom: 10px;">Skill Area Activity Learning Outcome Rating Level </h2> -->
            <!--Report table Start-->
            <table style="border-collapse:collapse; width: 100%;" cellspacing="0" cellpadding="0">
                <tr class="s3">
                    <th style="text-align: left; width:76pt; padding-bottom: 4px;">Skill Area</th>
                    <th style="text-align: left; width:80pt; padding-bottom: 4px;">Activity</th>
                    <th style="text-align: left; width:128pt; padding-bottom: 4px;">Learning Outcome</th>
                    <th style="text-align: center; padding-bottom: 4px;" colspan="5">Rating</th>
                    <th style="text-align: left; padding-bottom: 4px; padding-left: 4px; width:38pt;">Level</th>

                </tr>

<!-- Start Manipulative Skills -->

@foreach ($getSports as $spkey => $spval )

                <tr style="height:18pt">

                    <td style="border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5;border-right-style:solid;border-right-width:1pt;border-right-color:#E5E5E5; vertical-align: middle; padding-top:2px; padding-bottom:2px;"
                        rowspan="{{ $spval->total }}">
                        <p style="text-indent: 0pt;text-align: left;"><br /></p>
                        <p class="s3" style="padding-left: 2pt;text-indent: 0pt;line-height: 117%;text-align: left;">
                           {{ $spval->sportsskillname }}</p>
                    </td>


                    @foreach($getSkills as $skkey => $skval )

                        @foreach($skval as $sskey => $sskval)
                       
                    <?php if($sskval->skill_sports_id != $spval->skill_sports_id)
                           continue;
                        ?>

                    <td
                        style="border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-left-style:solid;border-left-width:1pt;border-left-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5; padding-left:2px; padding-top:2px; padding-bottom:6px;">
                        <p class="s4" style="padding-top: 3pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                        {{ $sskval->title }}</p>
                    </td>
                    <td
                    class="s4" style="border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5; padding-top:2px; padding-bottom:6px;">
                        <p class="s4" style="padding-top: 3pt;text-indent: 0pt;text-align: left;">
                        {!! $sskval->learning_outcomes !!}
                        </p>
                    </td>

                    @for($i=0; $i<$sskval->rating-1; $i++ )
                        <td
                            style="width:12pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5; padding-top:6px; padding-bottom:6px;">
                            <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                        </td>
                    @endfor

                    
                    @for($i=0; $i<6-$sskval->rating; $i++ )
                    <td
                        style="width:12pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5; padding-top:6px; padding-bottom:6px;">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: left;">&#9734;</p>
                    </td>
                    @endfor
                    
                    <td
                        style="width:12pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5; padding-top:2px; padding-bottom:6px; padding-left: 4px;">
                        <p class="s4" style="padding-top: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: left;">
                         {{ $sskval->level_name }}
                        </p>
                    </td>


                </tr>

                        @endforeach

                   @endforeach  


@endforeach             
            
               <!-- <tr style="height:18pt">
                    <td
                        style="width:130pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-left-style:solid;border-left-width:1pt;border-left-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                            Water Bottle
                            Fun</p>
                    </td>
                    <td
                        style="width:108pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;text-indent: 0pt;text-align: left;">
                            Toss,Catch
                            and clap</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:28pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: left;">&#9734;</p>
                    </td>
                    <td
                        style="width:59pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: left;">
                            Exemplary
                        </p>
                    </td>
                </tr> -->

<!-- End Manipulative Skills -->

<!-- Start Stability Skills -->


              <!--  <tr style="height:18pt">
                    <td style="width:76pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-right-style:solid;border-right-width:1pt;border-right-color:#E5E5E5; vertical-align: middle;"
                        rowspan="2">
                        <p style="text-indent: 0pt;text-align: left;"><br /></p>
                        <p class="s3" style="padding-left: 2pt;text-indent: 0pt;text-align: left;">Stability Skills</p>
                    </td>
                    <td
                        style="width:130pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-left-style:solid;border-left-width:1pt;border-left-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                            Bottle
                            Activity</p>
                    </td>
                    <td
                        style="width:108pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;text-indent: 0pt;text-align: left;">
                            Bending and
                            Stretching</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:28pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: left;">&#9734;</p>
                    </td>
                    <td
                        style="width:59pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: left;">
                            Exemplary
                        </p>
                    </td>
                </tr>
                <tr style="height:18pt">
                    <td
                        style="width:130pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-left-style:solid;border-left-width:1pt;border-left-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                            Chair
                            Aerobics Routines</p>
                    </td>
                    <td
                        style="width:108pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;text-indent: 0pt;text-align: left;">
                            Turning and
                            Twisting</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:18pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">&#9733;</p>
                    </td>
                    <td
                        style="width:28pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: left;">&#9734;</p>
                    </td>
                    <td
                        style="width:59pt;border-top-style:solid;border-top-width:1pt;border-top-color:#E5E5E5;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#E5E5E5">
                        <p class="s4" style="padding-top: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: left;">
                            Exemplary
                        </p>
                    </td>
                </tr> -->
               
<!-- End Stability Skills -->              

            </table>
            <!--Report table End-->
        </div>
        <!--Sign Start-->
        <div style="width: 100%;">
            <div style="display: flex; margin: auto; width: 800px; text-align: center; margin-top: 50px;">
                <div style="flex-grow: 1; flex-basis: 0;">
                    <p class="s2" style="text-indent: 0pt;text-align: center;padding: 4px;">Rashmi Sharma</p>
                    <p class="s7" style="text-indent: 0pt;text-align: center;padding: 4px;">Director</p>
                    <p class="s8" style="text-indent: 0pt;text-align: center;padding: 4px;">Sequoia Fitness &amp;Sports
                        Technology
                    </p>
                </div>
                <div style="flex-grow: 1; flex-basis: 0;">
                    <p class="s2" style="text-indent: 0pt;text-align: center;padding: 4px;">Manisha Bhagee</p>
                    <p class="s7" style="text-indent: 0pt;text-align: center;padding: 4px;">Principal</p>
                    <p class="s8" style="text-indent: 0pt;text-align: center;padding: 4px;">The Class of One</p>


                </div>
            </div>
        </div>
        <!--Sign end-->
    </div>
</body>

</html>