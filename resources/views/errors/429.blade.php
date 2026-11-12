 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Too Many Login Attempts</title>
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
            padding: 15px;
            box-sizing: border-box;
        }
        .error-content {
            width: 100%;
            max-width: 1000px;
            display: flex;
            background: #fff;
            border-radius: 10px;
            box-shadow: 1px 6px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .ani-icon {
            padding: 20px;
            text-align: center;
            background-color: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1 1 400px;
        }
        .text-content {
            flex: 1 1 360px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .status {
            font-size: 30px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #555;
        }
        .team-name {
            font-weight: 600;
            color: #2c3e50;
        }
        @media (max-width: 768px) {
            .error-content {
                flex-direction: column;
            }
            .text-content {
                padding: 25px 20px;
                text-align: center;
            }
            .ani-icon {
                padding: 10px;
            }
            .status {
                font-size: 25px;
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
            <h2 class="status">Too Many Login Attempts!</h2>
            <div class="maintenance-message">
                <p>
                    You have exceeded the maximum number of login attempts.
                    For security reasons, your login access has been temporarily restricted.
                </p>

                <p>
                    Please try again after
                    <strong>{{ max(1, (int) ceil(($retryAfter ?? 900) / 60)) }} minutes</strong>.
                </p>

                <p class="team-name">Team Active CISCE</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
