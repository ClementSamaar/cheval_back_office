<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="Views/style.css">
	<title>Interface d'admnistration</title>
</head>
	<section id="header">
		<a href="?ctrl=user"><i class="fa-solid fa-user"></i></a>
		<i class="fa-solid fa-clipboard-check"></i>
		<i class="fa-solid fa-comments"></i>
	</section>

    <?php
    if (isset($A_view)) {
        if ($A_view['select']) {
    ?>

    <h1><?= ucfirst($A_view['tableName']) ?></h1>
	<section id="buttons">
		<?php
            if (in_array('Insert', $A_view['userPrivileges'])) echo '<button class="add" onclick="openForm()">Ajouter</button>';
            if (in_array('Update', $A_view['userPrivileges'])) echo '<button class="add" onclick="openModif()">Modifier</button>';
            if (in_array('Delete', $A_view['userPrivileges'])) echo '<button class="add">Supprimer</button>';
        ?>
		<button onclick="window.location.href='?ctrl=log&action=logout'" class="add">Se déconnecter</button>

	  </section>
	  <div class="form-popup" id="myForm">
		<form method="POST" action="?ctrl=table&action=insertRow&table=<?= $A_view['tableName'] ?>" class="form-container">
		  <h2>Ajouter un élément</h2>
            <?php
            foreach ($A_view['tableAttributes'] as $attribute) {
                if ($attribute['COLUMN_NAME'] == $A_view['pk'] and $attribute['DATA_TYPE'] == 'bigint' or $attribute['DATA_TYPE'] == 'int')
                    continue;
                elseif ($attribute['DATA_TYPE'] == 'enum') {
                    echo '
                    <label for="' . $attribute['COLUMN_NAME'] . '"><b>' . $attribute['COLUMN_NAME'] . '</b></label>
                    <select name="' . $attribute['COLUMN_NAME'] . '" id="' . $attribute['COLUMN_NAME'] . '">';
                    if ($attribute['IS_NULLABLE']) echo '<option value="NULL"></option>';
                    preg_match_all("/'(\w+)'/", $attribute['COLUMN_TYPE'], $options);
                    foreach ($options[1] as $option) {
                        echo '<option value="' . $option . '">' . ucfirst($option) . '</option>';
                    }
                    echo '</select><br>';
                }
                else {
                    echo '
                    <label for="' . $attribute['COLUMN_NAME'] . '"><b>' . $attribute['COLUMN_NAME'] . '</b></label>
                    <input type="' . Table::getInputType($attribute['DATA_TYPE']) . '" 
                           name="' . $attribute['COLUMN_NAME'] . '" 
                           id="' . $attribute['COLUMN_NAME'] . '"';
                    if (isset($attribute['CHARACTER_MAXIMUM_LENGTH'])) echo ' maxlength="' . $attribute['CHARACTER_MAXIMUM_LENGTH'] . '"';
                    if ($attribute['IS_NULLABLE'] == 'NO') echo ' required';
                    echo '>';
                }
            }
            ?>
		  <button type="submit" class="btn">Ajouter</button>
		  <button type="button" class="btn cancel" onclick="closeForm1()">Fermer</button>
		</form>
	  </div>

	  <div class="form-popup" id="myForm1">
		<form method="POST" action="?ctrl=table&action=updateRow" class="form-container">
		  <h2>Modifier un élément</h2>
		  <button type="submit" class="btn">Mettre à jour</button>
		  <button type="button" class="btn cancel" onclick="closeForm()">Fermer</button>
		</form>
	  </div>

	<input type="checkbox" id="checkAll"> Tout cocher</input>


    <table>
        <thead>
        <tr>
            <th></th>
            <th></th>
            <?php
            foreach ($A_view['tableAttributes'] as $attribute) {
                echo '<th><a href="?ctrl=table&action=orderRows&table=' . $A_view['tableName'] . '&attribute=' . $attribute['COLUMN_NAME'];
                if (isset($_GET['order']) and $_GET['order'] == 'DESC') $order = 'ASC';
                else $order = 'DESC';
                echo '&order=' . $order . '&page=' . $_GET['page'] . '">' . $attribute['COLUMN_NAME'] . '</a></th>';
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($A_view['tableRows'] as $row){
            echo '<tr>';
            echo '<td><input type="checkbox" class="checkbox"></td>
                  <td>
                    <a href="?ctrl=table&action=deleteRow&table=' . $A_view['tableName'] . '&id=' . $row[$A_view['tableAttributes'][0]['COLUMN_NAME']] . '">
                        <i class="fa-solid fa-trash"></i>
                    </a> 
                    <a href="?ctrl=table&action=showUpdateForm&table=' . $A_view['tableName'] . '&id=' . $row[$A_view['tableAttributes'][0]['COLUMN_NAME']] . '">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                  </td>';
            foreach ($row as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <section class="pagination">
        <?php
                if ($A_view['page'] > 1)
                    echo '<a href="?ctrl=table&action=selectAllRows&table=' . $A_view['tableName'] . '&page=' . $A_view['page'] - 1 . ' ">&laquo;</a>';

                echo '<p>Page ' . $A_view['page'] . '</p>';

                if ($A_view['rowAmount'] > $A_view['page'] * 10 )
                    echo '<a href="?ctrl=table&action=selectAllRows&table=' . $A_view['tableName'] . '&page=' . $A_view['page'] + 1 . ' ">&raquo;</a>';
		    }
            else echo '<p>Vous n\'avez pas le droit de visionner cette vue</p>';
        }
        else echo ''
		?>
    </section>

	<script src="Js/script.js"></script>
</html>