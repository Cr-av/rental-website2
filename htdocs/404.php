<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Strona nie znaleziona</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #653b8d;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #5b2f89;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(151, 76, 156, 0.11);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }

        h1 {
            font-size: 100px;
            color:rgb(255, 255, 255);
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color:rgb(255, 255, 255);
            margin: 0 0 20px;
        }

        .button {
            display: inline-block;
            background-color:rgb(42, 192, 67);
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #4cd964;
        }

        .button:active {
            background-color:rgb(32, 197, 59);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Strona, której szukasz, nie istnieje.</p>
        <a href="index.php" class="button">Wróć na stronę główną</a>
    </div>
</body>
</html>


