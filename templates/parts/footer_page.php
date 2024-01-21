<footer class="bg-light text-center text-lg-start mt-auto">
	<div class="text-center p-3">

		<?php if(isset($_SESSION['user']) && $_SESSION['user']->role_name === 'Administrateur'): ?>
		<a class="btn btn-outline-dark space" href="/admin/dashboard">Administration</a>
		<?php endif ?>

		<p class="text-dark">©<?= date("Y") ?> Copyright: CookYourLanguage</p>
	</div>
</footer>

</body>

</html>