(function () {
	let sedangMenyimpan = false 

	let tableNavigation

	let tableLevel

	let janganSimpan = false

	const muatPilihanNav = () => {
		let level = tableLevel.table.datagrid('getChecked')

		level = level.map(x => x.permission_id)[0]

		let filterRules = [
			{
				field: 'a_permission_id',
				op: 'equal',
				value: level
			}
		];
		
		Ajax.kirim({
			url: 'level/get/navigations',
			data: { filterRules:JSON.stringify(filterRules) },
		}).done((respon) => {
			let rows = respon.rows || [];

			if (rows.length == 0) return

			const dataNavigation = tableNavigation.table
				.datagrid('getRows')
				.map((row, idx) => { return { idx: idx, pageId: row.a_page_id } })
			
			janganSimpan = true

			rows.map(row => row.a_page_id).forEach(pageId => {
				const idxNavigation = dataNavigation.find(x => x.pageId == pageId).idx || false

				if ( idxNavigation ) tableNavigation.table.datagrid('checkRow', idxNavigation )
			})

			janganSimpan = false
		})
	}

	const bersihkanPilihanNav = () => {
		tableNavigation.table.datagrid('clearChecked')

		return true
	}

	const levelSudahTerpilih = () => {
		if (tableLevel.table.datagrid('getSelections').length == 0) {
			new Toast({ text: 'Harap pilih salah satu Kelompok dahulu.', type: 'error' })

			return false
		}

		return true
	}

	const iniNavLevelUmum = (idx, row, table) => {
		if (janganSimpan) return
	
		if (table == 'navigation') {
			if (row.a_private == 0 || row.a_link == 'dashboard') {
				new Toast({ text: 'Dashboard atau navigasi yang tipe public tidak bisa diberi akses khusus.', type: 'error' })

				return true
			}
		} else if (table == 'level') {
			if (row.a_nama == 'Administrator') {
				new Toast({ text: 'Administrator tidak bisa dibatasi hak akses navigasi-nya.', type: 'error' })

				return true
			}
		}
		return false
	}

	const lagiMenyimpan = () => {
		if (sedangMenyimpan) { notif('Masih menyimpan sebelum...'); return true }
		return false
	}

	const notif = (text, type, time) => {
		if (!text) return

		new Toast({
			id: 'saveLevel',
			text: text,
			title: 'Hak Akses Level',
			type: type || 'info',
			timeOut: time || 10000
		})
	}

	const simpanAkses = (nav) => {
		sedangMenyimpan = true

		Ajax.kirim({
			url: 'navigation/put/access',
			data: nav,
			beforeSend: notif('Menyimpan...','info', false)
		}).done(respon => {
			let status = respon.status || false
			let teks = {
				ditambah: 0,
				dihapus: 0
			}

			if (status == 'sukses') {
				if (respon.detail.diTukar > 0) teks.ditambah += respon.detail.diTukar
				if (respon.detail.timpaTidakTerpakai > 0) teks.ditambah += respon.detail.timpaTidakTerpakai
				if (respon.detail.tambahBaru > 0) teks.ditambah += respon.detail.tambahBaru
				if (respon.detail.hapusLama > 0) teks.dihapus += respon.detail.hapusLama

				notif('Data disimpan!', 'success')
			} else {
				notif('Gagal disimpan!', 'danger')
			}

			sedangMenyimpan = false
		})
	}

	let updateData = (tipe, data) => {	
		if (janganSimpan) return
	
		if (lagiMenyimpan()) {
			notif('Sedang menyimpan...', 'info')

			return
		}

		let nav = tableNavigation.table.datagrid('getChecked')
		let level = tableLevel.table.datagrid('getChecked')

		nav = nav.map(x => x.a_page_id)
		level = level.map(x => x.permission_id)


		if (!nav || level.length == 0) { return }

		let akses = {}

		akses[level] = nav

		simpanAkses(akses)
	}

	class TableNavigation {
		checked = []

		constructor() {
			let table = new Datagrid('tblNavigations', { ...Datagrid.options })

			this.table = table.table

			this.table
				.datagrid({
					url: 'navigation/get',
					columns: this.columns,
					height: '500px',
					onClickCell: this.onClickCell,
					idField: 'a_page_id',
					remoteFilter: false,
					remoteSort: false,
					checkOnSelect: false,
					singleSelect: false,
					onBeforeCheck: this.onBefore,
					onBeforeUncheck: this.onBefore,
					onCheck: this.onCheck,
					onUncheck: this.onCheck,

				})

			this.table.datagrid('enableFilter', [
				{ field: 'edit', type: 'label' },
				{ field: 'icon', type: 'label' },
				{ field: 'a_private', type: 'label' },
			])
		}

		onBefore = (idx, row) => {
			if (lagiMenyimpan()) return false;
			if (iniNavLevelUmum(idx, row, 'navigation')) return false;
			if (!levelSudahTerpilih()) return false;
		}

		onCheck = (idx, row) => { updateData('nav', row) }

		columns = [[
			{
				checkbox: true,
				field: 'a_id'
			}, {
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
				field: 'a_private',
				sortable: true,
				align: 'center',
				title: '<i title="Nav ini hanya untuk Level tertentu" class="bx bxs-id-card"></i>',
				width: 5,
				formatter: Datagrid.formatOnOff,
			}
		]]
	}

	class TableLevel {
		constructor() {
			let table = new Datagrid('tblLevels', { ...Datagrid.options })

			this.table = table.table

			this.table
				.datagrid({
					url: 'level/get',
					columns: this.columns,
					height: '500px',
					idField: 'permission_id',
					singleSelect: true,
					checkOnSelect: false,
					onBeforeCheck: this.onBefore,
					// onBeforeUncheck: this.onBefore,
					// onBeforeUncheckAll: this.onBefore,
					onCheck: this.onCheck,
					onUncheck: this.onCheck,
					// onCheckAll: this.onCheck,
					// onUncheckAll: this.onCheck,
				})

			this.table.datagrid('enableFilter', [
				{ field: 'edit', type: 'label' }
			])
		}

		onBefore = (idx, row) => { if (lagiMenyimpan()) return false; if (iniNavLevelUmum(idx, row, 'level')) return false }

		onCheck = (idx, row) => {
			bersihkanPilihanNav();
			muatPilihanNav();
			//updateData('level', row) 
		}

		columns = [[
			{
				checkbox: true,
				field: 'permission_id',
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
			},
		]]
	}

	tableNavigation = new TableNavigation()

	tableLevel = new TableLevel()
})()