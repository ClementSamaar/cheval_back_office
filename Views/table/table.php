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
<body>
	<section id="header">
		<a href="?ctrl=user"><i class="fa-solid fa-user"></i></a>
		<i class="fa-solid fa-clipboard-check"></i>
		<i class="fa-solid fa-comments"></i>
	</section>

    <?php
    if (isset($A_view)) {
        if ($A_view['select']) {
    ?>

	<section id="buttons">
		<?php
            if (in_array('Insert', $A_view['userPrivileges'])) echo '<button class="add" onclick="openForm()">Ajouter</button>';
            if (in_array('Update', $A_view['userPrivileges'])) echo '<button class="add" onclick="openModif()">Modifier</button>';
            if (in_array('Delete', $A_view['userPrivileges'])) echo '<button class="add">Supprimer</button>';
        ?>
		<button onclick="window.location.href='?ctrl=log&action=logout'" class="add">Se déconnecter</button>
	  </section>
	  <div class="form-popup" id="myForm">
		<form class="form-container">
		  <h2>Ajouter un élément</h2>
		  <label for="nom"><b>Nom</b></label>
		  <input type="text" placeholder="Entrer le nom" name="nom" id="nom" required>
	  
		  <label for="description"><b>Description</b></label>
		  <input type="text" placeholder="Entrer la description" name="description" id="description" required>
	  
		  <label for="prix"><b>Prix</b></label>
		  <input type="text" placeholder="Entrer le prix" name="prix" id="prix" required>
	  
		  <button type="submit" class="btn">Ajouter</button>
		  <button type="button" class="btn cancel" onclick="closeForm1()">Fermer</button>
		</form>
	  </div>


	  <div class="form-popup" id="myForm1">
		<form class="form-container">
		  <h2>Modifier un élément</h2>
		  <label for="nom"><b>Nom</b></label>
		  <input type="text" placeholder="Entrer le nom" name="nom" required>
	  
		  <label for="description"><b>Description</b></label>
		  <input type="text" placeholder="Entrer la description" name="description" required>
	  
		  <label for="prix"><b>Prix</b></label>
		  <input type="text" placeholder="Entrer le prix" name="prix" required>
	  
		  <button type="submit" class="btn">Mettre à jour</button>
		  <button type="button" class="btn cancel" onclick="closeForm()">Fermer</button>
		</form>
	  </div>
	  
	<input type="text" placeholder="Recherche...">
	<br><br>

	<input type="checkbox" id="checkAll"> Tout cocher</input>


    <table>
        <thead>
        <tr>
            <th> - </th>
            <?php
            foreach ($A_view['tableAttributes'] as $attribute) {
                echo '<th>' . $attribute . '</th>';
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($A_view['tableRows'] as $row){
            echo '<tr>';
            echo '<td>
                    <a href="?ctrl=table&action=deleteRow&table=' . $A_view['table'] . '&id=' . $row[$A_view['tableAttributes'][0]] . '"><i class="fa-solid fa-trash"></i></a> 
                    <a href="?ctrl=table&action=deleteRow&table=' . $A_view['table'] . '&id=' . $row[$A_view['tableAttributes'][0]] . '"><i class="fa-solid fa-pen-to-square"></i></a>
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
                    echo '<a href="?ctrl=table&action=showTable&table=' . $A_view['table'] . '&page=' . $A_view['page'] - 1 . ' ">&laquo;</a>';

                echo '<p>Page ' . $A_view['page'] . '</p>';

                if ($A_view['rowAmount'] > $A_view['page'] * 10 )
                    echo '<a href="?ctrl=table&action=showTable&table=' . $A_view['table'] . '&page=' . $A_view['page'] + 1 . ' ">&raquo;</a>';
		    }
            else echo '<p>Vous n\'avez pas le droit de visionner cette vue</p>';
        }
        else echo ''
		?>
    </section>

	<script src="Js/script.js"></script>
	  
</body>
</html>