<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/css/login.css<?= IN_DEVELOPMENT ?>" />

<style>
.logo {
	max-width: 100%;
	height: auto;
}
.footer {
	position: absolute;
	bottom: 0;
	width: 100%;
	height: 60px;
	line-height: 60px;
	background-color: #f8f9fa;
}
</style>
<div class="container-fluid ps-0 pe-0">
	<div class="row g-0">
		<!-- Left Panel -->
		<div class="col-md-4 col-lg-6 d-none d-sm-block login-left-panel"></div>

		<!-- Right Panel -->
		<div class="col-md-8 col-lg-6 login-right-panel">
			<div class="d-flex flex-column justify-content-between h-100">
				<div class="mt-3 ms-3">
					<div class="col-6">
						<img src="view/images/rla_logo.png" class="logo img-fluid" />
					</div>
				</div>

				<div class="d-flex justify-content-center">
					<div class="col-7">
						<form id="frmLogin">
							<h3 class="mb-3">Assalamuallaikum<br /></h3>

							<p>Silahkan isi user dan password Anda</p>

							<div class="mb-3">
								<input type="hidden" name="csrf" value="<?= $_SESSION[SESSION_NAME]->token ?? '-' ?>" />
								<input type="text" class="form-control" id="username" placeholder="Username" name="username" required>
							</div>

							<div class="mb-3">
								<input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
							</div>

							<!-- <div class="mb-3">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" />
									<label class="form-check-label" for="remember_me"> Ingat Saya </label>
								</div>
							</div> -->

							<div class="d-grid gap-2">
								<button type="submit" class="btn btn-primary">Masuk</button>
								<a href="#" id="a_need_help">Bantuan?</a>
							</div>

						</form>
					</div>
				</div>

				<div class="text-center mb-3">
					&copy; <?= NAME ?> <?= date('Y') ?> All rights reserved.
				</div>
			</div>
		</div>
	</div>
</div>

<!-- / Content -->
<script src="<?= URL_PUBLIC_VIEW ?>/assets/js/login.js<?= IN_DEVELOPMENT ?>"></script>
<?php if (isset($_COOKIE['rla_akunku']) && isset($_COOKIE['rla_passku'])) { ?>
<script>
	(function () {
		let username = '<?= $_COOKIE[NAME . '_akunku'] ?>'

		let password = '<?= $_COOKIE[NAME . '_passku'] ?>'

		let ingataku = '<?= $_COOKIE[NAME . '_ingataku'] ?>' ?? 0

		$('#username').val(username)

		$('#password').val(password)

		<?php if (isset($_COOKIE['rla_ingataku'])) { ?>
		$('#remember_me').attr('checked', true);
		<?php } ?>
	})()
</script>
<?php } ?>