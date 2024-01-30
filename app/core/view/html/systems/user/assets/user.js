(function(){
	class User {
		table 

		constructor() {
			let table = new Datagrid('tblUser', {...Datagrid.options})

			this.table = table.table

			this.table
				.datagrid({
					url: 'user/get',
					fitColumns: true,
					columns: this.columns,
					onClickCell: this.onClickCell,
					idField: 'a_user_id'
				})

			this.table.datagrid('enableFilter', [
				{field: 'edit', type: 'label'},
				{field: 'history', type: 'label'},
				{field: 'action2', type: 'label'},
			])
			
			this.table.datagrid('getPager').pagination({
				buttons: [{
					plain: false,
					text: '<button title="Tambah Pengguna baru" class="btn btn-xs btn-primary"><span class="badge text-bg-secondary"> <i class="bi bi-person-add"></i></span>Baru</button>',
					handler: (e) => {
						e.preventDefault()

						this.modalUser('new', 0)
					}
				}]
			})
		}

		columns = [[
			{
				field: 'edit',
				sortable: false,
				title: '<i title="Edit User" class="bi bi-pencil-square"></i>',
				width: '5%',
				align: 'center',
				formatter: Datagrid.formatEdit
			}, {
				field: 'history',
				sortable: true,
				title: '<i title="Riwayat" class="bx bx-pen"></i>',
				width: '5%',
				align: 'center',
				formatter: (val, row) => { return Datagrid.formatCari(row.a_user_name, 'user', 'bx bx-history') },
			}, {
				field: 'a_user_name',
				sortable: true,
				title: 'Username',
				width: '15%'
			}, {
				field: 'a_display_name',
				sortable: true,
				title: 'Fullname',
				width: '15%',
				editor: {
					type: 'validatebox',
					options: {
						required: true,
					}
				}
			}, {
				field: 'a_email',
				sortable: true,
				title: 'Email',
				width: '15%',
				editor: {
					type: 'validatebox',
					options: {
						required: true,
						validType: ['email', 'length[8,50]']
					}
				}
			}, {
				field: 'a_handphone',
				sortable: true,
				title: 'No Handphone',
				width: '15%',
				editor: {
					type: 'validatebox',
					options: {
						required: false,
						delay: 500,
						validType: {
							remote: ['validate/handphone', 'value']
						}
					}
				}
			}, {
				field: 'c_nama',
				sortable: true,
				title: 'Level',
				width: '15%'
			}, {
				field: 'a_active',
				sortable: true,
				align: 'center',
				title: '<i title="Active" class="bx bxs-user-x"></i>',
				width: '5%',
				formatter: Datagrid.formatOnOff,
				editor: {
					type: 'checkbox',
					options: {
						on: 1,
						off: 0
					}
				}
			}, {
				field: 'action2',
				sortable: false,
				align: 'center',
				title: '<i title="Reset Password">Password</i>',
				width: '10%',
				formatter: function () {
					return Datagrid.formatButton(
						'<i class="bx bx-key"></i> Reset',
						'danger');
				}
			}
		]]

		onEndEdit = () => {
			return true;
		}

		onClickCell = (idx, field, value) => {
			if (field == 'edit') this.modalUser('edit', idx)
			if (field == 'action2') this.resetPassword( idx )

			return false;
		}

		resetPassword = (idx) => {
			let user = this.table.datagrid('getRows')[idx];

			App.modalTampil({
				id: 'modResetPassword',
				judul: 'Reset Password Pengguna',
				kaki: `<button type="submit" class="btn btn-primary me-2" form="formResetPassword"> Simpan </button><button class="btn btn-outline-secondary" data-bs-dismiss="modal"> Tutup </button>`,
				icon: 'bx bx-key',
				onShown: function () {
					$('#formResetPassword').form('load', user)

					$('#formResetPassword').submit((e) => {
						e.preventDefault()

						let data = $('#formResetPassword').serialize()
						
						Ajax.kirim({
							url: 'user/putPassword',
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

							new Toast({ id: 'toastUser', text: pesan, timeOut: time, type: tipe });
						})
					})
				},
				badan: `
					<div class="py-4">
						<form id="formResetPassword" autocomplete="off">
							<div class="row">
								<div class="mb-3 col-md-6">
									<label for="a_user_name" class="form-label">Username</label>
									<input type="hidden" name="a_user_id" value="" />
									<input class="form-control" type="text" id="a_user_name" name="a_user_name" placeholder="Minimal 5 karakter, hanya huruf dan angka" readonly />
								</div>
								<div class="mb-3 col-md-6">
									<label for="a_display_name" class="form-label">Nama Lengkap</label>
									<input class="form-control" type="text" id="a_display_name" name="a_display_name" placeholder="Nama Lengkap" readonly />
								</div>
								<hr/>
								<div class="mb-3 col">
									<label for="password" class="form-label">Password Baru</label>
									<input class="form-control" type="text" id="password" name="password" placeholder="Minimal 5 karakter" />
								</div>
						</form>
					</div>`,
				
			})
		}

		modalUser = (tipe, idx) => {
			if (this.sedangEdit) return false

			this.sedangEdit = true
			let table = this.table
			let user = this.table.datagrid('getRows')[idx];

			let myId = 'modEditUser';
			let myJudul = 'Edit Pengguna';
			let myForm = 'formEditUser';
			let myUrl = 'user/put';
			let myReadonly = 'readonly';

			if (tipe == 'new') {
				user = {};
				myId = 'modNewUser';
				myJudul = 'Tambah Pengguna Baru';
				myForm = 'formAddUser';
				myUrl = 'user/post';
				myReadonly = '';
			}

			App.modalTampil({
				id: myId,
				judul: myJudul,
				kaki: `<button type="submit" class="btn btn-primary me-2" form="${myForm}"> Simpan </button><button class="btn btn-outline-secondary" data-bs-dismiss="modal"> Tutup </button>`,
				size: 'lg',
				icon: 'bi bi-person-add',
				scrollable: 1,
				onHidden: () => { this.sedangEdit = false },
				onShown: function () { 
					/**
					 * Submit
					 */
					$('form#' + myForm ).submit( function (e) {
						e.preventDefault()

						let data = $(this).serializeArray()


						/**
						 * Validasi
						 */
						if ($('#' + myForm + ' #permission_id').val() == '') {
							new Toast({ text: "Harap pilih Kelompok-nya.", type: "warning" })

							return false
						}

						if (tipe == 'new') {
							let newPassword = data.find(x => x.name == 'new_password').value
							let confirmPassword = data.find(x => x.name == 'confirm_password').value

							if (newPassword != confirmPassword) {
								new Toast({ id: 'toastProfile', text: 'Kolom Ulang Password harus sama dengan Password Baru.', type: 'error' })

								return false
							}
						}


						/**
						 * Mulai mengirim
						 */
						Ajax.kirim({
							url: myUrl,
							data: data,
						}).done((respon) => {
							let status = respon.status || false
							let pesan = respon.pesan || false
							let tipe = 'success'
							let time = 5000

							if (Array.isArray(pesan)) pesan = pesan.join(' ');
							if (status == 'gagal') {
								tipe = 'danger'
								time = false
							}

							if (tipe == 'success') table.datagrid('reload')
							new Toast({ id: 'toastUser', text: pesan, timeOut: time, type: tipe });
						})
					})


					/**
					 * Muat form dengan data user
					 */
					$('#' + myForm).form('load', user)


					/**
					 * Grup si user
					 */
					/*
					let levelNya = $('#' + myForm + ' #permission_id').combogrid({
						prompt: 'Pilih Kelompok...',
						mode: 'remote',
						url: 'level/get',
						multiple: true,
						delay: 500,
						method: 'post',
						width: '100%',
						height: '38px',
						panelHeight: '250px',
						textField: 'a_nama',
						idField: 'permission_id',
						pageSize: 500,
						pageList: [500,1000],
						selectOnNavigation: false,
						separator: ', ',
						editable: false,
						columns: [
							[{
								field: 'check',
								checkbox: true,
								width: '2%'
							}, {
								field: 'a_nama',
								sortable: true,
								title: 'Level',
								width: '28%'
							}, {
								field: 'a_keterangan',
								sortable: true,
								title: 'Keterangan',
								width: '70%'
							}]
						]
					})*/


					/**
					 * combogrid Kantor
					 */
					let optionLevel = { ...Combogrid.optionRemote }

					optionLevel.url = 'level/get'
					optionLevel.idField = 'permission_id'
					optionLevel.prompt = 'Pilih Kelompok...',
					optionLevel.textField = 'a_nama'
					optionLevel.multiple = true
					optionLevel.delay = 500
					optionLevel.separator = ', ',
					optionLevel.pageSize = 500
					optionLevel.pageList = [500, 1000]
					optionLevel.columns = [
						[{
							field: 'check',
							checkbox: true,
							width: '2%'
						}, {
							field: 'a_nama',
							sortable: true,
							title: 'Level',
							width: '28%'
						}, {
							field: 'a_keterangan',
							sortable: true,
							title: 'Keterangan',
							width: '70%'
						}]
					]

					let levelNya = new Combogrid(`#permission_id`, optionLevel)
						levelNya = levelNya.combogrid




					/**
					 * combogrid Kantor
					 */
					let optionKantor = { ...Combogrid.optionRemote }

					optionKantor.url = 'admin/kantor/get'
					optionKantor.idField = 'a_kantor_id'
					optionKantor.textField = 'a_nama'
					optionKantor.multiple = false
					optionKantor.columns = [[{
						field: 'check',
						checkbox: true,
						width: '2%'
					}, {
						field: 'a_nama',
						sortable: true,
						title: 'Kantor',
						width: '28%'
					}, {
						field: 'a_alamat',
						sortable: true,
						title: 'Alamat',
						width: '70%'
					}]]

					let kantorNya = new Combogrid(`#f_nama`, optionKantor)
					kantorNya = kantorNya.combogrid



					/**
					 * combogrid Jabatan
					 */
					let optionJabatan = { ...Combogrid.optionRemote }

					optionJabatan.url = 'admin/jabatan/get'
					optionJabatan.idField = 'a_jabatan_id'
					optionJabatan.textField = 'a_nama'
					optionJabatan.multiple = false
					optionJabatan.columns = [[{
						field: 'check',
						checkbox: true,
						width: '2%'
					}, {
						field: 'a_nama',
						sortable: true,
						title: 'Jabatan',
						width: '28%'
					}, {
						field: 'a_alamat',
						sortable: true,
						title: 'Keterangan',
						width: '70%'
					}]]

					let jabatanNya = new Combogrid(`#e_nama`, optionJabatan)
					jabatanNya = jabatanNya.combogrid


					/** Kalau edit sesuaikan jabatan, kantor dan kelompok nya yg ada */
					if (tipe == 'edit') {
						let permission_id = user.permission_id || ''
						let kantor_id = user.f_kantor_id || ''
						let jabatan_id = user.e_jabatan_id || ''

						levelNya.combogrid('setValues', permission_id.split(','))
						kantorNya.combogrid('setValues', kantor_id.split(','))
						jabatanNya.combogrid('setValues', jabatan_id.split(','))
					}
					
					/** Jika user baru, kosongkan semua kolom */
					if (tipe == 'new') {
						$('#' + myForm + ' #a_help').prop('checked', true)

						$('#' + myForm + ' #a_active').prop('checked', true)

						$('#' + myForm + ' > .row').append( `
							<hr class="my-3" />
							<div class="mb-3 col-md-6">
								<label class="form-label" for="new_password">Password</label>
								<div class="input-group input-group-merge">
									<span class="input-group-text"><i class="bx bx-key"></i></span>
									<input type="password" id="new_password" name="new_password" class="form-control" placeholder="Set password" required></div>
								</div>
							</div>
							<div class="mb-3 col-md-6">
								<label class="form-label" for="confirm_password">Ulang Password</label>
								<div class="input-group input-group-merge">
									<span class="input-group-text"><i class="bx bx-key"></i></span>
									<input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ulangi..." required>
								</div>
							</div>` )
					}
				},
				badan: `
					<!--div class="py-4">
						<div class="d-flex align-items-start align-items-sm-center gap-4">
							<img src="view/assets/images/dummy_photo.png" alt="user-avatar" class="d-block rounded border border-dark" height="100" width="100" id="uploadedAvatar">
							<div class="button-wrapper">
								<label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
									<span class="d-none d-sm-block"> <s>Upload poto</s> </span>
									<i class="bx bx-upload d-block d-sm-none"></i>
									<input type="file" id="upload" class="account-file-input" hidden="" accept="image/png, image/jpeg">
								</label>
								<p class="text-muted mb-0">Fitur poto profile belum diterapkan. <del>File haruslah JPG, GIF atau PNG.</del></p>
							</div>
						</div>
					</div-->
					<hr class="my-0">
					<div class="py-4">
						<form id="${myForm}">
							<div class="alert alert-info alert-dismissible fade show" role="alert">
								<strong>Tips!</strong> Silahkan lengkapi kolom berikut, Username harus diisi dengan tanpa spasi.
								Password sebaiknya lebih dari 5 karakter dengan 3 syarat berikut: berisi 1 huruf besar 1, huruf kecil 1, 1 angka atau 1 karakter spesial.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>

							<div class="row">
								<div class="mb-3 col-md-6">
									<label for="a_user_name" class="form-label">Username</label>
									<input type="hidden" name="a_user_id" value="" />
									<input class="form-control" type="text" id="a_user_name" name="a_user_name" placeholder="Minimal 5 karakter, hanya huruf dan angka" ${myReadonly} required />
								</div>
								<div class="mb-3 col-md-6">
									<label for="a_display_name" class="form-label">Nama Lengkap</label>
									<input class="form-control" type="text" name="a_display_name" placeholder="Nama lengkap" id="a_display_name" required >
								</div>
								<div class="mb-3 col-md-6">
									<label for="a_gender" class="form-label">Gender</label>
									<select class="form-control" name="a_gender" required >
										<option value="">Pilih...</option>
										<option value="M">Pria</option>
										<option value="F">Wanita</option>
									</select>
								</div>
								<div class="mb-3 col-md-6">
									<label class="form-label" for="a_handphone">Handphone</label>
									<div class="input-group input-group-merge">
										<input type="text" id="a_handphone" name="a_handphone" class="form-control" placeholder="08123456789" required>
									</div>
								</div>
								<div class="mb-3 col-md-12">
									<label for="permission_id" class="form-label">Kelompok</label>
									<input type="hidden" name="c_nama" />
									<input type="text" class="form-control" style="width:100%" id="permission_id" name="permission_id[]" >
								</div>
								<div class="mb-3 col-md-12">
									<label for="f_nama" class="form-label">Kantor</label>
									<input type="text" class="form-control" style="width:100%" id="f_nama" placeholder="Kantor" name="f_nama" />
								</div>
								<div class="mb-3 col-md-12">
									<label for="e_nama" class="form-label">Jabatan</label>
									<input type="text" class="form-control" style="width:100%" id="e_nama" placeholder="Jabatan" name="e_nama" />
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
									<div class="mb-3 col ms-3 form-switch">
										<input type="checkbox" class="form-check-input" id="a_active" name="a_active" value="1">
										<label class="form-check-label" for="a_active">Active</label>
									</div>
								</div>

								<hr/>
								<div class="row">
									<div class="mb-3 col">
									<label class="form-label" for="myPassword">Password Anda (untuk menyimpan perubahan)</label>
									<div class="input-group input-group-merge">
										<span class="input-group-text"><i class="bx bx-key"></i></span>
										<input type="password" id="myPassword" name="myPassword" class="form-control" placeholder="Ketik password yang aktif sekarang..." required>
									</div>
								</div>
							</div>

							</div>
						</form>
					</div>
				`,
			})
		}
	}

	let tblUser = new User()

	$('#btnNewUser').click(function (e) {
		e.preventDefault()

		tblUser.modalUser('new', 0)
	})
})()