<!-- resources/views/email/verify_success.blade.php -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Success</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #D7DBE4; /* Optional: Background color */
        }

        .container {
            text-align: center;
            background-color: #0C3C61FF; /* Container color */
            padding: 30px;
            border-radius: 10px;
            color: white;
            width: 80%;
            max-width: 600px; /* Optional: Max width for the container */
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        p {
            font-size: 1.2rem;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Email has been Successfully Verified!</h1>
        <p>Thank you for verifying your email address. You can now <a href="https://techfix.online/login" style="color: white; text-decoration: underline;">return to the website</a> and proceed to log in.</p>
    </div>
</body>
</html>

