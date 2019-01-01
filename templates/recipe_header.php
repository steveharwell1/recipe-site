<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recipe Book</title>
    <link rel="stylesheet" href="css/main1.css">
    <script src="js/script.js"></script>

</head>

<body>

<nav>
        <a href="index.php">Home</a>
        <a href="recipes.php">Recipe Book</a>
        <a href="create.php">Create</a>
        <a href="tables.php">DB Tables</a>
        <div id="hamburger">Search</div>
</nav>
<?php
    require_once('templates/dbi.php');

    $side_result = $conn->query("SELECT INGREDIENTS.INGREDIENT_ID, INGREDIENTS.NAME, count(*) CNT
    FROM INGREDIENTS
    INNER JOIN RECIPE_INGREDIENT
    ON RECIPE_INGREDIENT.INGREDIENT_ID = INGREDIENTS.INGREDIENT_ID
    GROUP BY INGREDIENTS.INGREDIENT_ID
    ORDER BY INGREDIENTS.NAME");

    //print($conn->errors());
    print('<div id="sidebar" class="hide"><h2>Recipes by Ingredients</h2>');
    while($row = $side_result->fetch_assoc())
      {
          $ingr = htmlspecialchars(addslashes($row['INGREDIENT_ID']));
          $name = htmlspecialchars(addslashes($row['NAME']));
          $cnt = htmlspecialchars(addslashes($row['CNT']));

        print('<a href="recipes.php?ingr='.$ingr.'">'.$name.'<span class="round">'.$cnt.'<span></a>');
      }
    //tableify($side_result);
    print('</div>');

?>

<div id="flash"></div>