
const Navigasi = (function () {
	const _tabNav = '#targetIsi'
	let _assetsLoaded = false
	let _firstLoad = true
	let _tabs = []
	const daftarJunkDOM = [
		'.panel.combo-p.panel-htop',
		'.flatpickr-calendar.animate'
	]

	/**
	 * Triggers
	 */
	// Link dari Menu Utama kiri
	$('#layout-menu ul.menu-inner a').click(async function (e) {
		e.preventDefault()

		/** jika dia klik Induk dari SubMenu */
		if ($(this).hasClass('menu-toggle')) {
			return
		}

		// $(this).parent().addClass('active')

		let data = $(this).find('div').data()

		let _link = $(this).attr('href')

		let teks = $(this).text().trim()

		let _title = data.section + ' / ' + teks

		if (data.section == ' ' || data.section == '' ) _title = teks
		
		if (_link == 'javascript:;') return

		/** jika asset belum dimuat */
		if (!_assetsLoaded) {
			$.ajaxSetup({cache: true})

			await muatAssets().then(respon => {
				_assetsLoaded = true
			})

			$.ajaxSetup({ cache: false })
		}

		if (!cekHalamanIsLoaded(_title, _link)) return

		/** ambil isi halaman */
		tarikHalaman( _link, _title )
	})

	const cekHalamanIsLoaded = function (title, link) {
		/** Cek apa Tab halaman ini sudah dimuat */
		if (_tabs.indexOf(title + link) >= 0) {
			$(_tabNav).tabs('select', title)

			return false
		}

		return true
	}

	const tarikHalaman = function (link, title, kunci) {
		let halaman = Ajax.kirim({
			url: link,
			data: { fromNav: true },
			method: 'POST',
			contentType: 'application/json',
			dataType: 'html'
		})

		halaman
			.then((body, status, cc) => {
				let contentType = cc.getResponseHeader('content-type');

				muatHalaman(contentType, body, link, title, kunci)
			})
			.fail((a, b, pesan) => {
				let laporan = a.responseJSON || a.responseText

				if (pesan == 'Not Found') {
					laporan = 'Halaman tidak ditemukan!'
				}

				if (a.responseJSON) {
					laporan = a.responseJSON

					if (laporan.pesan) laporan = laporan.pesan
				}

				if (!laporan) {
					console.error('*** Tidak ada respon!');

					laporan = 'Module Not Found!';
				}

				new Toast({
					text: laporan,
					title: 'Navigasi: ' + link,
					type: 'error',
					timeOut: false
				})
			})

	}

	const muatHalaman = function (contentType, body, link, title, kunci) {
		if (contentType.indexOf('text/html') < 0) {
			new Toast({
				text: 'Batal! Halaman dimuat tidak dalam format diinginkan!',
				title: 'Navigasi: ' + link,
				type: 'error',
				timeOut: false
			})
		}

		_daftarkanTab(title, link)

		_muatHalaman( body, title, kunci )
	}

	const _daftarkanTab = function (title, link) {
		_tabs.push( title + link )
	}

	const _muatHalaman = function( body, title, lock ) {
        /** Daftarkan Tab supaya tidak dipanggil ulang */
		lock = lock !== false ? true : lock

		// _tabs.push(title)

		let id = '__tab' + title.replaceAll(' ', '')

		/** Muat isi halaman baru */
		$(_tabNav).tabs('add', {
			title: title,
			id: id,
			content: body,
			closable: lock
		});

		// new PerfectScrollbar(
		// 	$('#' + id).get(0),
		// 	{
		// 		wheelPropagation: false,
		// 		suppressScrollY: false
		// 	}
		// );
	}

	const muatAssets = async function () {
		/** Muat CSS ekstra */
		$('head').append(
			$('<link rel="stylesheet" type="text/css" />').attr('href', 'view/assets/libs/easyui/themes/icon.css')
		)

		$('head').append(
			$('<link rel="stylesheet" type="text/css" />').attr('href', 'view/assets/libs/dropzone6/dropzone.css')
		)

		/** Muat JS ekstra */
		let files = [
			"view/assets/libs/dropzone6/dropzone-min.js",
			// "view/assets/libs/easyui/old-datagrid-filter.js",
			"view/assets/libs/easyui/datagrid-export.js",
			"view/assets/libs/easyui/datagrid-detailview.js",
			"view/assets/libs/easyui/datagrid-cellediting.js",
		]

		files.forEach( async (file) => {
			await _muatAssets(file)
		})

		return 'done'
	}

	const _muatAssets = function(file) {
		return new Promise( resolve => {
			$.getScript(file)

			resolve();
		})
	}

	/**
	 * Efek warna untuk menu sedang aktif
	 * @param {string} title
	 * @param {int} index
	 */
	const menuActive = (title, index) => {
		$('.menu-inner .active').removeClass('active')

		$(`.menu-inner .menu-item div[data-idx="${title}"]`).closest('.menu-item').addClass('active')
	}

	
	/**
	 * Inisialisasi awal
	 */
	const initial = function () {
		/** Bersihkan Tab */
		$(_tabNav).empty()

		/** Siapkan TABNAV */
		$(_tabNav).tabs({
			border: false,
			plain: true,
			//fit: true,
			tabHeight: 42,
			onSelect: menuActive,
			onClose: (title, index) => {
				_tabs.splice(index, 1)

				/** Hapus sisa-sisa panel */
				daftarJunkDOM.forEach(elm => {
					// let junks = document.querySelectorAll('.panel.combo-p.panel-htop')
					let junks = document.querySelectorAll(elm)
	
					if ( junks.length > 0 ) junks.forEach( x => x.remove() )					
				})
			}
		});

		/** Isi dengan Dashboard */
		if (_firstLoad) {
			/** Resize si Tabs */
			window.addEventListener("resize", () => {
				$(_tabNav).tabs('resize')
			});

			_firstLoad = false
            let kunci = false

            tarikHalaman('dashboard', 'Dashboard', kunci)
		}
	}

	initial()
})()