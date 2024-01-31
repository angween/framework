/*********************************
 * Semua Definisi Global Classes
 *********************************/

/**
 * Global VARs
 */
let isSearching = false
let toastLast = []


/***
 * Huruf besarkan karakter pertama
 * @param {string} "kalimat panjang".capitalize()
 */
Object.defineProperty(String.prototype, 'capitalize', {
	value: function () {
		return this.charAt(0).toUpperCase() + this.slice(1);
	},
	enumerable: false
});

/**
 * Format angka jadi duit Indonesia
 * Cara pakai: DUIT.format( value )
 */
const DUIT = new Intl.NumberFormat(
	'id-ID',
	{
		style: 'currency',
		currency: 'IDR',
		minimumFractionDigits: 0,
		maximumFractionDigits: 0
	}
)


/**
 * FORM HANDLER
 */
class Ajax {
	/**
	 * Tarik Ajax dari server
	 * @toast {object} Toast
	 * @url {string} sumber
	 * @type {string} 'post'
	 * @data {object}
	 * @contentType {string}
	 * @dataType {string} json|html
	 * @beforeSend {function}
	 * @return array
	 */
	static kirim(params) {
		if (params.toast) {
			new Toast({
				id: params.toast.id || 'RLAnotif',
				text: params.toast.text || '',
				type: params.toast.type || 'info',
				timeOut: params.toast.time || 5000
			})
		}

		return $.ajax({
			url: params.url,
			type: params.method || 'POST',
			data: params.data || {},
			contentType: params.contentType || 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: params.dataType || 'json',
			beforeSend: params.beforeSend || null
		})
	}

	static kirimDenganFile(params) {
		return $.ajax({
			url: params.url,
			type: params.method || 'POST',
			data: params.data || {},
			dataType: params.dataType || 'json',
			beforeSend: params.beforeSend || null,
			contentType: false,
			processData: false,
		})
	}

	static fetch = (params) => {
		if (params.toast) {
			new Toast({
				id: params.toast.id || 'RLAnotif',
				text: params.toast.text || '',
				type: params.toast.type || 'info',
				timeOut: params.toast.time || 5000
			})
		}

		let url = params.url
		let method = params.method || 'POST'
		let contentType = params.contentType || 'application/json; charset=UTF-8'
		let header = new Headers({
			'Content-Type': contentType,
			'Accept' : contentType,
			'Accept-Language': 'id-ID'
		})

		let formData = {}
		
		for (let [key, value] of params.body.entries()) {
			formData[key] = value;
		}

		try {
			fetch(url, {
				method: method,
				headers: header,
				body: JSON.stringify(formData)
			}).then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok.')
				}

				return response.json()
			})
		} catch (error) {
			console.error('Error:', error)
		}
	}
}

class Formulir {
	_polaFeedback = `<div class="invalid-feedback">#TITLE#</div >`

	constructor(params) {
		this.target = params.target

		this.params = params

		this._initForm()
	}

	_initForm = () => {
		if (!$(this.target).hasClass('needs-validation')) $(this.target).addClass('needs-validation')

		let form = this

		$(this.target + ' input').each((x, elm) => {
			let required = $(elm).prop('required')

			if (required) {
				let title = $(elm).prop('title')

				if (!title) title = 'Harus diisi.'

				let elmNew = form._polaFeedback.replace('#TITLE#', title)

				$(elmNew).insertAfter(elm)
			}
		})
	}

	/**
	 * Merubah form data yg <name='x' value='y'> jadi {name:value}
	 * @name {string} nama elemen
	 * @value {string} value nya
	 */
	static formDataToObj = (objek) => {
		let hasil = {}

		for (let i = 0; i < objek.length; i++) {
			hasil[objek[i]['name']] = objek[i]['value']
		}

		return hasil
	}

	static serializeForm(form) {
		const formData = new FormData(form);
		const serializedData = {}

		formData.forEach((value, key) => {
			if (serializedData[key]) {
				if (!Array.isArray(serializedData[key])) {
					serializedData[key] = [serializedData[key]];
				}

				serializedData[key].push(value)
			} else {
				serializedData[key] = value
			}
		})

		return serializedData;
	}

}

/**
 * Notifikasi Bootstrap Toast
 * @id {string}
 * @title {string} judul dalam toast
 * @text {string} pesan
 * @type {string} ['error','info','warning']
 * @timeOut {int} milli-secs
 */

class Toast {
	container = '#toast-container'

	targetUpdate

	toastIsUpdate = false

	// timer = undefined
	
	timeOutExist

	position

	constructor(params) {
		/**
		 * Pesan toast masih sama dgn sebelum?
		 * Atau memiliki ID yg sama?
		 * Beri animasi
		 */

		// toast baru
		if ( toastLast.length == 0 && params.id ) toastLast.push(params)

		// toast masuk ada id nya
		else if ( typeof(params.id) != 'undefined') {
			// cek apa id toast sudah ada
			if (toastLast.findIndex(x => x.id == params.id) >= 0) {
				this.toastNeedUpdate(params)

				/** update teks */
				if (toastLast.find(x => x.id == params.id).text != params.text) {
					this.toastUpdateText(params)
				}

				/** Update type */
				if (toastLast.find(x => x.id == params.id).type != params.type) {
					this.toastUpdateType(params)
				}

				/** Update timer */
				if (params.timeOut > 1000) {
					this.toastUpdateTime(params.id, params.timeOut)
				}

				/** Update data id yg sudah ada */
				this.updateExisting( params )

				/** Tambahkan animasi */
				this.toastExist( params.id )

				return
			}
		}
		else if (toastLast.findIndex( x => x.text ).text != params.text) { toastLast.push(params) }
		else if (toastLast.text == params.text) { return }

		/**
		 * Pastikan value untuk ID
		 */
		let id = new Date().toLocaleTimeString(undefined, { hour12: false })
		if (typeof (params.id) == 'undefined') { this.id = "t" + id.replaceAll(":", "") }
		else { this.id = params.id }

		/**
		 * Inisialisasi varibel lokal
		 */
		this.type = params.type || 'primary'
		this.jam = params.jam || id
		this.timeOut = params.timeOut || 5000
		this.text = params.text || '...'
		this.itemPosition = this.toastItemPosisi['topRight']

		if (params.title) this.title = params.title
		else {
			if (this.type == 'danger') this.title = 'Gagal'
			else if (this.type == 'warning') this.title = 'Perhatian'
			else if (this.type == 'success') this.title = 'Berhasil'
			else if (this.type == 'primary') this.title = 'Info'
			else this.title = 'Info'
		}

		/**
		 * Posisi Toast perlu diubah?
		 */
		if (params.position) {
			this.position = params.position

			$(this.container)
				.removeClass(Object.values(this.toastPosisi))
				.addClass(this.toastPosisi[this.position])

			this.itemPosition = this.toastItemPosisi[this.position]
		}

		/**
		 * Inisialisasi animasi
		 */
		if (params.timeOut === false) this.timeOut = false

		if (params.animIn) this.myAnimIn = params.animIn
		else this.myAnimIn = this.animIn[Math.floor(Math.random() * this.animIn.length)];

		if (params.animOut) this.myAnimOut = params.animOut
		else this.myAnimOut = this.animOut[Math.floor(Math.random() * this.animOut.length)];


		/**
		 * Mulai toast
		 */
		this.toastElm()
	}

	toastType = {
		primary: "bg-primary",
		secondary: "bg-secondary",
		success: "bg-success",
		danger: "bg-danger text-bg-dark",
		error: "bg-danger text-bg-dark",
		warning: "bg-warning text-bg-dark",
		info: "bg-info",
		dark: "bg-dark text-bg-dark",
	}

	toastPosisi = {
		topLeft: "top-0 start-0",
		topCenter: "top-0 start-50 translate-middle-x",
		topRight: "top-0 end-0",
		middleLeft: "top-50 start-0 translate-middle-y",
		center: "top-50 start-50 translate-middle",
		middleRight: "top-50 end-0 translate-middle-y",
		bottomLeft: "bottom-0 start-0",
		bottomMiddle: "bottom-0 start-50 translate-middle-x",
		bottomRight: "bottom-0 end-0"
	}

	toastItemPosisi = {
		topLeft: "me-auto",
		topCenter: "",
		topRight: "ms-auto",
		middleLeft: "me-auto",
		center: "",
		middleRight: "ms-auto",
		bottomLeft: "me-auto",
		bottomMiddle: "",
		bottomRight: "ms-auto"
	}

	toastTemplate = `
		<div id="#ID#" class="bs-toast toast #TIPE# #MARGIN#" role="alert" aria-live="assertive" aria-atomic="true" style="position:relative" #AUTOHIDE# #DELAY#>
			<div class="toast-header">
				<a href="#" class="stretched-link text-white" data-bs-dismiss="toast"><i class="bi bi-bell me-2"></i></a>
				<div class="me-auto fw-semibold">#TOASTJUDUL#</div>
				<small>#TOASTJAM#</small>
				<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>
			<div class="toast-body">#TOASTPESAN#</div>
		</div>`

	animOut = ["animate__flipOutX", "animate__rotateOutUpRight",
		"animate__flipOutY", "animate__fadeOutUp", "animate__bounceOutUp",
		"animate__bounceOutRight", "animate__backOutUp"]

	animExist = ["animate__headShake", "animate__heartBeat", "animate__jello", "animate__swing", "animate__shakeX", "animate__rubberBand"]

	animIn = ["animate__bounceIn", "animate__bounceInRight", "animate__zoomIn",
		"animate__lightSpeedInRight", "animate__flipInX",
		"animate__flipInY", "animate__backInDown", "animate__backInRight"]

	toastExist = (id) => {
		if (!id) return

		let myAnimExist = this.animExist[Math.floor(Math.random() * this.animExist.length)]

		$('#' + id).addClass(myAnimExist)
	}

	updateExisting = (params) => {
		let idx = toastLast.findIndex(x => x.id == params.id)
		
		toastLast[idx].text = params.text
		toastLast[idx].type = params.type
	}

	toastNeedUpdate = (params) => {
		let target = '#toast-container #' + params.id

		if ($(target).length != 1) return;

		this.targetUpdate = target
		this.toastIsUpdate = true

		return
	}

	toastUpdateText = (params) => {
		$(this.targetUpdate + ' .toast-header > div').text(params.title)
		$(this.targetUpdate + ' .toast-body').html(params.text)
	}

	toastUpdateType = (params) => {
		$(this.targetUpdate)
			.removeClass(Object.values(this.toastType))
			.addClass(this.toastType[params.type])
	}

	toastUpdateTime = (id, time) => {
		let $targetToast = $(`#toast-container #${id}`) 

		if (time !== false) {			
			$targetToast
				.attr('data-bs-autohide', true)
				.attr('data-bs-delay', time)
			
			const $_toast = new bootstrap.Toast($targetToast)
			
			clearTimeout( this.timeOutExist )

			this.timeOutExist = undefined
			
			this.timeOutExist = setTimeout(() => {
				$_toast.hide()
			}, time);
		}
	}

	toastElm = (params) => {
		let toastBaru = this.toastTemplate
			.replace('#ID#', this.id)
			.replace('#TOASTJUDUL#', this.title)
			.replace('#TIPE#', this.toastType[this.type])
			.replace('#TOASTJAM#', this.jam)
			.replace('#TOASTPESAN#', this.text)
			.replace('#MARGIN#', this.itemPosition)

		if (this.timeOut === false) {
			toastBaru = toastBaru
				.replace('#AUTOHIDE#', 'data-bs-autohide="false"')
				.replace('#DELAY#', '')
		} else {
			toastBaru = toastBaru
				.replace('#AUTOHIDE#', 'data-bs-autohide="true"')
				.replace('#DELAY#', `data-bs-delay="${this.timeOut}"`)
		}

		$(this.container).append(toastBaru)

		$('#' + this.id).toast('show')

		$('#' + this.id).addClass(`animate__animated ${this.myAnimIn}`)

		$('#' + this.id).on('hide.bs.toast', () => {
			// beri animasi keluar
			$('#' + this.id).addClass(this.myAnimOut)

			// this.timer = setTimeout(() => {
			this.timeOutExist = setTimeout(() => {
				$('#' + this.id).remove()

				toastLast = []
			}, 500)

			return false;
		})

		// remove animasi masuk
		setTimeout(() => { $('#' + this.id).removeClass(this.myAnimIn) }, 3000)
	}


	static tutupById = (id) => {
		let $targetToast = $(`#toast-container #${id}`)

		if ($targetToast.length == 0) return

		let myAnimOut = "animate__flipOutX"

		$(`#toast-container #${id}`).addClass(`animate__animated ${myAnimOut}`)

		setTimeout(() => {
			$(`#${id}`).remove()
		}, 1500)
	}



	static tutup = (id) => {
		let $targetToast
		let myAnimOut
		let myId

		if (id == 'all') {
			$('#toast-container > div').each((i, x) => {
				myId = $(x).prop('id')

				this.tutupById(myId)
			})
		} else {
			this.tutupById(id)
		}
	}
}

const Terbilang = class {
	isMinus = false

	minus = ''

	ones = ['', 'SATU', 'DUA', 'TIGA', 'EMPAT', 'LIMA', 'ENAM', 'TUJUH', 'DELAPAN', 'SEMBILAN', 'SEPULUH']

	tens = ['', 'SEPULUH', 'DUA PULUH', 'TIGA PULUH', 'EMPAT PULUH', 'LIMA PULUH', 'ENAM PULUH', 'TUJUH PULUH', 'DELAPAN PULUH', 'SEMBILAN PULUH']

	convert_billions(num) {
		if (num >= 1000000000) return this.convert_billions(Math.floor(num / 1000000000)) + " MILYAR " + this.convert_millions(num % 1000000000);
		else return this.convert_millions(num);
	}

	convert_millions(num) {
		if (num >= 1000000) return this.convert_millions(Math.floor(num / 1000000)) + " JUTA " + this.convert_thousands(num % 1000000);
		else return this.convert_thousands(num);
	}

	convert_thousands(num) {
		if (num >= 1000) return this.convert_hundreds(Math.floor(num / 1000)) + " RIBU " + this.convert_hundreds(num % 1000);
		else return this.convert_hundreds(num);
	}

	convert_hundreds(num) {
		if (num > 99) return this.ones[Math.floor(num / 100)] + " RATUS " + this.convert_tens(num % 100);
		else return this.convert_tens(num);
	}

	convert_tens(num) {
		if (num <= 10) return this.ones[num];
		else if (num > 10 && num < 20) return this.ones[Math.floor(num % 10)] + ' BELAS';
		else return this.tens[Math.floor(num / 10)] + " " + this.ones[num % 10];
	}

	constructor(num) {
		if (num == 0) return "-";
		else {
			if (num < 0) {
				num = Math.abs(num);
				this.isMinus = true;
				this.minus = 'MINUS ';
			}
			this.awal = num

			num = Math.floor(num)

			this.koma = (this.awal - num) * 100
			this.koma = this.convert_tens(this.koma)

			if (this.koma != '') this.koma = ' KOMA ' + this.koma

			// this.respon = this.convert_millions(num)
			this.respon = this.convert_billions(num)
			this.respon = this.respon.replace('SATU PULUH', 'SEPULUH').replace('SATU BELAS', 'SEBELAS').replace('SATU RATUS', 'SERATUS'); //.replace('SATU RIBU', 'SERIBU');
			this.responDgnKoma = this.minus + this.respon + this.koma;
		}
	}

	valueOf() { return this.respon; }
}


const Cari = class {
	lagiNyari = false

	idNotif = '_cari'

	constructor(cari) {
		if (!cari) return;

		if (this.lagiNyari) {
			this.showNotyCari('Pencarian sebelumnya masih aktif.', 'warning', 3000)

			return;
		}

		if (cari.length < 3) this.showNotyCari('Kata pencarian terlalu pendek', 'info', 3000)

		this.cari = cari.toLowerCase();

		this.mulaiNyari();
	}

	mulaiNyari = () => {
		this.lagiNyari = true

		App.modalTampil({
			// theme: 'card',
			id: 'modalCari',
			judul: 'Pencarian',
			badan: '<div id="containerHasilCari" style="min-height:20rem"></div>',
			kaki: '',
			size: 'lg',
			fullscreen: '',
			scrollable: 1,
			onHidden: () => { this.lagiNyari = false },
			onShown: () => {
				Ajax.kirim({
					url: 'cari',
					data: JSON.stringify({ cari: this.cari }),
					contentType: 'application/json',
					dataType: 'html',
					toast: { text: 'Mencari...', id: this.idNotif, type: 'info', time: false }
				}).done((respon, bb, cc) => {
					let contentType = cc.getResponseHeader('content-type')
					let type = 'succes'
					let time = 3000

					if (contentType.indexOf('text') >= 0 ) {
						this.tampilkanHasil(respon)
					} else if (contentType.indexOf('json') >= 0 ) {
						if (typeof (respon) == 'string') {
							respon = JSON.parse( respon )
						}

						if (respon.status == 'gagal') {
							type = 'danger'
							time = false
						}

						this.showNotyCari(respon.pesan, type, time)

						this.tampilkanHasil( `
							<div class="position-relative">
								<div class="position-absolute top-50 start-50 translate-middle">
									${respon.pesan}
								</div>
							</div>`)
					}
				})
			},
		})
	}

	tampilkanHasil = (hasil) => {
		hasil = hasil.replaceAll(
			'<dd class="col-sm-9">1</dd>',
			'<dd class="col-sm-9"><span class="bi bi-checkbox-checked text-primary fs-4"></span></dd>'
		)

		$('#containerHasilCari').html(hasil);
	}


	// notif pencarian
	showNotyCari = (text, type, timeout) => {
		new Toast({ id: this.idNotif, text: text, timeOut: timeout, type: type });
	}
}

class App {
	myTips

	/**
	 * Tampilkan bootstrap dialog
	 * @id {string}
	 * @judul {string}
	 * @badan {string} html
	 * @scrollable {int} 1, 0
	 * @static {int} 1, 0
	 * @size {string} sm, *md*, lg, xl
	 * @kaki {string} html
	 * @padding {css-class} padding modal-body
	 * @onShown {function} method
	 * @onHidden {function} method
	 * @returns void
	 */
	static modalTampil(param) {
		if (typeof (param) != 'object') return false;

		const container = '#modal-container'

		let id = param.id || 'iniModal',
			border = param.border === undefined ? '' : 'border',
			borderFooter = param.borderFooter === undefined ? '' : param.borderFooter,
			theme = param.theme || 'default',
			judul = param.judul || 'Jendela Dialog',
			badan = param.badan || '<p>-Memuat isi-</p>',
			kaki = param.kaki || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>',
			size = param.size || 'md',
			icon = param.icon || 'fa-solid fa-magnifying-glass',
			scrollable = param.scrollable === undefined ? 0 : param.scrollable,
			fullscreen = param.fullscreen === undefined ? 0 : 1,
			statik = param.statik === undefined ? 0 : param.statik,
			padding = param.padding === undefined ? '3' : param.padding,
			centered = param.centered === undefined ? undefined : param.centered,
			onShow = param.onShow || Function(),
			onShown = param.onShown || Function(),
			onHidden = param.onHidden || Function(),
			onHide = param.onHide || Function(),
			removeOnHidden = param.removeOnHidden === undefined ? true : param.removeOnHidden;

		if ($(container).length != 1) {
			new Toast({ id: 'error', text: '*** Modal Container not found!', timeOut: false, type: 'error' });

			return false;
		}

		// new Toast({ id: 'modalTampil', title: 'Dialog', text: 'Menampilkan dialog...', timeOut: false, type: 'info' });

		$(container).load(
			'view/html/systems/modal.php',
			{
				id: id, theme: theme, st: statik, sz: size, fullscreen: fullscreen,
				scrollable: scrollable, icon: icon, border: border, padding: padding,
				centered: centered, borderFooter: borderFooter
			},
			function () {
				/** On Show Modal */
				$('#' + id).on('show.bs.modal', function (e) {
					if (theme == 'default') {
						$('#' + id + ' .modal-title').html(judul)
						// $('#' + id + ' .modal-title').html(cari)
						$('#' + id + ' div.modal-footer').html(kaki)
						$('#' + id + ' div.modal-body').html(badan)
					} else if (theme == 'search') {
						// $('#' + id + ' .modal-title').html(judul)
						$('#' + id + ' div.modal-content').html(badan)
						$('#' + id + ' div.modal-footer').html(kaki)
					}
				})

				/** On Shown Modal */
				$('#' + id).on('shown.bs.modal', function (e) {
					if (theme == 'default') new PerfectScrollbar($('.modal-body').get(0), { wheelPropagation: false })
	
					onShown();
				})
	
				/** On Hide Modal */
				$('#' + id).on('hide.bs.modal', function (e) { onHide() })

				// $('#' + id).on('show.bs.modal', function (e) { onShow() })

				/** On Hidden Modal */
				$('#' + id).on('hidden.bs.modal', function (e) {
					onHidden();

					if (removeOnHidden) $(container).empty()
				})

				/** Tampilkan Modal */
				$('#' + id).modal('show')
		})

		/* Ada error sewaktu set perfectscrollbar pada tiap body modal */
		// new PerfectScrollbar( $('.modal-body').get(0), { wheelPropagation: false })
	}

	static tabLoad(param) {
		if (!param.target || !param.link) return false

		const data = param.data || '';

		$(param.target)
			.empty()
			.load(param.link, { param: data }, function (response, status, xhr) {
				if (status == "error") {
					const depan = response.substring(0, 1)

					if ( depan == '{' || depan == '[' ) {
						response = JSON.parse(response)
						const pesan = " / " + response['pesan'] || ''
						const teks = 'Error ' + xhr.status + ": " + xhr.statusText + pesan

						new Toast({
							text: teks,
							title: param.link,
							type: 'error',
							timeOut: false
						})
					} else if ( depan == '<' ) {
						new Toast({
							text: 'Halaman tidak ditemukan',
							title: param.link,
							type: 'error',
							timeOut: false
						})
					}
				}
		})
	}

	static cari(param) {
		const cari = new Cari(param)
	}

	static randomChar = (n) => {
		n = n || 6

		if (n <= 2) n = 6

		return Math.random().toString(36).substring(2, n)
	}

	static toast(text, type, timeOut, id) {
		id = id || 'toastId'
		text = text 
		type = type || 'info'
		timeOut = timeOut === undefined ? 5000 : timeOut

		new Toast({
			id: id,
			text: text,
			type: type,
			timeOut: timeOut
		})
	}

	static linkDownloadFile = (filename) => {		
		var downloadLink = document.createElement('a');

		downloadLink.href = filename;

		let pathParts = filename.split('/');

		const fileAja = pathParts[pathParts.length - 1];

		downloadLink.download = fileAja; // You can customize the downloaded filename

		document.body.appendChild(downloadLink);

		downloadLink.click()

		document.body.removeChild(downloadLink)
	}

	static random = (length) => {
		length = length || 3

		const min = Math.pow(10, length - 1)
		const max = Math.pow(10, length) - 1

		return Math.floor(Math.random() * (max - min + 1)) + min;	} 
}

/*
class Cari {
	static riwayat = (param) => {
		console.log('issearchin', this.isSearching);
		if (!param || this.isSearching) {
			console.log( 'masih nyari')
			return
		}

		App.modalTampil({
			id: 'cariGlobal',
			judul: 'Pencarian',
			kaki: '',
			size: '',
			icon: 'lg',
			scrollable: 1,
			onHidden: '',
			onShown: '',
			badan: 'Hello',
		})
	}
}
*/

class Datagrid {
	static toast

	static title = null

	static options = {
		method: 'post',
		fitColumns: true,
		rownumbers: true,
		striped: false,
		height: '500px',
		filterDelay: 500,
		singleSelect: true,
		checkOnSelect: true,
		selectOnCheck: true,
		singleChecked: true,
		scrollOnSelect: false,
		pagination: true,
		pageSize: 20,
		pageList: [20, 500, 1000],
		onBeforeLoad: this.onBeforeLoad,
		onLoadSuccess: this.onLoadSuccess,
		onLoadError: this.onLoadError,
	}

	constructor(container, options) {
		this.container = container.substring(0, 1) == '#' ? container : '#' + container

		options = options || {...this.options }

		this.table = $(this.container).datagrid( options )
	}

	/* 	
	static optionsEdit = (title) => {
		this.title = title.substr(0, 1) == '#' ? title : '#' + title

		return {
			method: 'post',
			fitColumns: true,
			rownumbers: true,
			striped: false,
			height: '500px',
			filterDelay: 500,
			singleSelect: true,
			checkOnSelect: true,
			selectOnCheck: true,
			singleChecked: true,
			scrollOnSelect: false,
			pagination: true,
			pageSize: 20,
			pageList: [20, 500, 1000],
			onBeforeLoad: this.onBeforeLoad,
			onLoadSuccess: this.onLoadSuccess,
			onLoadError: this.onLoadError,
			// onDblClickRow: this.onDblClickCell,
			onDblClickCell: this.onDblClickCell,
			onClickCell: this.onClickCell,
			onEndEdit: this.onEndEdit
		}
	}
	*/

	static onBeforeLoad = () => {
		this.showToast('Memuat data...', 'info', false)

		return true
	}

	static detailview = () => {
		return {
			method: 'post',
			fitColumns: true,
			height: '500px',
			rownumbers: true,
			striped: false,
			filterDelay: 500,
			singleSelect: true,
			checkOnSelect: true,
			selectOnCheck: true,
			singleChecked: true,
			scrollOnSelect: false,
			pagination: true,
			pageSize: 20,
			pageList: [20, 500, 1000],
			onLoadSuccess: this.onLoadSuccess,
			onLoadError: this.onLoadError,
			view: detailview,
			detailFormatter: function (index, row) {
				return `<div class="border detailView"><table></table></div>`;
			}
		}
	}

	static onLoadSuccess = (data) => {
		let pesan = data.pesan || null

		let status = data.status || null

		
		if (status == 'gagal') {
			if (pesan) new Toast({ text: `Gagal! ${pesan}`, type: 'danger', timeOut: 9000 })
		} else {
			this.showToast('Memuat data.', 'success', 2000)
		}

		return
	}

	static showToast = (pesan, tipe, time) => {
		new Toast({
			id: 'RLAnotif',
			title : this.title || null,
			text: pesan,
			type: tipe,
			timeOut: time === false ? false : time
		})
	}

	static onLoadError = () => {
		new Toast({ text: 'Tidak ada balasan dari server.', type: 'error', timeout: false });
	}

	static formatEdit = (icon) => {
		icon = icon || 'bi bi-pencil-square';

		return `<a class="void" href="javascript:;"><i class="${icon}"></i></a>`;
	}

	static formatSatuan = ( value, row, index, suffix ) => {
		if (! suffix) suffix = 'buah';

		return `${value} ${suffix}`;
	}

	static formatLink = (link, tipe, icon) => {
		if (tipe == 'user') {
			link = 'user:' + link
			icon = icon || 'bi bi-search'
		}

		return `<a href="javascript:;" onclick="App.cari('${link}')"><i class="${icon}"></i></a>`
	}

	static formatCari = (link, tipe, icon) => {
		if (tipe == 'user') {
			link = 'user:' + link
			icon = icon || 'bi bi-search'
		}

		return `<a href="javascript:;" onclick="App.cari('${link}')"><i class="${icon}"></i></a>`
	}

	static formatOnOff = (val) => {
		if (val == 1) return '<i class="bi bi-toggle-on"></i>';
		return '<i class="bi bi-toggle-off text-muted"></i>';
	}

	static formatActive = (val) => {
		if (val == 1) return '<i class="bi bi-check2-circle text-success"></i>';
		return '<i class="bi bi-x-circle text-muted"></i>';
	}

	static formatIcon = (icon) => {
		if (!icon) return '';
		return `<i class="${icon} fs-4"></i>`
	}

	static formatTeksPanjang = (value) => {
		if (!value) return '';
		return `<span title="${value}">${value}</span>`
	}

	static hapusGaris = (value) => {
		if (!value) return ''

		return value.replaceAll('_', ' ')
	}

	static formatButton = (teks, tipe) => {
		if (!teks) teks = 'OK'
		if (!tipe) tipe = 'primary'
		return `<button class="btn btn-xs btn-${tipe}"> ${teks} </button>`
	}

	static formatTanggal = (val) => {
		let tanggal = ''

		if (val) tanggal = `<span title="${val.substr(10)}">${val.substr(0, 10)}</span>`

		return tanggal
	}

	static formatUang = (val) => {
		if (val) return DUIT.format(val)

		if (val === '') return ''

		return '0'
	}

	static formatRibuan = (number, separator) => {
		
		if (number) {
			separator = separator || '.'

			return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator)
		}

		return ''
	}

	static styleUang = (val) => {
		if (val < 0) {
			return {class: 'text-danger'}
		}
	}

	/**
	 * Edit cell
	 * @param {*} index 
	 * @param {*} field 
	 * @param {*} value 
	 */
	static editIndex = undefined

	static isEditing = false

	static onDblClickCell = (index, field, value) => {
		if (this.editIndex != index) {
			if (this.endEditing()) {
				$(this.title)
					.datagrid('selectRow', index)
					.datagrid('beginEdit', index);

				var ed = $(this.title).datagrid('getEditor', { index: index, field: field })

				if (ed) {
					($(ed.target).data('textbox') ? $(ed.target).textbox('textbox') : $(ed.target)).focus()
				}

				this.editIndex = index
			} else {
				setTimeout(function () {
					$(this.title).datagrid('selectRow', this.editIndex);
				}, 0);
			}
		}

	}

	static onEndEdit = (index, row, changes) => {
		/* Ini mestinya di declare lagi oleh masing-masing table karena akan berbeda tiap2 table */
	}

	static onClickCell = () => {
		this.endEditing()
	}

	static endEditing = () => {
		if (this.editIndex == undefined) { return true }

		if ($(this.title).datagrid('validateRow', this.editIndex)) {

			$(this.title).datagrid('endEdit', this.editIndex)

			this.editIndex = undefined

			return true
		}
	}
}

class DatagridEdit {
	container
	table
	static editIndex
	static toastId = 'dgEdit'
	static options = {
		method: 'post',
		fitColumns: true,
		rownumbers: true,
		striped: false,
		height: '300px',
		filterDelay: 500,
		singleSelect: true,
		checkOnSelect: true,
		selectOnCheck: true,
		singleChecked: true,
		scrollOnSelect: false,
		pagination: true,
		pageSize: 20,
		pageList: [20, 500, 1000],
		// onBeforeLoad: this.onBeforeLoad,
		// onLoadSuccess: this.onLoadSuccess,
		// onLoadError: this.onLoadError,
		// onDblClickCell: this.onDblClickCell,
		// onClickCell: this.onClickCell,
		// onEndEdit: this.onEndEdit
	}

	constructor(container, options) {
		this.container = container.substr(0, 1) == '#' ? container : '#' + container

		options = options || { ...this.options }

		this.table = $(this.container).datagrid(options)
	}


	static onBeforeLoad = (params) => {
		if (Object.keys(params).length === 0) return

		if (this.siapTampil) {
			setTimeout(() => {
				Toast.tutup(this.toastId)
			}, 2000);

			return
		}

		this.siapTampil = setTimeout(() => {
			this.showToast('Memuat data ke table...', 'info', false)

			this.siapTampil = undefined
		}, 100)

		return true
	}

	static onLoadSuccess = (data) => {
		let pesan = data.pesan || null

		let status = data.status || null

		if (status == 'gagal' && pesan) new Toast({ text: `Gagal! ${pesan}`, type: 'danger', timeOut: 3000 })

		this.showToast('Data dimuat.', 'success', 2000)
	}

	static onLoadError = () => {
		this.showToast('Tidak ada balasan dari server.', 'error', 5000)
	}

	static showToast = (pesan, tipe, time, id) => {
		id = id || 'dgEdit_' + this.title

		new Toast({
			id: id,
			title: 'Table',
			text: pesan,
			type: tipe,
			timeOut: time === false ? false : time
		})
	}

	static onDblClickCell = (index, field, value) => {
		if (this.editIndex == index) return

		if (this.endEditing()) {
			this.table
				.datagrid('selectRow', index)
				.datagrid('beginEdit', index);

			var ed = this.table.datagrid('getEditor', { index: index, field: field })

			if (ed) {
				($(ed.target).data('textbox') ? $(ed.target).textbox('textbox') : $(ed.target)).focus()
			}

			this.editIndex = index
		} else {
			setTimeout(function () {
				this.table.datagrid('selectRow', this.editIndex);
			}, 0);
		}
	}

	static onEndEdit = (index, row, changes) => {
		/* Ini mestinya di declare lagi oleh masing-masing table karena akan berbeda tiap2 table */
		// this.endEditing()
	}

	static onClickCell = (index, field, value) => {
		if (this.editIndex == index) return

		if ( ! this.endEditing() ) return false
	}

	static endEditing = () => {
		if (this.editIndex == undefined) { return true }

		if (this.table.datagrid('validateRow', this.editIndex)) {
			this.table.datagrid('endEdit', this.editIndex)

			this.editIndex = undefined

			return true
		}

		return false
	}

	static setColumnTitle = (table, field, newTitle) => {
		let $panel = table.datagrid('getPanel');

		let $field = $('td[field=' + field + ']', $panel);

		if ($field.length) {
			var $span = $('span', $field).eq(0);

			$span.html(newTitle);
		}
	}
}

class Combogrid {
	static options = {
		cls: 'form-control rounded',
		delay: 1000,
		editable: true,
		width: '100%',
		height: '39px',
		mode: 'local',
		prompt: 'Pilih...',
		limitToList: false,
		fitColumns: true,
		columns: [[]]
	}

	static optionRemote = {
		cls: 'form-control rounded',
		delay: 1000,
		width: '100%',
		editable: true,
		height: '39px',
		mode: 'remote',
		prompt: 'Pilih...',
		loadMsg: 'Mengambil data...',
		limitToList: false,
		fitColumns: true,
		columns: [[]]
	}

	constructor(container, options) {
		this.container = container.substring(0, 1) == '#' ? container : '#' + container

		options = options || { ...this.optionRemote }

		this.combogrid = $(this.container).combogrid(options)
	}
}

class Combobox {
	static options = {
		width: '100%',
		height: '39px',
		cls: 'form-control rounded',
		valueField: 'id',
		textField: 'nama',
		prompt: 'Pilih...',
		panelHeight: 300
	}

	constructor(container, options) {
		container = container.substring(0, 1) == '#' ? container : '#' + container

		options = options || this.options

		this.combobox = $(container).combobox(options)
	}
}


class bsAccordion {
	static listTemplate = `<div class="accordion-item">
		<h2 class="accordion-header">
			<button class="accordion-button" type="button" data-bs-toggle="collapse"
				data-bs-target="#@@ITEM_ID@@" aria-expanded="true"
				aria-controls="@@ITEM_ID@@">
				@@TITLE@@
			</button>
		</h2>
		<div id="@@ITEM_ID@@" class="accordion-collapse collapse show">
			<div class="accordion-body">
				@@BODY@@
			</div>
		</div>
	</div>
`
	accordion

	listOpened = []

	/**
	 * Bootstrap Accordion
	 * @id {string}
	 * @onShown {function} method
	 * @onHidden {function} method
	 * @returns void
	 */
	constructor(params) {
		this.accordion = document.getElementById(params.container)

		this.firstListShown = params.firstListShown || 1

		this.onShown = params.onShown || Function()

		this.onHidden = params.onHidden || Function()

		this.alwaysReloadItem = params.alwaysReloadItem || false

		if (this.accordion) this.initEvent()
	}

	initEvent = () => {
		if (this.firstListShown != '1') {
			this.listOpened.push(this.firstListShown)
		}

		this.accordion.addEventListener('shown.bs.collapse', (event) => {
			const panelId = event.target.getAttribute('id')
			
			if (this.listOpened.indexOf(panelId) >= 0) return 

			this.listOpened.push(panelId)

			if (typeof (this.onShown[panelId]) === 'function') {
				this.onShown[panelId]()
			}
		})

		this.accordion.addEventListener('hidden.bs.collapse', (event) => {
			const panelId = event.target.getAttribute('id')

			if (this.alwaysReloadItem) {
				var indexToRemove = this.listOpened.indexOf(panelId);

				if (indexToRemove !== -1) {
					this.listOpened.splice(indexToRemove, 1)
				}
			}

			if (typeof (this.onHidden[panelId]) === 'function') {
				this.onHidden[panelId]()
			}
		})
	}
}

class FormFormat {
	static duit = (target, pemisah) => {
		pemisah = pemisah || ' '

		$(target).on("keyup", function (event) {
			/** Abaikan tombol arrow */
			if (event.which >= 37 && event.which <= 40) {
				event.preventDefault();
			}

			let value = $(this).val()
			let isDecimal = false
			let desimal = ''
			let nilai = ''
			let koma = ''
			let pieces = []

			if (value == '' || value < 1000) return true

			if (value.indexOf(',') > 0 || value.indexOf('.') > 0) {
				isDecimal = true

				if (value.indexOf('.') > 0) { koma = '.' } else { koma = ',' }

				pieces = value.split(koma)
			}

			nilai = pieces[0] || value

			desimal = isDecimal ? koma + pieces[1] : ''

			// TODO regex untuk pemisah selain spasi blm ada
			nilai = nilai.replace(/ /gi, "").split(/(?=(?:\d{3})+$)/).join(pemisah)

			$(this).val(nilai + desimal)
		})
	}

	static handphone = (target, pemisah, grup) => {
		grup = grup || 4
		pemisah = pemisah || ' '

		$(target).on("keyup", function (event) {
			/** Abaikan tombol arrow */
			if (event.which >= 37 && event.which <= 40) {
				event.preventDefault();
			}

			let value = $(this).val()
			let nilai = ''

			if (value == '' || value.length < grup) return true

			// TODO regex untuk grup (4) blm ada
			nilai = value.replace(/ /gi, "").split(/(?=(?:\d{4})+$)/).join(pemisah);

			$(this).val( nilai )
		})
	}
}

class Tabs {
	tabs

	activeTab = ''

	constructor(tabsLink, activeTab) {
		if (typeof (tabsLink) === 'object') this.tabs = tabsLink
		else console.error('This is not Object!')

		if (activeTab) this.activeTab

		this.initTrigger()

		this.loadDefaultTab()
	}

	initTrigger = () => {
		$('a[data-bs-toggle="tab"]').on('shown.bs.tab', (event) => {
			let target = $(event.target).data();

			if (!target.bsTarget) return

			const link = this.tabs[target.bsTarget];

			if (link) App.tabLoad({
				target: target.bsTarget,
				link: link
			})
		})
	}

	loadDefaultTab = () => {
		/** Initial awal */
		const activeTab = this.activeTab || Object.keys(this.tabs)[0]
		const activeLink = this.tabs[activeTab]

		App.tabLoad({
			target: activeTab,
			link: activeLink
		})
	}	
}

class Waktu {
	static checkPlugin = () => {
		if (typeof (dayjs) == 'function') return true
		else return false
	}

	static formatKapan(value) {
		if (!Waktu.checkPlugin()) {
			console.error('*** Waktu: Plugin DAYJS belum dimuat!')

			return
		}

		if (!value) return ''
		
		let postFix = ''
		let preFix = ''

		if (dayjs().diff(value, 'day') <= 0) preFix = 'dalam '
		else postFix = ' lalu'

		let string1 = dayjs(value).format("D MMMM YYYY")
		let string2 = dayjs(value).toNow(true)

		return `${string1} (${preFix}${string2}${postFix})`
	}

	static formatKapanLengkap(value) {
		if (!Waktu.checkPlugin()) {
			console.error('*** Waktu: Plugin DAYJS belum dimuat!')

			return
		}

		if (!value) return ''

		let string1 = dayjs(value).format("dddd, D MMMM YYYY [jam] h:mm")
		let string2 = dayjs(value).fromNow()

		return `${string1} (${string2})`
	}
}