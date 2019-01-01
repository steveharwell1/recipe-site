<?php
	require('templates/recipe_header.php');
	require_once('templates/dbi.php')
?>

<main>

<?php
print('<div class="table">');
$query = "SELECT * FROM RECIPES;";
print('<p class="sql-code">' . $query . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="table">');
$query = "SELECT * FROM RECIPE_INGREDIENT;";
print('<p class="sql-code">' . $query . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="table">');
$query = "SELECT * FROM INGREDIENTS;";
print('<p class="sql-code">' . $query . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');
?>
</main>
<?php
	require('templates/footer.php');
?>