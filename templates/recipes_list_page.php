<?php $title = "Bienvenue !" ?>

<?php ob_start(); ?>

<h1>Bienvenue dans l'application de cuisine!</h1>
<div class="row">
	<?php foreach ($data['meals'] as $recipe):?>
	<div class="col-sm-6 col-lg-3">
		<div class="card">
			<span class="btn btn-warning btn-sm"><?= $recipe['strArea'] ?></span>
			<img class="card-img-top" src="<?= $recipe['strMealThumb'] ?>" alt="Card image cap">
			<div class="card-body">
				<h5 class="card-title"><?= $recipe['strMeal']; ?></h5>
				<p class="card-text"><?= $recipe['strInstructions'] ?></p>
			</div>
			<div class="card-footer bg-transparent border-dark">
				<?php for ($i = 1; $i <= 6; $i++): ?>
				<li><?= $recipe['strIngredient' . $i]; ?> -
					<?= $recipe['strMeasure' . $i]; ?>
				</li>
				<?php endfor; ?>
			</div>
		</div>
	</div>
	<?php endforeach;?>
</div>

<?php $content = ob_get_clean(); ?>

<?php
require_once(__DIR__ . '/../templates/parts/layout.php');
?>