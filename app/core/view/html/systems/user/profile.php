<?php
defined('APP_OWNER') or exit('No direct script access allowed');
?>

				<!-- 
				<div class="py-4">
					<div class="d-flex align-items-start align-items-sm-center gap-4">
						<img src="view/assets/images/dummy_photo.png" alt="user-avatar" class="d-block rounded border border-dark" height="100" width="100" id="uploadedAvatar">
						<div class="button-wrapper">
							<label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
								<span class="d-none d-sm-block"> <s>Upload poto</s> </span>
								<i class="bx bx-upload d-block d-sm-none"></i>
								<input readonly disabled title="Belum bisa dipakai." type="file" id="upload" class="account-file-input" hidden="" accept="image/png, image/jpeg">
							</label>
							<p class="text-muted mb-0">Fitur poto profile belum diterapkan. <del>File haruslah JPG, GIF atau PNG.</del></p>
						</div>
					</div>
				</div>

				<hr class="my-0 px-0"> 
				-->

				<div class="py-4">
					<form id="<?= $form_id ?>">
						<div class="alert alert-info alert-dismissible fade show" role="alert">
							<strong>Tips!</strong> Silahkan lengkapi kolom berikut, Username harus diisi dengan tanpa spasi.
							Password sebaiknya lebih dari 5 karakter dengan 3 syarat berikut: berisi 1 huruf besar 1, huruf kecil 1, 1 angka atau 1 karakter spesial.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>

						<div class="row">
							<div class="mb-3 col-md-6">
								<label for="a_user_name" class="form-label">Username</label>
								<input type="hidden" name="a_user_id" value="" />
								<input type="hidden" name="editMyProfile" value="true" />
								<input class="form-control" type="text" id="a_user_name" name="a_user_name" placeholder="Minimal 5 karakter, hanya huruf dan angka" readonly />
							</div>
							<div class="mb-3 col-md-6">
								<label for="a_display_name" class="form-label">Nama Lengkap</label>
								<input class="form-control" type="text" name="a_display_name" placeholder="Nama lengkap" id="a_display_name" required >
							</div>
							<div class="mb-3 col-md-6">
								<label for="a_gender" class="form-label">Gender</label>
								<select class="form-control" id="a_gender" name="a_gender" required >
									<option value="">Pilih...</option>
									<option value="M">Pria</option>
									<option value="F">Wanita</option>
								</select>
							</div>
							<div class="mb-3 col-md-6">
								<label class="form-label" for="a_handphone">Handphone</label>
								<div class="input-group input-group-merge">
									
									<input type="text" id="a_handphone" name="a_handphone" class="form-control" placeholder="8123456789" required>
								</div>
							</div>
							<div class="mb-3 col-md-12">
								<label for="c_nama" class="form-label">Kelompok</label>
								<input type="text" class="form-control" id="c_nama" placeholder="Kelompok" name="c_nama" readonly />
							</div>
							<div class="mb-3 col-md-12">
								<label for="f_nama" class="form-label">Kantor</label>
								<input type="text" class="form-control" id="f_nama" placeholder="Pilih Kantor" name="f_nama" readonly />
							</div>
							<div class="mb-3 col-md-12">
								<label for="e_nama" class="form-label">Jabatan</label>
								<input type="text" class="form-control" id="e_nama" placeholder="Pilih Jabatan" name="e_nama" readonly />
							</div>
							<div class="mb-4 col-md-12">
								<label for="a_email" class="form-label">E-Mail</label>
								<input type="email" title="Harap isikan dengan format email yang benar" class="form-control" id="a_email" name="a_email" placeholder="E-Mail">
							</div>
							
							<div class="row">
								<div class="mb-3 col ms-3 form-switch">
									<input type="checkbox" class="form-check-input" id="a_help" name="a_help" value="1">
									<label title="Menampilkan kalimat bantuan yang terkadang ada muncul di beberapa menu, berisi petunjuk atau tips, seperti di atas." class="form-check-label" for="a_help">Tampilkan Bantuan / Tips</label>
								</div>
							</div>

							<div class="mb-3 col-md-6">
								<label class="form-label" for="new_password">Tukar Password Baru</label>
								<div class="input-group input-group-merge">
									<span class="input-group-text"><i class="bx bx-key"></i></span>
									<input type="password" id="new_password" name="new_password" class="form-control" placeholder="Isi ini jika ingin menukar password lama..">
								</div>
							</div>
							<div class="mb-3 col-md-6">
								<label class="form-label" for="confirm_password">Ulang Password Baru</label>
								<div class="input-group input-group-merge">
									<span class="input-group-text"><i class="bx bx-key"></i></span>
									<input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ulangi...">
								</div>
							</div>

							<hr class="px-0" />
							<div class="mb-3 col">
								<label class="form-label" for="myPassword">Password Aktif (untuk menyimpan perubahan)</label>
								<div class="input-group input-group-merge">
									<span class="input-group-text"><i class="bx bx-key"></i></span>
									<input type="password" id="myPassword" name="myPassword" class="form-control" placeholder="Ketik password yang aktif sekarang..." required>
								</div>
							</div>

						</div>
					</form>
				</div>