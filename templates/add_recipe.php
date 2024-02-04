<?php $title = "Bienvenue !" ?>

<?php ob_start(); ?>

<h1>Ajouter une recette</h1>
<form action="/post_add_recipe" method="post">
	<label>Lier à une recette existante :</label>
	<select name="linkTo" class="form-control">
		<option value="" selected>Nouvelle recette</option>
		<?php foreach($recipesNames as $recipe) :?>
		<option value=<?= $recipe->id ?>>
			<?= $recipe->fr ? '*'.$recipe->fr.' (fr)' : "" ?>
			<?= $recipe->en ? '*'.$recipe->en.' (en)' : "" ?>
			<?= $recipe->es ? '*'.$recipe->es.' (es)' : "" ?>
		</option>
		<?php endforeach; ?>
	</select>
	<br>
	<label>Dans quelle langue est la recette à insérer ?</label>
	<select class="form-control" name="lang">
		<option value="fr">Français</option>
		<option value="en">Anglais</option>
		<option value="es">Espagnol</option>
	</select>
	<br>
	<label>Insérer le json de la recette : </label>
	<textarea rows="10" class="form-control" name="data-chatgpt"></textarea>
	<br>
	<button class="btn btn-primary" type="submit">Ajouter la recette</button>
</form>



<?php $content = ob_get_clean(); ?>

<?php
require_once(__DIR__ . '/../templates/parts/layout.php');
?>