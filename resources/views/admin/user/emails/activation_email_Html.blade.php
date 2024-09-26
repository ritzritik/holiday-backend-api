<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Sky Sea Holiday!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #242E88;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
            color: #333333;
        }
        .content h2 {
            color: #242E88;
        }
        .content p {
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
        }
        .button {
            background-color: #242E88;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #777777;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Sky Sea Holidays!</h1>
        </div>
        <div class="content">
            <h2>Thank You for Registering!</h2>
            <p>Dear {{ $name }},</p>
            <p>
                We are thrilled to welcome you to Sky Sea Holidays, your gateway to exciting holiday packages and unforgettable experiences! Thank you for registering on our website. We are committed to providing you with the best deals and the most memorable vacations.
            </p>
            <p>
                Whether you're looking for a serene beach getaway, an adventurous mountain trek, or a cultural city tour, we have something special for you. As a registered member, you'll be the first to know about our exclusive offers and latest holiday packages.
            </p>
            <div class="button-container">
                <a href="https://main--skyseaholidays.netlify.app/package" class="button">Explore Holiday Packages</a>
            </div>
            <p>
                We're excited to help you plan your next holiday adventure. If you have any questions or need assistance, feel free to contact our support team at any time.
            </p>
            <p>
                Happy Travels!
            </p>
            <p>
                Warm Regards,<br>
                The Sky Sea Holidays Team
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Sky Sea Holidays. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
