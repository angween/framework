<link rel="stylesheet" href="view/assets/css/login.css" />

<style>
/* Custom styles */
badan {
	min-height: 100vh;
	display: flex;
	flex-direction: column;
}

.footer {
	/* margin-top: auto; */
	background-color: #f8f9fa;
	padding: 10px 0;
	text-align: center;
}
</style>

<div class="badan min-vh-100 d-flex flex-column justify-content-between">
	<div class="container-fluid bg-light text-dark py-2 border-bottom border-secondary">
		<div class="d-flex justify-content-center justify-content-md-start">
		<div class="col-8 col-sm-2 col-lg-1">
				<!-- <img src="view/images/silungkang_logo.png" class="" style="width:10vw"/> -->
				<img src="view/images/silungkang_logo.png" class="img-fluid" style="max-width: 100%; height: auto;" />
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row justify-content-center">
			<div class="authentication-wrapper authentication-basic container-p-y flex-column justify-content-between" style="min-height:600px">
				<div class="authentication-inner border-0 border-secondary rounded">
					<!-- Register -->
					<div class="card bg-transparent border-0">
						<img class="app-brand-logo img-fluid" style="max-width: 100%; height: auto;" src="view/images/ahlan_wasahlan.svg" />

						<div class="card-body">
							<h6 class="mb-3 text-center text-secondary">
								Welcome back, <small>sign in with your <br/>existing <strong><?= COMPANY_NAME ?></strong> account</small>
							</h6>

							<form id="frmLogin">
								<div class="form-floating mb-3">
									<input type="hidden" name="csrf" value="<?= $_SESSION[SESSION_NAME]->token ?? '-' ?>" />
									<input type="text" class="form-control" id="username" name="username" 
										title="Harus diisi" 
										placeholder="" autofocus required />
									<label for="username" class="form-label">Username</label>
								</div>

								<div class="form-floating mb-3 form-password-toggle">
									<div class="input-group input-group-lg input-group-merge">
										<input type="password" id="password" class="form-control" name="password"
											000placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
											placeholder="Password"
											title="" required />
										<span class="input-group-text cursor-pointer"><i class="bi bi-eye-slash"></i></span>
									</div>
								</div>
								<div class="mb-3">
									<div class="d-flex justify-content-between">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" />
											<label class="form-check-label" for="remember_me"> Remember me</label>
										</div>

										<a tabindex="-1" href="#">
											<small>Lost Password?</small>
										</a>
									</div>
								</div>
								<div class="mb-3">
									<button class="btn btn-primary btn-lg d-grid w-100" type="submit">Login</button>
								</div>
							</form>
						</div>
					</div>
					<!-- /Register -->
				</div>
			</div>
		</div>
	</div>

	<footer class="footer bg-secondary text-bg-dark">
	<div class="container">
		&copy; 2023 RLA Technology. All rights reserved.
	</div>
	</footer>
</div>


<!-- / Content -->
<script src="view/assets/js/login.js"></script>
<?php if (isset($_COOKIE[NAME . '_akunku']) && isset($_COOKIE[NAME . '_passku'])) { ?>
<script>
	(function () {
		let username = '<?= $_COOKIE[NAME . '_akunku'] ?>'

		let password = '<?= $_COOKIE[NAME . '_passku'] ?>'

		let ingataku = '<?= $_COOKIE[NAME . '_ingataku'] ?>' ?? 0

		$('#username').val(username)

		$('#password').val(password)

		<?php if (isset($_COOKIE[ NAME . '_ingataku'])) { ?>
			$('#remember_me').attr('checked', true);
		<?php } ?>
	})()
</script>
<?php } ?>