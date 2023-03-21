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
		<a href="log/logout.php"><i class="fa-solid fa-user"></i></a>
		<i class="fa-solid fa-clipboard-check"></i>
		<i class="fa-solid fa-comments"></i>
	</section>
	
    <?php
    if (isset($A_view)) {
        if (!isset($A_view['select'])) {
    ?>
	<section id="buttons">
		<button class="add">Ajouter</button>
    </section>

	  
	<input type="text" placeholder="Recherche...">
	<br><br>

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
        }
		?>
	</section>

	




	<script src="script.js"></script>
	  
</body>
</html>