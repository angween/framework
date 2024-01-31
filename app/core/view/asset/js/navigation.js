(function () {
	class Navigation {
		constructor() {
			let table = new Datagrid('tblNavigation', { ...Datagrid.options })

			this.table = table.table

			this.table
				.datagrid({
					url: 'navigation/get',
					columns: this.columns,
					onClickCell: this.onClickCell,
					idField: 'a_page_id',
					remoteFilter: false,
					remoteSort: false
				})

			this.table.datagrid('enableFilter', [
				{field:'edit', type:'label'},
				{field:'icon', type:'label'},
				{field:'a_private', type:'label'},
			])
		}

		columns = [[
			{
				field: 'c_name',
				sortable: true,
				title: 'Menu Section',
				width: 10,
			}, {
				field: 'b_name',
				sortable: true,
				title: 'Menu Grup',
				width: 10,
			}, {
				field: 'a_page',
				sortable: true,
				title: 'Nama Navigasi',
				width: 20
			}, {
				field: 'a_link',
				sortable: true,
				title: 'Link',
				width: 10,
			}, {
				field: 'a_urutan',
				sortable: true,
				title: 'Urutan',
				align: 'right',
				width: 5,
			}, {
				field: 'icon',
				sortable: true,
				title: 'Icon',
				align: 'center',
				width: 5,
				formatter: Datagrid.formatIcon
			}, {
				field: 'a_private',
				sortable: true,
				align: 'center',
				title: '<i title="Nav ini hanya untuk Level tertentu" class="bx bxs-id-card"></i>',
				width: 5,
				formatter: Datagrid.formatOnOff,
			},
		]]

		onClickCell = (idx, field, value) => {
			if (field == 'edit') this.editNavigation(idx)

			return false;
		}

		editNavigation = (idx) => {
			let level = this.table.datagrid('getRows')[idx];

			App.modalTampil({
				id: 'modEditNavigation',
				judul: 'Edit Kelompok',
				kaki: `<button type="submit" class="btn btn-primary me-2" form="formEditNavigation">Perbarui</button><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>`,
				size: 'lg',
				icon: 'bx bx-group',
				scrollable: 1,
				onShown: function () {
					/**
					 * Submit
					 */
					$('form#formEditNavigation').submit(function (e) {
						e.preventDefault()

						let data = $(this).serializeArray()

						Ajax.kirim({
							url: 'navigation/put',
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

							new Toast({ id: 'toastNavigation', text: pesan, timeOut: time, type: tipe });
						})
					})

					/**
					 * Muat form dengan data
					 */
					$('#formEditNavigation').form('load', level)

					if (level['builtin'] == 1) {
						$('#formEditNavigation #a_kunci_nama')
							.prop('readonly', true)
							.prop('disabled', true)
					}
				},
				badan: `
					<div class="py-4">
						<form id="formEditNavigation">
							<div class="mb-3">
								<label for="a_nama" class="form-label">Kelompok</label>
								<input type="hidden" name="a_user_id" value="" />
								<input class="form-control" type="text" id="a_nama" name="a_nama" readonly>
							</div>
							<div class="mb-3">
								<label for="a_keterangan" class="form-label">Keterangan</label>
								<input class="form-control" type="text" name="a_keterangan" id="a_keterangan">
							</div>
							<div class="mb-3 col ms-3 form-check">
								<input type="checkbox" class="form-check-input" id="a_kunci_nama" name="a_kunci_nama" value="1">
								<label class="form-check-label" for="a_kunci_nama">Kunci Kelompok - untuk tidak bisa dirubah atau terhapus.</label>
							</div>
						</form>
					</div>`,
			})
		}
	}

	let tabelNavigation = new Navigation()
})()