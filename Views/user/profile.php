<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="Views/log/profile.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
</head>
<body>

    <h1>Liste des droits</h1>
    <?php
    if (empty($A_view['userPrivileges']))
        echo '<p>Cet utilisateur n\'a pas de droits</p>';
    else {?>
        <ul>
            <?php
            foreach ($A_view['userPrivileges'] as $userPrivilege){
                echo '<li>' . $userPrivilege . '</li>';
            }
            ?>
        </ul>
    <?php } ?>

    <h1>Liste des tables accessibles</h1>
    <?php
    if (empty($A_view['userTable']))
    echo '<p>Cet utilisateur n\'a accès à aucune table</p>';
    else {?>
        <ul>
            <?php
            foreach ($A_view['userTable'] as $userPrivilege){
                echo '<li>' . $userPrivilege . '</li>';
            }
            ?>
        </ul>
    <?php } ?>
</body>
</html>