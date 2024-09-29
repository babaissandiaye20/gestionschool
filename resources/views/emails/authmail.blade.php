<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4CAF50;
            padding: 10px;
            border-radius: 5px 5px 0 0;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue {{ $prenom }} {{ $nom }}</h1>
        </div>
        <p>Bonjour {{ $prenom }},</p>
        <p>Voici vos informations d'authentification :</p>
        <ul>
            <li>Nom : {{ $nom }}</li>
            <li>Prénom : {{ $prenom }}</li>
            <li>Email : {{ $email }}</li>
            <li>Mot de passe : {{ $password }}</li>
        </ul>
    </div>
</body>
</html>
