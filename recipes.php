<?php
require('templates/recipe_header.php');
require_once('templates/dbi.php');
require_once('models.php');
$result = null;
if(isset($_GET['id'])){
	$single = (int)$_GET['id'];
	$query = "SELECT * from RECIPES WHERE RECIPE_ID = ".$single;
	$result = $conn->query($query);
	}
	else if (isset($_GET['ingr']))
	{
		$stmt = $conn->prepare("SELECT * FROM RECIPES 
								INNER JOIN RECIPE_INGREDIENT
								ON RECIPES.RECIPE_ID = RECIPE_INGREDIENT.RECIPE_ID
								WHERE INGREDIENT_ID = ?
								ORDER BY RECIPES.TITLE;");
		$stmt->bind_param("s", $_GET['ingr']);
		$stmt->execute();
		$result = $stmt->get_result();
		//$stmt->close();
	}
	else
	{
	$query = "SELECT * from RECIPES ORDER BY RECIPES.TITLE";
	$result = $conn->query($query);
	}
echo '<main class="recipe-container">';
while($row = $result->fetch_assoc())
{
echo '<div class="recipe-card"><h2>';
echo htmlspecialchars( stripslashes($row["TITLE"]));
echo '</h2><h3>';
echo htmlspecialchars( stripslashes($row["SUBTITLE"]));
echo "</h3>";
echo '<img src="'.htmlspecialchars( $row["IMAGE"]).'?'.filemtime($row["IMAGE"]).'" alt="image of food"><br>';
	$query = "select * from RECIPE_INGREDIENT INNER JOIN INGREDIENTS ON INGREDIENTS.INGREDIENT_ID = RECIPE_INGREDIENT.INGREDIENT_ID WHERE RECIPE_INGREDIENT.RECIPE_ID = ".$row["RECIPE_ID"];
	$recipe_result = $conn->query($query);
	if($recipe_result->num_rows > 0)
	{
		echo "<ul>";
		while($recipe_row = $recipe_result->fetch_assoc())
		{
			echo "<li>".$recipe_row["QUANTITY"]." ".$recipe_row["MEASURE"]." ".$recipe_row["NAME"]."</li>";
			
		}
		echo "</ul>";
	} else
	{
		echo "No ingredients!";
	}
echo "<p>".str_replace('{{br}}','<br>', htmlspecialchars( stripslashes($row["INSTRUCTIONS"]))) ."</p>";
print ('<div class="options"><a href="update.php?id='.$row["RECIPE_ID"].'">Update</a>');
print ('<a href="delete.php?id='.$row["RECIPE_ID"].'">Delete</a></div>');
echo "</div>";

}
echo '</main>';

$conn->close();
require('templates/footer.php');
?>