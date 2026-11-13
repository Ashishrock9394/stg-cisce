<div id="independenceDayPopup" class="fitness-idp-overlay">

    <div class="independence-page">

        <button type="button"
                class="fitness-idp-close btn-close"
                onclick="closeIndependencePopup()"
                aria-label="Close"></button>

        <!-- TOP TRICOLOR -->
        <div class="tricolor-line"></div>

        <!-- BACKGROUND -->
        <div class="bg-circle circle-one"></div>
        <div class="bg-circle circle-two"></div>
        <div class="bg-circle circle-three"></div>
        <div class="bg-circle circle-four"></div>

        <!-- FLOATING ICONS -->
        <div class="floating-icon icon-1">
            <i class="fa-solid fa-person-running"></i>
        </div>

        <div class="floating-icon icon-2">
            <i class="fa-solid fa-heart-pulse"></i>
        </div>

        <div class="floating-icon icon-3">
            <i class="fa-solid fa-dumbbell"></i>
        </div>

        <div class="floating-icon icon-4">
            <i class="fa-solid fa-person-walking"></i>
        </div>

        <!-- ASHOKA CHAKRA -->
        <div class="chakra-wrapper">
            <div class="chakra">

                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span>

            </div>
        </div>


        <!-- =========================================
            TOP CISCE
        ========================================== -->

        <div class="top-section">

            <!-- Replace with actual CISCE logo -->
            <img
                src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}"
                alt="CISCE Logo"
                class="cisce-logo"
            >

        </div>


        <!-- =========================================
            MAIN CONTENT
        ========================================== -->

        <main class="main-content">

            <div class="celebration-label">
                <span class="india-flag">
                    <span class="flag-chakra"></span>
                </span>
                <span>15 August 2026</span>
                <span class="india-flag">
                    <span class="flag-chakra"></span>
                </span>
            </div>


            <h1 class="main-title">
                Happy Independence Day
            </h1>
            <div class="subtitle">Celebrating <span>80</span> Years of Freedom with the spirit of a healthier, fitter India.
            </div>
            <p class="description">
                A healthier generation today, a stronger and brighter India tomorrow.
                <strong>Physical Health & Fitness Assessment.</strong>
            </p>

            <div class="fitness-dashboard-btn-wrapper">
                <button onclick="closeIndependencePopup()"
                aria-label="Close" class="fitness-dashboard-btn">
                    <span>Go to Dashboard</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <!-- FITNESS ASSESSMENT -->
            <div class="assessment-cards">

                <!-- Speed -->
                <div class="assessment-card">

                    <div class="assessment-icon">
                        <i class="fa-solid fa-bolt"></i>
                    </div>

                    <h3>Speed</h3>

                    <p>
                        Run • Move • Perform
                    </p>

                </div>


                <!-- Strength -->
                <div class="assessment-card">

                    <div class="assessment-icon">
                        <i class="fa-solid fa-dumbbell"></i>
                    </div>

                    <h3>Strength</h3>

                    <p>
                        Build • Grow • Excel
                    </p>

                </div>


                <!-- Flexibility -->
                <div class="assessment-card">

                    <div class="assessment-icon">
                        <i class="fa-solid fa-person-walking"></i>
                    </div>

                    <h3>Flexibility</h3>

                    <p>
                        Stretch • Balance • Move
                    </p>

                </div>


                <!-- Endurance -->
                <div class="assessment-card">

                    <div class="assessment-icon">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>

                    <h3>Endurance</h3>

                    <p>
                        Energy • Stamina • Health
                    </p>

                </div>

            </div>
            <div class="fitness-message">

                <div class="fitness-message-item">
                    <i class="fa-solid fa-person-running"></i>
                    <strong>Move with Purpose</strong>
                </div>

                <div class="fitness-message-item">
                    <i class="fa-solid fa-dumbbell"></i>
                    <strong>Live with Strength</strong>
                </div>

                <div class="fitness-message-item">
                    <i class="fa-solid fa-heart-pulse"></i>
                    <strong>Inspire a Fitter India</strong>
                </div>

            </div>

        </main>


        <!-- =========================================
            BOTTOM FITNESS365
        ========================================== -->

        <div class="bottom-section">

            <!-- Replace with actual Fitness365 logo -->
             <div class="bottom-caption">
                Powered by
            </div>
            <img
                src="{{ asset('logo/fitness365-logo-web.jpg') }}"
                alt="Fitness365"
                class="fitness365-logo"
            >

        </div>


        <!-- SIDE DECORATION -->

        <div class="side-line left"></div>
        <div class="side-line right"></div>

    </div>

    </div>
    <style>

        /* ============================================================
        INDEPENDENCE DAY POPUP
        ACTIVE CISCE
        ============================================================ */


        /* ============================================================
        GO TO DASHBOARD BUTTON
        ============================================================ */

        .fitness-dashboard-btn-wrapper {
            margin-top: 10px;

            display: flex;
            justify-content: center;
            align-items: center;

            position: relative;
            z-index: 20;
        }

        .fitness-dashboard-btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 10px;

            padding: 9px 22px;

            border-radius: 50px;

            text-decoration: none !important;

            font-size: 11px;
            font-weight: 700;

            color: #fff !important;

            background:
                linear-gradient(
                    90deg,
                    #ff9933 0%,
                    #e87500 45%,
                    #138808 100%
                );

            box-shadow:
                0 8px 22px rgba(0, 0, 0, .12);

            transition:
                transform .25s ease,
                box-shadow .25s ease;
        }

        .fitness-dashboard-btn i {
            font-size: 12px;

            transition:
                transform .25s ease;
        }

        .fitness-dashboard-btn:hover {
            color: #fff !important;

            transform: translateY(-3px);

            box-shadow:
                0 12px 28px rgba(0, 0, 0, .18);
        }

        .fitness-dashboard-btn:hover i {
            transform: translateX(4px);
        }


        /* ============================================================
        FULL SCREEN OVERLAY
        ============================================================ */

        .fitness-idp-overlay {
            position: fixed !important;

            inset: 0 !important;

            width: 100vw !important;
            height: 100vh !important;

            min-width: 100vw !important;
            min-height: 100vh !important;

            z-index: 99999999 !important;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden !important;

            background: #ffffff;

            animation: idpOverlayShow .6s ease forwards;
        }


        /* Main page */

        .fitness-idp-overlay .independence-page {
            width: 100%;
            height: 100%;

            min-height: 100vh;
        }


        /* ============================================================
        CLOSE BUTTON
        ============================================================ */

        .fitness-idp-close {
            position: fixed !important;

            top: 18px !important;
            right: 22px !important;

            z-index: 999999999 !important;

            width: 42px;
            height: 42px;

            border: 1px solid rgba(0,0,0,.08);

            border-radius: 50%;

            background: rgba(255,255,255,.92);

            color: #555;

            display: flex;

            align-items: center;
            justify-content: center;

            cursor: pointer;

            box-shadow:
                0 6px 22px rgba(0,0,0,.12);

            transition: all .25s ease;
        }

        .fitness-idp-close:hover {
            transform: rotate(90deg);

            background: #fff;

            box-shadow:
                0 8px 25px rgba(0,0,0,.18);
        }


        @keyframes idpOverlayShow {

            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }

        }


        /* ============================================================
        RESET
        ============================================================ */

        .fitness-idp-overlay *,
        .fitness-idp-overlay *::before,
        .fitness-idp-overlay *::after {
            box-sizing: border-box;
        }


        /* ============================================================
        MAIN PAGE
        ============================================================ */

        .fitness-idp-overlay {
            font-family: "Poppins", sans-serif;
        }

        .independence-page {

            position: relative;

            width: 100%;
            height: 100vh;

            min-height: 0;

            overflow: hidden;

            background:
                linear-gradient(
                    180deg,
                    #fff4e9 0%,
                    #ffffff 42%,
                    #ffffff 58%,
                    #edf9f2 100%
                );

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: flex-start;

            padding: 12px 30px 8px;
        }


        /* ============================================================
        TRICOLOR TOP LINE
        ============================================================ */

        .tricolor-line {

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;

            height: 7px;

            background:
                linear-gradient(
                    to right,
                    #ff9933 0%,
                    #ff9933 33.33%,
                    #ffffff 33.33%,
                    #ffffff 66.66%,
                    #138808 66.66%,
                    #138808 100%
                );

            z-index: 20;
        }


        /* ============================================================
        BACKGROUND CIRCLES
        ============================================================ */

        .bg-circle {

            position: absolute;

            border-radius: 50%;

            pointer-events: none;
        }


        /* Orange */

        .circle-one {

            width: 430px;
            height: 430px;

            top: -250px;
            left: -180px;

            background:
                rgba(255, 153, 51, 0.10);
        }


        /* Green */

        .circle-two {

            width: 500px;
            height: 500px;

            bottom: -330px;
            right: -200px;

            background:
                rgba(19, 136, 8, 0.09);
        }


        /* Orange ring */

        .circle-three {

            width: 220px;
            height: 220px;

            top: 35%;
            right: -110px;

            border:
                35px solid rgba(255, 153, 51, 0.06);
        }


        /* Green ring */

        .circle-four {

            width: 160px;
            height: 160px;

            top: 45%;
            left: -80px;

            border:
                25px solid rgba(19, 136, 8, 0.05);
        }


        /* ============================================================
        FLOATING FITNESS ICONS
        ============================================================ */

        .floating-icon {

            position: absolute;

            display: flex;

            align-items: center;
            justify-content: center;

            width: 48px;
            height: 48px;

            border-radius: 50%;

            background:
                rgba(255,255,255,0.75);

            backdrop-filter: blur(8px);

            box-shadow:
                0 8px 25px rgba(0,0,0,0.08);

            font-size: 19px;

            animation:
                floatIcon 4s ease-in-out infinite;

            z-index: 2;
        }

        .floating-icon i {

            background:
                linear-gradient(
                    135deg,
                    #ff9933,
                    #138808
                );

            -webkit-background-clip: text;

            -webkit-text-fill-color: transparent;
        }


        .icon-1 {

            top: 23%;
            left: 7%;

            animation-delay: 0s;
        }

        .icon-2 {

            top: 34%;
            right: 8%;

            animation-delay: 1s;
        }

        .icon-3 {

            bottom: 26%;
            left: 11%;

            animation-delay: 2s;
        }

        .icon-4 {

            bottom: 28%;
            right: 11%;

            animation-delay: 3s;
        }


        @keyframes floatIcon {

            0%,
            100% {

                transform:
                    translateY(0);
            }

            50% {

                transform:
                    translateY(-12px);
            }

        }


        /* ============================================================
        ASHOKA CHAKRA
        24 SPOKES
        ============================================================ */

        .chakra-wrapper {

            position: absolute;

            width: 190px;
            height: 190px;

            top: 50%;
            left: 50%;

            transform:
                translate(-50%, -50%);

            opacity: 0.045;

            pointer-events: none;

            z-index: 0;
        }


        .chakra {

            width: 100%;
            height: 100%;

            border:
                7px solid #000080;

            border-radius: 50%;

            position: relative;

            animation:
                rotateChakra 30s linear infinite;
        }


        /* Center */

        .chakra::after {

            content: "";

            position: absolute;

            width: 18px;
            height: 18px;

            background:
                #000080;

            border-radius: 50%;

            left: 50%;
            top: 50%;

            transform:
                translate(-50%, -50%);

            z-index: 5;
        }


        /* Spokes */

        .chakra span {

            position: absolute;

            width: 3px;
            height: 88px;

            background:
                #000080;

            left: 50%;
            top: 50%;

            transform-origin:
                center bottom;

            transform:
                translate(-50%, -100%);

            border-radius: 2px;
        }


        /* 24 × 15 degrees */

        .chakra span:nth-child(1) {
            transform: translate(-50%, -100%) rotate(0deg);
        }

        .chakra span:nth-child(2) {
            transform: translate(-50%, -100%) rotate(15deg);
        }

        .chakra span:nth-child(3) {
            transform: translate(-50%, -100%) rotate(30deg);
        }

        .chakra span:nth-child(4) {
            transform: translate(-50%, -100%) rotate(45deg);
        }

        .chakra span:nth-child(5) {
            transform: translate(-50%, -100%) rotate(60deg);
        }

        .chakra span:nth-child(6) {
            transform: translate(-50%, -100%) rotate(75deg);
        }

        .chakra span:nth-child(7) {
            transform: translate(-50%, -100%) rotate(90deg);
        }

        .chakra span:nth-child(8) {
            transform: translate(-50%, -100%) rotate(105deg);
        }

        .chakra span:nth-child(9) {
            transform: translate(-50%, -100%) rotate(120deg);
        }

        .chakra span:nth-child(10) {
            transform: translate(-50%, -100%) rotate(135deg);
        }

        .chakra span:nth-child(11) {
            transform: translate(-50%, -100%) rotate(150deg);
        }

        .chakra span:nth-child(12) {
            transform: translate(-50%, -100%) rotate(165deg);
        }

        .chakra span:nth-child(13) {
            transform: translate(-50%, -100%) rotate(180deg);
        }

        .chakra span:nth-child(14) {
            transform: translate(-50%, -100%) rotate(195deg);
        }

        .chakra span:nth-child(15) {
            transform: translate(-50%, -100%) rotate(210deg);
        }

        .chakra span:nth-child(16) {
            transform: translate(-50%, -100%) rotate(225deg);
        }

        .chakra span:nth-child(17) {
            transform: translate(-50%, -100%) rotate(240deg);
        }

        .chakra span:nth-child(18) {
            transform: translate(-50%, -100%) rotate(255deg);
        }

        .chakra span:nth-child(19) {
            transform: translate(-50%, -100%) rotate(270deg);
        }

        .chakra span:nth-child(20) {
            transform: translate(-50%, -100%) rotate(285deg);
        }

        .chakra span:nth-child(21) {
            transform: translate(-50%, -100%) rotate(300deg);
        }

        .chakra span:nth-child(22) {
            transform: translate(-50%, -100%) rotate(315deg);
        }

        .chakra span:nth-child(23) {
            transform: translate(-50%, -100%) rotate(330deg);
        }

        .chakra span:nth-child(24) {
            transform: translate(-50%, -100%) rotate(345deg);
        }


        @keyframes rotateChakra {

            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }

        }


        /* ============================================================
        TOP CISCE LOGO
        ============================================================ */

        .top-section {

            position: relative;

            z-index: 10;

            width: 100%;

            text-align: center;

            padding-top: 2px;
        }


        .cisce-logo {

            width: 105px;
            height: 105px;

            object-fit: contain;
        }


        /* ============================================================
        MAIN CONTENT
        ============================================================ */

        .main-content {

            position: relative;

            z-index: 10;

            width:
                min(1050px, 100%);

            display: flex;

            flex-direction: column;

            align-items: center;

            text-align: center;

            margin-top: 0;
        }


        /* ============================================================
        DATE LABEL
        ============================================================ */
        .celebration-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            padding: 6px 15px;

            border-radius: 50px;

            background: rgba(255,255,255,0.78);

            border: 1px solid rgba(255,153,51,0.25);

            color: #555;

            font-size: 11px;
            font-weight: 700;

            letter-spacing: 1.5px;

            box-shadow: 0 7px 25px rgba(0,0,0,0.04);
        }

        .india-flag {
            position: relative;

            width: 25px;
            height: 16px;

            display: inline-block;

            overflow: hidden;

            border-radius: 2px;

            background:
                linear-gradient(
                    to bottom,
                    #ff9933 0%,
                    #ff9933 33.33%,
                    #ffffff 33.33%,
                    #ffffff 66.66%,
                    #138808 66.66%,
                    #138808 100%
                );

            box-shadow:
                0 2px 5px rgba(0,0,0,.12);
        }

        .flag-chakra {
            position: absolute;

            left: 50%;
            top: 50%;

            width: 5px;
            height: 5px;

            border: 1px solid #000080;

            border-radius: 50%;

            transform:
                translate(-50%, -50%);
        }

        /* ============================================================
        MAIN TITLE
        ============================================================ */

        .main-title {

            margin-top: 8px;

            padding:
                0 5px;

            font-size:
                clamp(34px, 4.6vw, 62px);

            line-height:
                1.05;

            font-weight:
                800;

            letter-spacing:
                -2px;

            background:
                linear-gradient(
                    90deg,
                    #ff9933 0%,
                    #e97819 30%,
                    #333 48%,
                    #333 52%,
                    #138808 72%,
                    #0b7505 100%
                );

            -webkit-background-clip:
                text;

            -webkit-text-fill-color:
                transparent;
        }


        /* ============================================================
        SUBTITLE
        ============================================================ */

        .subtitle {

            margin-top:
                7px;

            font-size:
                clamp(15px, 2vw, 21px);

            line-height:
                1.35;

            font-weight:
                600;

            color:
                #333;
        }


        .subtitle span {

            color:
                #138808;

            font-size:
                20px;
        }


        /* ============================================================
        DESCRIPTION
        ============================================================ */

        .description {

            max-width:
                760px;

            margin:
                5px auto 0;

            font-size:
                13px;

            line-height:
                1.55;

            color:
                #777;
        }


        /* ============================================================
        ASSESSMENT CARDS
        ============================================================ */

        .assessment-cards {

            display:
                grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap:
                13px;

            width:
                100%;

            max-width:
                800px;

            margin-top:
                18px;
        }


        .assessment-card {

            position:
                relative;

            padding:
                16px 12px;

            background:
                rgba(255,255,255,0.82);

            border-radius:
                17px;

            border:
                1px solid rgba(0,0,0,0.06);

            box-shadow:
                0 12px 35px rgba(0,0,0,0.07);

            transition:
                transform .3s ease,
                box-shadow .3s ease;
        }


        .assessment-card:hover {

            transform:
                translateY(-6px);

            box-shadow:
                0 18px 40px rgba(0,0,0,0.12);
        }


        /* Assessment icon */

        .assessment-icon {

            width:
                45px;

            height:
                45px;

            margin:
                0 auto 9px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                13px;

            font-size:
                19px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,153,51,0.14),
                    rgba(19,136,8,0.12)
                );

            color:
                #333;
        }


        .assessment-card h3 {

            font-size:
                12px;

            font-weight:
                700;

            color:
                #333;
        }


        .assessment-card p {

            margin-top:
                3px;

            font-size:
                9px;

            color:
                #888;
        }


        /* ============================================================
        FITNESS MESSAGE
        ============================================================ */

        .fitness-message {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                10px;

            margin-top:
                12px;

            padding:
                9px 18px;

            border-radius:
                50px;

            background:
                rgba(255,255,255,.75);

            border:
                1px solid rgba(7,85,165,.12);

            box-shadow:
                0 7px 24px rgba(0,0,0,.05);

            font-size:
                10px;

            position:
                relative;

            z-index:
                20;
        }


        .fitness-message-item {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                6px;

            white-space:
                nowrap;
        }


        .fitness-message-item i {

            font-size:
                14px;
        }


        .fitness-message-item strong {

            font-weight:
                700;
        }


        /* Move */

        .fitness-message-item:nth-child(1) i {

            color:
                #ff9933;
        }

        .fitness-message-item:nth-child(1) strong {

            color:
                #e87500;
        }


        /* Strength */

        .fitness-message-item:nth-child(2) i {

            color:
                #0755a5;
        }

        .fitness-message-item:nth-child(2) strong {

            color:
                #0755a5;
        }


        /* Fitter India */

        .fitness-message-item:nth-child(3) i {

            color:
                #138808;
        }

        .fitness-message-item:nth-child(3) strong {

            color:
                #138808;
        }


        /* ============================================================
        BOTTOM FITNESS365
        ============================================================ */

        .bottom-section {

            position:
                absolute;

            left:
                0;

            right:
                0;

            bottom:
                8px;

            width:
                100%;

            margin:
                0 !important;

            z-index:
                50;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;
        }


        .fitness365-logo {

            width:
                105px;

            height:
                36px;

            object-fit:
                contain;

            background:
                #fff;

            border-radius:
                8px;

            padding:
                3px;

            box-shadow:
                0 5px 20px rgba(0,0,0,0.08);
        }


        .bottom-caption {

            margin-top:
                3px;

            font-size:
                11px;

            color:
                #777;

            font-weight:
                500;
        }


        /* ============================================================
        SIDE DECORATIVE LINES
        ============================================================ */

        .side-line {

            position:
                absolute;

            width:
                4px;

            height:
                90px;

            border-radius:
                10px;

            opacity:
                .25;
        }


        .side-line.left {

            left:
                0;

            top:
                38%;

            background:
                linear-gradient(
                    to bottom,
                    #ff9933,
                    transparent
                );
        }


        .side-line.right {

            right:
                0;

            top:
                52%;

            background:
                linear-gradient(
                    to bottom,
                    #138808,
                    transparent
                );
        }


        /* ============================================================
        DESKTOP - FINAL VIEWPORT FIT
        ============================================================ */

        @media (min-width: 801px) {

            .independence-page {

                height:
                    100vh;

                min-height:
                    0;

                padding:
                    12px 30px 8px;

                justify-content:
                    flex-start;
            }


            .top-section {

                padding-top:
                    2px;
            }


            .cisce-logo {

                width:
                    105px;

                height:
                    105px;
            }


            .main-content {

                margin-top:
                    0;
            }


            .celebration-label {

                padding:
                    6px 15px;
            }


            .main-title {

                margin-top:
                    8px;

                font-size:
                    clamp(34px, 4.6vw, 62px);
            }


            .subtitle {

                margin-top:
                    7px;
            }


            .description {

                margin-top:
                    5px;
            }


            .fitness-dashboard-btn-wrapper {

                margin-top:
                    10px;
            }


            .fitness-dashboard-btn {

                padding:
                    9px 22px;
            }


            .assessment-cards {

                margin-top:
                    18px;
            }


            .fitness-message {

                margin-top:
                    12px;
            }


            .bottom-section {

                position:
                    absolute;

                left:
                    0;

                right:
                    0;

                bottom:
                    8px;

                margin:
                    0 !important;

                z-index:
                    50;
            }


            .fitness365-logo {

                width:
                    105px;

                height:
                    36px;
            }


            .bottom-caption {

                margin-top:
                    3px;

                font-size:
                    11px;
            }

        }


        /* ============================================================
        TABLET
        ============================================================ */

        @media (max-width: 800px) {

            .fitness-idp-overlay {

                overflow-y:
                    auto !important;

                align-items:
                    flex-start;

                justify-content:
                    flex-start;
            }


            .independence-page {

                width:
                    100%;

                height:
                    100vh;

                min-height:
                    100vh;

                overflow:
                    hidden;

                padding:
                    18px 18px 70px;

                justify-content:
                    flex-start;
            }


            .assessment-cards {

                grid-template-columns:
                    repeat(2, 1fr);

                max-width:
                    500px;

                margin-top:
                    18px;
            }


            .main-content {

                margin-top:
                    10px;
            }


            .floating-icon {

                display:
                    none;
            }


            .chakra-wrapper {

                width:
                    260px;

                height:
                    260px;
            }


            /* =========================================
               MOBILE / TABLET FITNESS365
               ALWAYS STAY AT VIEWPORT BOTTOM
            ========================================= */

            .bottom-section {

                position:
                    fixed !important;

                left:
                    0 !important;

                right:
                    0 !important;

                bottom:
                    8px !important;

                width:
                    100% !important;

                margin:
                    0 !important;

                padding:
                    0 !important;

                display:
                    flex !important;

                flex-direction:
                    column !important;

                align-items:
                    center !important;

                justify-content:
                    center !important;

                z-index:
                    999999 !important;
            }


            .fitness365-logo {

                width:
                    110px;

                height:
                    40px;

                object-fit:
                    contain;
            }


            .bottom-caption {

                margin-top:
                    2px;

                font-size:
                    9px;
            }

        }


        /* ============================================================
        MOBILE
        ============================================================ */

        @media (max-width: 600px) {

            .fitness-idp-close {

                width:
                    34px;

                height:
                    34px;

                top:
                    9px !important;

                right:
                    9px !important;
            }


            .fitness-dashboard-btn {

                padding:
                    9px 20px;

                font-size:
                    10px;
            }


            .fitness-message {

                gap:
                    7px;

                padding:
                    8px 12px;

                font-size:
                    8px;

                max-width:
                    100%;
            }


            .fitness-message-item {

                gap:
                    3px;
            }


            .fitness-message-item i {

                font-size:
                    11px;
            }


            .fitness-message-item strong {

                font-size:
                    8px;
            }
        }


        /* ============================================================
        SMALL MOBILE
        ============================================================ */

        @media (max-width: 450px) {

            .independence-page {

                padding:
                    14px 12px 65px;
            }


            .cisce-logo {

                width:
                    72px;

                height:
                    72px;
            }


            .main-title {

                font-size:
                    34px;

                letter-spacing:
                    -1px;
            }


            .subtitle {

                font-size:
                    14px;

                line-height:
                    1.4;
            }


            .subtitle span {

                font-size:
                    16px;
            }


            .description {

                font-size:
                    11px;

                line-height:
                    1.5;
            }


            .assessment-cards {

                gap:
                    9px;

                margin-top:
                    16px;
            }


            .assessment-card {

                padding:
                    13px 8px;

                border-radius:
                    14px;
            }


            .assessment-icon {

                width:
                    39px;

                height:
                    39px;

                margin-bottom:
                    7px;

                font-size:
                    16px;
            }


            .assessment-card h3 {

                font-size:
                    11px;
            }


            .assessment-card p {

                font-size:
                    8px;
            }


            .fitness-message {

                flex-wrap:
                    wrap;

                border-radius:
                    18px;

                padding:
                    8px 10px;

                margin-top:
                    12px;
            }


            .fitness-message-item strong {

                font-size:
                    8px;
            }


            .fitness-message-item i {

                font-size:
                    10px;
            }


            .bottom-section {

                position:
                    fixed !important;

                left:
                    0 !important;

                right:
                    0 !important;

                bottom:
                    6px !important;

                width:
                    100% !important;

                margin:
                    0 !important;

                padding:
                    0 !important;

                z-index:
                    999999 !important;
            }


            .fitness365-logo {

                width:
                    105px;

                height:
                    38px;

                object-fit:
                    contain;
            }


            .bottom-caption {

                margin-top:
                    2px;

                font-size:
                    9px;
            }


            .chakra-wrapper {

                width:
                    220px;

                height:
                    220px;
            }

        }


        /* ============================================================
        VERY SMALL DEVICES
        ============================================================ */

        @media (max-width: 360px) {

            .main-title {

                font-size:
                    29px;
            }


            .subtitle {

                font-size:
                    12px;
            }


            .subtitle span {

                font-size:
                    14px;
            }


            .description {

                font-size:
                    10px;
            }


            .assessment-card {

                padding:
                    10px 5px;
            }


            .assessment-icon {

                width:
                    35px;

                height:
                    35px;

                font-size:
                    14px;
            }


            .assessment-card h3 {

                font-size:
                    10px;
            }


            .assessment-card p {

                font-size:
                    7px;
            }


            .fitness-message-item strong {

                font-size:
                    7px;
            }

        }

        </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const popup =
                document.getElementById("independenceDayPopup");

            if (!popup) {
                return;
            }

            document.documentElement.style.overflow = "hidden";
            document.body.style.overflow = "hidden";
        });


        function closeIndependencePopup() {

            const popup =
                document.getElementById("independenceDayPopup");

            if (!popup) {
                return;
            }

            popup.style.transition =
                "opacity .35s ease";

            popup.style.opacity = "0";


            setTimeout(function () {

                popup.remove();

                document.documentElement.style.overflow = "";
                document.body.style.overflow = "";

            }, 350);
        }
    </script>