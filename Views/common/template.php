<?php
if (isset($A_view)){ ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./assets/css/style.scss" rel="stylesheet">
    <title><?= $A_view['title'] ?></title>
</head>
<body>
<?php
    View::show('common/header', null);
    View::show($A_view['bodyView'], $A_view['bodyContent']);
    View::show('common/footer', null);
}
else echo '<p>There is nothing to display, you might have made a mistake</p>';
?>
</body>
</html>
