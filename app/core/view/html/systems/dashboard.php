<?php
defined('APP_OWNER') or exit('No direct script access allowed');
use \RLAtech\controller\App;

$uniqID = uniqid();
$tipeMember = 'view';
?>
<script src="<?= URL_VIEW ?>assets/libs/easyui/old-datagrid-filter.js"></script>

<div class="container-fluid mb-3">
	<div class="h5 mt-5 mb-3 text-secondary border-bottom border-secondary">
		Pindai QR-Code Member
	</div>

	<div class="pt-3">
		<div class="row">
			<div class="col-12 col-sm-4 mb-3">
				<div class="border rounded" style="min-width:210px; min-height:250px">
					<div id="scanner_<?= $uniqID ?>"></div>
				</div>
			</div>

			<div class="col-12 col-sm-8 mb-3">
				<div class="alert alert-info alert-dismissible fade show" role="alert">
					<strong>Cara Pakai</strong> Kalau muncul permintaan izin untuk menggunakan kamera, silahkan di-BOLEH-kan supaya
					bisa menggunakan kamera (baik kamera HP ataupun Komputer) untuk scan QR code.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
				
				<textarea type="text" tabindex="-1" id="barcode_id_<?= $uniqID ?>" rows="4" class="form-control" autofocus
					placeholder="Hasil scan kamera atau barcode scanner akan muncul di sini, atau ketik secara manual."></textarea>
			</div>
		</div>
	</div>

	<div class="h5 mt-5 mb-3 text-secondary border-bottom border-secondary">
		Data Member
	</div>

	<form id="frmScanQRcode_<?= $uniqID ?>" class="row my-3 pt-3">		
		<div class="col-sm-9 col-12 order-sm-2">
		<?php include("app/spg/view/assets/html/form_member.php") ?>
		</div>

		<div class="col-sm-3 col-12 order-first order-md-2">
			<img id="photo_<?= $uniqID ?>" class="img-fluid img-thumbnail w-100" 
				src="view/images/member/photo_placeholder.jpg" 
				alt="view/images/member/photo_placeholder.jpg" 
			/>
		</div>
	</form>
</div>

<script src="./view/assets/js/html5-qrcode.min.js"></script>

<script src="./view/assets/libs/qrcode/qrcode.js<?= IN_DEVELOPMENT ?>"></script>

<script type="module">
	import { Scanner } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/Scanner.js<?= IN_DEVELOPMENT ?>"
	import { ComboBox } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/ComboBox.js<?= IN_DEVELOPMENT ?>"
	import { Card as MemberCard } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/Card.js<?= IN_DEVELOPMENT ?>"
	import { Qcode } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/Qcode.js<?= IN_DEVELOPMENT ?>"
	import { MemberForm } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/MemberForm.js<?= IN_DEVELOPMENT ?>"
	import { MemberPoint as Point } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/MemberPoint.js<?= IN_DEVELOPMENT ?>"
	import { VoucherTable as Voucher } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/VoucherTable.js<?= IN_DEVELOPMENT ?>"
	import { Dashboard } from "./app/<?= PROGRAM_PATH ?>/view/assets/js/Dashboard.js<?= IN_DEVELOPMENT ?>"

	const qcode = new Qcode({
		size: 320,
		colorDark: '#6D3393'
	})

	const frmMember = new Scanner({
		scanner: "scanner_<?= $uniqID ?>", 
		result: "barcode_id_<?= $uniqID ?>",
		form: "frmScanQRcode_<?= $uniqID ?>",
		photo: "photo_<?= $uniqID ?>",
		memberIDelm: "member_id_<?= $uniqID ?>",
		btnResetPassword: "btnResetPassword_<?= $uniqID ?>"
	})

	new MemberCard({
		form: "frmScanQRcode_<?= $uniqID ?>",
		photo: "photo_<?= $uniqID ?>",
		button: "member_kartu_<?= $uniqID ?>",
		type: 'dashboard',
		QRcode: qcode,
		uniqID: '<?= $uniqID ?>'
	})

	new MemberForm(
		"frmScanQRcode_<?= $uniqID ?>",
		ComboBox,
		'<?= $uniqID ?>',
		"edit",
	)

	new Point({
		formID: `frmScanQRcode_<?= $uniqID ?>`,
		pointBtn: `btnTukarPoint_<?= $uniqID ?>`,
		uniqID: `<?= $uniqID ?>`
	})

	//const viewVoucher = new VoucherTable()
	

	$(function(){
		/** checkin */
		$(`#btnCheckIn_<?= $uniqID ?>`).click((ev) => {
			ev.preventDefault()

			const memberId = $(`#frmScanQRcode_<?= $uniqID ?> input[name="member_id"]`).val()

			if ( ! memberId || memberId == '-' ) {
				App.toast('Member ID harap di-isi!', 'danger', 5000)
				
				return
			}

			Ajax.kirim({
				url: 'spg/member/postCheckIn',
				data: {memberId: memberId},
			}).done( respon => {
				const status = respon.status || 'gagal'
				let affected = respon.affected || null
				let pesan = respon.pesan ||null
				let type = 'danger'
				let delay = 5000

				if ( status == 'sukses' ) {
					type = 'success'

					if (affected > 0) pesan = 'Check-In berhasil. Point tidak ditambahkan'
					else {
						pesan = 'Poin Check-In tidak bisa ditambahkan! Pastikan Member terakhir check-in lebih dari 5 jam lalu.'

						type = 'danger'

						delay = 9000
					}
				}

				App.toast(pesan, type, delay)
			})
		})

		/** voucher */
		new Dashboard({
			frmMember: frmMember,
			btnVoucher: 'btnPakaiVoucer_<?= $uniqID ?>',
			Voucher:Voucher
		})

		// $(`#btnVoucher_<?= $uniqID ?>`).click( (ev) => {
		// 	ev.preventDefault()

		// 	alert('Fitur voucher belum tersedia.')
		// })
	})
</script>