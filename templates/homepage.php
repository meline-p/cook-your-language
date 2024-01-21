<?php $title = "Bienvenue !" ?>

<?php ob_start(); ?>

<h1>Bienvenue dans l'application de cuisine!</h1>
<?php require_once(__DIR__ . '/../templates/components/searchRecipies.php');
?>

<?php if (!empty($data['meals'])): ?>
<div class="row">
	<?php foreach ($data['meals'] as $recipe):?>
	<div class="col-sm-6 col-lg-3">
		<div class="card mb-3">
			<span class="btn btn-warning btn-sm"><?= $recipe['strArea'] ?></span>
			<img class="card-img-top" src="<?= $recipe['strMealThumb'] ?>" alt="Card image cap">
			<div class="card-body">
				<h5 class="card-title"><?= $recipe['strMeal']; ?></h5>
			</div>
		</div>
	</div>
	<?php endforeach;?>
</div>
<?php elseif($data['meals'] == null): ?>
<p>Aucune recette trouvée</p>
<?php endif;?>

<?php $content = ob_get_clean(); ?>

<?php
require_once(__DIR__ . '/../templates/parts/layout.php');
?>