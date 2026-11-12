<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {margin: 0; font-family: 'Inter', sans-serif;background: #f8f9fa;color: #333;display: flex;align-items: center; justify-content: center;
      height: 100vh;  padding: 20px; }
    .card { background: #fff;  max-width: 500px;  width: 100%;border-radius: 12px;padding: 40px 30px;box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      text-align: center; }
    .card h1 { font-size: 24px; margin-bottom: 20px; color: #dc3545;  }
    .card p { font-size: 16px; margin-bottom: 25px; line-height: 1.6; }
    .btn { display: inline-block;  padding: 10px 20px; background-color: #0d6efd;  color: #fff; border-radius: 6px; text-decoration: none;
      font-weight: 600;  transition: background-color 0.3s; }
    .btn:hover {  background-color: #084298; }
    .footer { margin-top: 30px; font-size: 13px; color: #666; }
  </style>
</head>
<body> 

  <div class="card">
    <h1>Student Data Not Found</h1>
    <p>
      It looks like  school has not uploaded any student data yet.<br>
      Please log in using the credentials provided to you and upload the student list from the <strong>Student Manage</strong> module.
    </p>
    <a href="https://active.cisce.org/login" class="btn btn-primary">Go to Login</a>
    <div class="footer">
      Need help? Contact support at <a href="mailto:fitness.assessment@cisce.org">fitness.assessment@cisce.org</a>
    </div>
  </div>

</body>
</html>
