<?php
require('templates/recipe_header.php');
require_once('templates/dbi.php');
#phpinfo();
$messages = array();
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$stmt = $conn->prepare("UPDATE RECIPES SET TITLE=?, SUBTITLE=?, INSTRUCTIONS=? WHERE RECIPE_ID=?");
	$stmt->bind_param("ssss", $title, $subtitle, $instructions, $ID);
	
	$title = $_POST["title"];
	$subtitle = $_POST["subtitle"];
	$instructions = $_POST["instructions"];
	$ID = $_POST["hidden_id"];
	$id = $ID;
	if( false === $stmt->execute())
	{
		array_push($errors, "Your recipe creation failed because: ".htmlspecialchars($stmt->error));
	} else 
	{
		/////////
		$uploadable = true;
		if (
			!isset($_FILES['image']['error']) ||
			is_array($_FILES['image']['error'])
		) {
			array_push($errors, 'Invalid image parameters.');
			$uploadable = false;
		}
	
		// Check $_FILES['image']['error'] value.
		switch ($_FILES['image']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				array_push($errors, 'No file sent.');
				$uploadable = false;
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				array_push($errors, 'Exceeded filesize limit.');
				$uploadable = false;
				break;
			default:
				array_push($errors, 'Unknown errors.');
				$uploadable = false;
		}
	
		// // You should also check filesize here.
		if ($_FILES['image']['size'] > 1000000) {
			array_push($errors, 'Exceeded filesize limit.');
			$uploadable = false;
		}
	
		// // DO NOT TRUST $_FILES['image']['mime'] VALUE !!
		// // Check MIME Type by yourself.
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search(
			$finfo->file($_FILES['image']['tmp_name']),
			array(
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
			),
			true
		)) {
			array_push($errors, 'Invalid file format.');
			$uploadable = false;
		}
	
		// You should name it uniquely.
		// DO NOT USE $_FILES['image']['name'] WITHOUT ANY VALIDATION !!
		// On this example, obtain safe unique name from its binary data.
		if($uploadable) {
			$target_file = 	sprintf('./uploads/%s.%s', sha1_file($_FILES['image']['tmp_name']), $ext);
			if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
				array_push($errors, 'Failed to move uploaded file.');
				$uploadable = false;
			} else {
				$img_stmt = $conn->prepare("UPDATE RECIPES SET IMAGE=? where RECIPE_ID = ?");
				$img_stmt->bind_param("ss", $target_file, $id);
				$img_stmt->execute();
				$img_stmt->close();
			}

		}

		$amounts = $_POST["amount"];
		$measures = $_POST["measure"];
		$ingredients = $_POST["ingredient"];
		$del_stmt = $conn->prepare("DELETE FROM RECIPE_INGREDIENT WHERE RECIPE_ID = ?");
		$del_stmt->bind_param("s", $id);
		$del_stmt->execute();
		$ing_stmt = $conn->prepare("INSERT INTO INGREDIENTS (NAME) VALUES (?)");
		$ing_stmt->bind_param("s", $ingredient);
		$measure_stmt = $conn->prepare("INSERT INTO RECIPE_INGREDIENT (QUANTITY, MEASURE, INGREDIENT_ID, RECIPE_ID) VALUES (?, ?, ?, ?)");
		$measure_stmt->bind_param("ssii", $amount, $measure, $ing_id, $id);
		$ing_search = $conn->prepare("SELECT INGREDIENT_ID FROM INGREDIENTS WHERE NAME = ?");
		$ing_search->bind_param("s", $ingredient);
		for ($x = 0; $x < count($amounts); $x++)
		{
			$ingredient = $ingredients[$x];
			$amount = $amounts[$x];
			$measure = $measures[$x];
			if($ingredient == "" or $amount == "") { continue; }
			if($ing_stmt->execute()){
				$ing_id = $conn->insert_id;
			} else {
			$ing_search->execute();
			$ing_search->store_result(); 
			$ing_search->bind_result($ing_id);
			if($ing_search->num_rows == 1) {$ing_search->fetch();}
			else{print("Database error: unable to retrieve ingredient");}
			}
			$measure_stmt->execute();
		}
		
		if($uploadable){
			array_push($messages, '<h2>Recipe updated successfully</h2><div id="individual"><a href="recipes.php?id='.$id.'">See your new post</a></div>');
		} else {
			array_push($messages, '<h2>Recipe updated successfully. No new image saved.</h2><div id="individual"><a href="recipes.php?id='.$id.'">See your new post</a></div>');
		}
	}
	$stmt->close();
	

}
$title = "";
$subtitle = "";
$image = "";
$instructions = "";
$single = -1;
if(isset($_GET['id'])){
	$single = (int)$_GET['id'];
	$query = "select * from RECIPES WHERE RECIPE_ID = ".$single;
	$result = $conn->query($query);
	if($row = $result->fetch_assoc()){
		$title = htmlspecialchars( stripslashes($row["TITLE"]));
		$subtitle = htmlspecialchars( stripslashes($row["SUBTITLE"]));
		$image = htmlspecialchars($row["IMAGE"]);
		$instructions = htmlspecialchars( stripslashes($row["INSTRUCTIONS"]));
	}
	else {
		print("Recipe could not be retrieved");
	}
}

?>

<form method="POST" enctype="multipart/form-data">
	<fieldset>
		<legend>Create a new recipe</legend>
		<label for="title">Title</label>
		<input id="title" name="title" type="text" value="<?php echo $title ?>" required><br>
		
		<label for="subtitle">Subtitle</label>
		<input id="subtitle" name="subtitle" type="text" value="<?php echo $subtitle ?>"><br>
		
		<label for="image">Image</label>
		<input type="file" name="image" id="image"><img src="<?php echo $image ?>" height="50" width="50"><br>
		<fieldset id="ing_set">
			<legend>Add Ingredients</legend>
			<?php
			if(isset($_GET['id'])){
				$query = "select * from RECIPE_INGREDIENT INNER JOIN INGREDIENTS ON INGREDIENTS.INGREDIENT_ID = RECIPE_INGREDIENT.INGREDIENT_ID WHERE RECIPE_INGREDIENT.RECIPE_ID = ".$single;
	$recipe_result = $conn->query($query);
	if($recipe_result->num_rows > 0)
	{
		print('<input type="number" placeholder="1" name="amount[]"  step=".01" min="0"><input type="text" placeholder="cup" name="measure[]"><input type="text" placeholder="sugar" name="ingredient[]"><u id="add_btn">Add Another Ingredient</u><br>');
		while($recipe_row = $recipe_result->fetch_assoc())
		{
			print('<input type="number" name="amount[]" value="'.$recipe_row["QUANTITY"].'"  step=".01" min="0"><input type="text" placeholder="cup" name="measure[]" value="'.$recipe_row["MEASURE"].'"><input type="text" placeholder="sugar" name="ingredient[]" value="'.$recipe_row["NAME"].'"><br>');
			
		}

	} else
	{
		print('<input type="number" placeholder="1" name="amount[]"  step=".01" min="0"><input type="text" placeholder="cup" name="measure[]"><input type="text" placeholder="sugar" name="ingredient[]"><u id="add_btn">Add Another Ingredient</u><br><input type="number" placeholder="1" name="amount[]" step=".01"><input type="text" placeholder="cup" name="measure[]"><input type="text" placeholder="sugar" name="ingredient[]">');
	}
	}
?>


			
		</fieldset>
		
		<label for="instructions">Recipe Directions</label><br>
		<textarea id="instructions" name="instructions" type="textarea" rows="20" cols="60" ><?php echo $instructions?></textarea><br>
		<br>{{br}} for new line.
	</fieldset>
	<input type="hidden" value="<?php echo $single ?>" name="hidden_id">
	<button name="submit" type="submit">Update</button>
</form>

<script>
	var ing = document.getElementById("ing_set");
	document.getElementById("add_btn").addEventListener("click", function() {
		let node = document.createElement("div");
		node.innerHTML = '<input type="number" placeholder="1" name="amount[]"  step=".01" min="0"><input type="text" placeholder="cup" name="measure[]"><input type="text" placeholder="sugar" name="ingredient[]">';
		ing.appendChild(node);
	
		console.log("Hello");
	});
</script>
<?php
	require('templates/footer.php');
?>


