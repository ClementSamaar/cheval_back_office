<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="Views/log/login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>

    <form method="POST" action="?ctrl=log&action=login">
        <div class="form-group">
          <label for="username">Nom d'utilisateur :</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Mot de passe :</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
         <button type="submit" name="submit" id="submit">Se connecter</button>
        </div>
        <?php
        if (isset($A_view['error']))
            if ($A_view['error'] == '1') echo '<p>Les informations entrées sont incorrectes, veuillez réessayer</p>';
            if ($A_view['error'] == '2') echo '<p>Ce compte est inaccessible depuis cette interface</p>';
        ?>
    </form>
      
    
</body>
</html>