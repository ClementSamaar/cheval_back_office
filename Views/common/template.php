<?php
if (isset($A_view)){ ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $A_view['title'] ?></title>
</head>
<body>
<?php
    View::show($A_view['bodyView'], $A_view['bodyContent']);
}
else echo '<p>There is nothing to display, you might have made a mistake</p>';
?>
</body>
</html>
