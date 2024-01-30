$( function() {
	class FormulirLogin extends Formulir {
		constructor( params ) {
			super( params )

			this.initForm()
		}

		initForm() {
			$( this.target ).submit( (e) => {
				e.preventDefault()

				this.params['data'] = $(this.target).serializeArray();

				$( this.target ).addClass('was-validated')
				
				if ( this.params['data'][0]['value'] == '' ) {
					return;
				}

				Ajax
					.kirim({
						url: this.params.submitUrl,
						data: this.params.data,
						toast: { text: 'Login...', id: 'login', type: 'info', time: false }
					})
					.done( ( respon ) => {
						let status = respon.status || false

						let pesan = respon.pesan || false

						if ( status == 'sukses' ) {
							new Toast({
								id:'login',
								title: 'Login',
								type: 'success',
								text: 'Login berhasil, membuka Dashboard!',
								timeOut: 2000
							})

							setTimeout( () => { window.location.reload() }, 1000 )
						}

						if ( status == 'gagal' ) { new Toast({
							id:'login',
							title: 'Login',
							type: 'error',
							text: pesan,
							timeOut: false
						}) } })
					.fail( (a, b, c) => {
						let respon = a.responseJSON || a.responseText

						if (!respon) {
							new Toast({id:'error', text:'Tidak ada respon dari server!', type: 'danger', timeOut: false})

							console.error('*** Tidak ada respon!')

							return
						}

						new Toast({
							text: respon.pesan,
							title: 'Login',
							type: 'error',
							timeOut: false
						}) })
			})
		}
	}

	let frmLogin = new FormulirLogin({ 
		target: '#frmLogin',
		submitUrl: 'login'
	})

	$(`a#a_need_help`).click(e => {
		e.preventDefault()

		alert("Untuk karyawan silahkan ketik nama lengkap, pisahkan perkata dengan titik, misal: JOKO.WIDODO, password standar adalah: Qwerty1")
	})
})
