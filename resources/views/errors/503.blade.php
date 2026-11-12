<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Under Maintenance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #333;
        }
        .error-div {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 0px;
        }
        .error-content {
            max-width: 1000px;
            display: flex;
            /*flex-wrap: wrap;*/
            background: #fff;
            border-radius: 10px;
            box-shadow: 1px 6px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .ani-icon {
            /*flex: 1 1 400px;*/
            padding: 0px;
            text-align: center;
            background-color: #f0f4f8;
        }
        .text-content {
            flex: 1 1 360px;
            padding: 40px;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        p {
            font-size: 18px;
            margin-bottom: 25px;
            color: #555;
        }
        .goback {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0056b3;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .goback:hover {
            background-color: #003f80;
        }

        @media (max-width: 768px) {
            .error-content {
                flex-direction: column;
            }
            .text-content, .ani-icon {
                padding: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="error-div">
    <div class="error-content">
        <div class="ani-icon">
            <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

            <dotlottie-player
                src="https://assets10.lottiefiles.com/packages/lf20_w51pcehl.json"
                background="transparent"
                speed="1"
                style="width: 100%; max-width: 460px;"
                loop
                autoplay>
            </dotlottie-player>
        </div>
        <div class="text-content">
            <h2 class="status">Active CISCE - Maintenance Notice</h2>
            <div class="maintenance-message">
                <p>Dear Users,</p>
                <p>
                    The Active CISCE Portal is temporarily unavailable due to essential
                    maintenance. We apologise for the inconvenience and request you to
                    try accessing the portal again shortly.
                </p>
                <p>
                    Thank you for your patience and cooperation.
                </p>
                <p class="team-name">Team Active CISCE</p>
            </div>
            <!-- <a href="{{ url()->previous() }}" class="goback">Go Back</a> -->
        </div>
    </div>
    <!-- <img src="{{ asset('assets/uploads/503.jpeg') }}" alt="Maintenance"> -->
</div>

</body>
</html>
