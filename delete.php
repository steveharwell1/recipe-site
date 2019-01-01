<?php
	require('templates/recipe_header.php');
	require_once('templates/dbi.php');
	
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
		$del_recipe = $conn->prepare('DELETE FROM RECIPES WHERE RECIPE_ID = ?');
		$del_recipe->bind_param("i", $id);
		if($del_recipe->execute()){
			print('Recipe '.htmlspecialchars($id).' is now deleted. <br>');
		} else {
			print(htmlspecialchars($del_recipe->error()));
		}
	}
	else
	{
		print('<div class="alert">Please navigate back to the recipe that you would like to delete and select the delete link.<br>');
	}
	require('templates/footer.php');
?>