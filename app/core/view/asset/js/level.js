(function () {
	class Level {
		table 

		constructor() {
			let table = new Datagrid('tblLevel', { ...Datagrid.options })

			this.table = table.table

			this.table
				.datagrid({
					url: 'level/get',
					columns: this.columns,
					onClickCell: this.onClickCell,
					idField: 'permission_id'
				})

			this.table.datagrid('enableFilter', [
				{field:'edit', type: 'label'}
			])
		}

		columns = [[
			{
				field: 'edit',
				sortable: false,
				title: '<i title="Edit User" class="bi bi-pencil-square"></i>',
				width: 5,
				align: 'center',
				formatter: Datagrid.formatEdit
			}, {
				field: 'a_nama',
				sortable: true,
				title: 'Level',
				width: 20
			}, {
				field: 'a_keterangan',
				sortable: true,
				title: 'Keterangan',
				width: 40,
			}, {
				field: 'a_kunci_nama',
				sortable: true,
				align: 'center',
				title: '<i title="Kunci" class="bi bi-check"></i>',
				width: '5%',
				formatter: Datagrid.formatOnOff,
				editor: {
					type: 'checkbox',
					options: {
						on: 1,
						off: 0
					}
				}
			},
		]]

		onClickCell = (idx, field, value) => {
			if (field == 'edit') this.editLevel(idx)

			return false;
		}

		editLevel = (idx) => {
			let level = this.table.datagrid('getRows')[idx];

			App.modalTampil({
				id: 'modEditLevel',
				judul: 'Edit Kelompok',
				kaki: `<button type="submit" class="btn btn-primary me-2" form="formEditLevel">Perbarui</button><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>`,
				size: 'lg',
				icon: 'bx bx-group',
				scrollable: 1,
				onShown: function () {
					/**
					 * Submit
					 */
					$('form#formEditLevel').submit(function (e) {
						e.preventDefault()

						let data = $(this).serializeArray()

						Ajax.kirim({
							url: 'level/put',
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

							new Toast({ id: 'toastLevel', text: pesan, timeOut: time, type: tipe });
						})
					})

					/**
					 * Muat form dengan data
					 */
					$('#formEditLevel').form('load', level)
					
					if (level['builtin'] == 1) {
						$('#formEditLevel #a_kunci_nama')
							.prop('readonly', true)
							.prop('disabled', true)
					}
				},
				badan: `
					<div class="py-4">
						<form id="formEditLevel">
							<div class="mb-3">
								<label for="a_nama" class="form-label">Kelompok</label>
								<input type="hidden" name="permission_id" value="" />
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
					</div>
				`,
			})
		}
	}

	let tblLevel = new Level()
})()