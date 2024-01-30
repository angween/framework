<?php
require("app/RLAtech/config.php");

use RLAtech\controller\App;

//  echo "<!--";
//  print_r($_SESSION);
//  echo "!-->";
?>
<!DOCTYPE html>

<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?= URL_VIEW ?>/assets/">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, shrink-to-fit=no"/>

	<title><?= COMPANY_NAME ?></title>

	<meta name="description" content="" />

	<!-- icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="<?= URL_VIEW ?>assets/ico/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= URL_VIEW ?>assets/ico/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= URL_VIEW ?>assets/ico/favicon-16x16.png">
	<link rel="manifest" href="<?= URL_VIEW ?>assets/ico/site.webmanifest">
	<link rel="mask-icon" href="<?= URL_VIEW ?>assets/ico/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-config" content="<?= URL_VIEW ?>assets/view/images/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@100;300&family=Taviraj:wght@100;500&display=swap" rel="stylesheet">

	<!-- Loading CSS Assets -->
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/libs/bootstrap/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/css/core.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/css/theme-default.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/libs/animate/animate.min.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/libs/perfectscrollbar/perfectscrollbar.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/css/app.css<?= IN_DEVELOPMENT ?>" type="text/css" />
	<link rel="stylesheet" href="<?= URL_VIEW ?>assets/libs/bootstrap/fonts/bootstrap-icons.min.css">

	<!-- Loading script assets -->
	<script src="<?= URL_VIEW ?>assets/libs/jquery/jquery-3.6.0.min.js"></script>
	<!-- <script src="<?= URL_VIEW ?>assets/libs/bootstrap/js/popper.min.js"></script> tidak perlu kalau sudah pakai bundle -->
	<script src="<?= URL_VIEW ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- App scripts -->
	<script src="<?= URL_VIEW ?>assets/js/helpers.js"></script>
	<script src="<?= URL_VIEW ?>assets/js/config.js"></script>
	<script src="<?= URL_VIEW ?>assets/js/app.js<?= IN_DEVELOPMENT ?>"></script>

	<?php 
	$sudahPakaiNotif = false;

	if ( App::$loggedInUser && ! $__LOCALHOST && $sudahPakaiNotif  ) {
		/** FORMAT ABCD => Karyawan-Manager-Director-Yayasan-Finance, kalau true = T kalau tidak = F,
		 * contoh karyawan aja = TFFFF
		 */
		$grup = '';
		$karyawan = 'F';
		$manager  = 'F';
		$director = 'F';
		$yayasan  = 'F';
		$finance  = 'F';

		// if ( \RLA\controller\Parameters::isKaryawan() ) $karyawan = 'T';
		// if ( \RLA\controller\Parameters::isManager() ) $manager = 'T';
		// if ( \RLA\controller\Parameters::isDirector() ) $director = 'T';
		// if ( \RLA\controller\Parameters::isFinance() ) $finance = 'T';

		$grup = $karyawan . $manager . $director . $yayasan . $finance;
	?>
	<!-- One Signal -->
	<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
	<script>
		window.OneSignalDeferred = window.OneSignalDeferred || [];

		/** Hapus tag yg sudah di-set */
		const removeTags = ['karyawan'];

		/** Set ulang dengan tag sesuai yg login saat ini */
		const tags = {
			group: '<?= $grup ?>'
		}

		OneSignalDeferred.push( async function(OneSignal) {
			await OneSignal.init({
				appId: ""
			})

			// await OneSignal.User.removeTags(removeTags);

			await OneSignal.User.addTags(tags)
		})
	</script>
	<?php } ?>
</head>

<body>
	<?php
	if ( ! App::$loggedInUser ) {
		include(PATH_FILE_VIEW . "/html/systems/login.php" );
	} else {
		include(PATH_FILE_VIEW . "/html/systems/container.php" );
	}
	?>

	<!-- Other Container Container -->
	<div id="modal-container"></div>
	<div id="toast-container" class="bs-toast toast-container top-0 end-0 p-3"></div>
	<div class="layout-overlay layout-menu-toggle"></div>

	<!-- App scripts -->
	<script src="<?= URL_VIEW ?>assets/libs/perfectscrollbar/perfectscrollbar.js"></script>
	<script src="<?= URL_VIEW ?>assets/js/menu.js"></script>
	<script src="<?= URL_VIEW ?>assets/js/main.js<?= IN_DEVELOPMENT ?>"></script>
</body>

</html>