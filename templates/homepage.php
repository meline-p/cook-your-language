<?php $title = "Bienvenue !" ?>

<?php ob_start(); ?>

<h1>Bienvenue dans l'application de cuisine!</h1>


<div class="row">
	<div class="col-sm-6 col-lg-3">
		<div class="card mb-3">
			<span class="btn btn-warning btn-sm">Temps total : <?= $data['recipe']['total_time'] ?></span>
			<span class="btn btn-warning btn-sm">Nombre de personnes :
				<?= $data['recipe']['number_of_people'] ?></span>
			<h5 class="card-title"><?=$data['recipe']['name']?></h5>
			<div class="card-body">
				<h5 class="card-title">Ingrédients</h5>
				<?php foreach($data['recipe']['ingredients'] as $ingredient) :?>
				<li><?php if ($ingredient['quantity'] !== null){
						echo $ingredient['name'] . ' - ' . $ingredient['quantity'];
					} else {
						echo $ingredient['name'];
					}?>
				</li>
				<?php endforeach; ?>
			</div>
			<div class="card-body">
				<h5 class="card-title">Etapes</h5>
				<?php foreach($data['recipe']['steps'] as $step) :?>
				<?php if (is_array($step['ingredient'])) ?>
				<li><?php
				if (is_array($step['ingredient'])) {
				    echo $step['action'] . ' : ' .implode(', ', $step['ingredient']);
				} elseif ($step['ingredient'] != null) {
				    echo $step['action'] . ' : ' . $step['ingredient'];
				} else {
				    echo $step['action'];
				};
				    ?>
				</li>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>


<?php $content = ob_get_clean(); ?>

<?php
require_once(__DIR__ . '/../templates/parts/layout.php');
?>