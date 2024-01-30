<?php
defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\App;

$form_id = 'formEditBiodata';
?>
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme pe-0"
	id="layout-navbar">
	<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
		<a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
			<i class="bi bi-list"></i>
		</a>
	</div>

	<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
		<!-- Search -->
		<div class="navbar-nav align-items-center col">
			<div class="nav-item d-flex align-items-center col">
				<i class="bi bi-search fs-4 lh-0"></i>
				<form id="globalCari" class="col">
					<input name="keyword" type="search" class="form-control border-0 shadow-none" placeholder="Cari..."
						aria-label="Cari..." />
					<button type="submit" style="display:none"> - </button>
				</form>
			</div>
		</div>
		<!-- /Search -->

		<ul class="navbar-nav flex-row align-items-center ms-auto">
			<!-- Spare buat keperluan nanti -->
			<li class="nav-item lh-1 me-3">
				<span></span>
			</li>

			<!-- User -->
			<li class="nav-item navbar-dropdown dropdown-user dropdown">
				<a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
					<div class="avatar avatar-online">
						<!-- <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle"> -->
						<?= App::viewPhoto() ?>
					</div>
				</a>
				<ul id="userMenu" class="dropdown-menu dropdown-menu-end mt-2">
					<li>
						<a class="dropdown-item" href="javascript:;">
							<div class="d-flex">
								<div class="flex-shrink-0 me-3">
									<div class="avatar avatar-online">
										<!-- <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle"> -->
										<?= App::viewPhoto() ?>
									</div>
								</div>
								<div class="flex-grow-1">
									<span class="fw-semibold d-block">
										<?= App::$loggedInUser->name ?>
									</span>
									<small class="text-muted">
										<?= App::$loggedInUser->user ?>
									</small>
								</div>
							</div>
						</a>
					</li>
					<li>
						<div class="dropdown-divider"></div>
					</li>
					<li>
						<a class="dropdown-item" id="navGetProfile" href="user/getProfile">
							<i class="bi bi-person-lines-fill me-2"></i>
							<span class="align-middle">Biodata</span>
						</a>
					</li>
					<li>
						<a class="dropdown-item" href="javascript:;">
							<i class="bi bi-person-fill-exclamation me-2"></i>
							<span class="align-middle"><s>Notifikasi</s></span>
						</a>
					</li>
					<li>
						<a class="dropdown-item" href="javascript:;">
							<span class="d-flex align-items-center align-middle">
								<i class="bi bi-chat-text me-2"></i>
								<span class="flex-grow-1 align-middle"><s>Pesan</s></span>
								<span
									class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">0</span>
							</span>
						</a>
					</li>
					<li>
						<div class="dropdown-divider"></div>
					</li>
					<li>
						<a class="dropdown-item" href="logout">
							<i class="bi bi-box-arrow-right me-2"></i>
							<span class="align-middle">Log Out</span>
						</a>
					</li>
				</ul>
			</li>
			<!--/ User -->
		</ul>
	</div>
</nav>

<script>
	(function () {
		/**
		 * Pencarian global
		 */
		$('#globalCari').submit(function (e) {
			e.preventDefault()

			// new Toast({id:'cariGlobal', title:'Pencarian', text:'Fitur Cari belum dikembangkan.', type:'error', timeOut: false})

			App.cari($(this).serializeArray().find(x => x.name == 'keyword').value)
		})


		/**
		 * User menu
		 */
		$('#userMenu a').click(function (e) {
			const link = $(this).attr('href')

			if (link == 'logout') return

			e.preventDefault()

			// console.log(link)

			if (link == 'javascript:;') return

			let myLink = $(this).attr('href')
			let myId = 'modalEditProfile'
			let myJudul = 'Edit Biodata'
			let myForm = '<?= $form_id ?>'
			let mySubmit = 'user/put'
			let tipe = 'edit'
			let dataValid = (data) => {
				let newPassword = data.find(x => x.name == 'new_password').value
				let confirmPassword = data.find(x => x.name == 'confirm_password').value

				if (newPassword != confirmPassword) {
					new Toast({ id: 'toastProfile', text: 'Kolom Ulang Password harus sama dengan Password Baru.', title: 'Edit Profile', type: 'error', timeOut: 5000 })

					return false
				}

				return true
			}

			App.modalTampil({
				id: myId,
				judul: myJudul,
				kaki: `<button type="submit" class="btn btn-primary me-2" form="${myForm}">Perbarui</button><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>`,
				size: 'lg',
				icon: 'bx bx-user',
				scrollable: 1,
				onShown: function () {
					$('.modal-body')
					/**
					 * Submit
					 */
					$('form#' + myForm).submit(function (e) {
						e.preventDefault()

						let data = $(this).serializeArray()

						if (!dataValid(data)) return

						new Toast({ id: 'toastProfile', text: 'Menyimpan...', type: 'info', timeOut: false })

						Ajax.kirim({
							url: mySubmit,
							data: data,
						}).done((respon) => {
							let status = respon.status || false
							let pesan = respon.pesan || false
							let tipe = 'success'
							let time = 5000

							if (status == 'gagal') {
								tipe = 'danger'
								time = false
							}

							new Toast({ id: 'toastProfile', text: pesan, timeOut: time, type: tipe });
						})
					})


					/**
					 * Initial awal: tarik data profile diri
					 */
					Ajax.kirim({
						url: myLink,
					}).done((myProfile) => {
						if (!myProfile.status || myProfile.status == 'gagal') {
							new Toast({ text: 'Gagal! Tidak ada data dari server!', type: 'danger', timeOut: 9000 })
						}

						/* Muat form dengan data user */
						myProfile = myProfile['rows'][0] || []

						myProfile['c_nama'] = myProfile['c_nama'].replaceAll(',', ', ')

						$('#' + myForm).form('load', myProfile)
					})
				},
				badan: `
					<?php require("view/html/systems/user/profile.php"); ?>
				`,
			})
		})
	})()
</script>