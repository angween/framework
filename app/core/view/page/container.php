<?php
defined('APP_OWNER') or exit('No direct script access allowed');
?>
<link rel="stylesheet" type="text/css" href="<?= URL_PUBLIC_VIEW ?>assets/libs/easyui/themes/metro/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?= URL_PUBLIC_VIEW ?>assets/css/datagrid.css<?= IN_DEVELOPMENT ?>" />
<!-- <link rel="stylesheet" type="text/css" href="<?= URL_PUBLIC_VIEW ?>assets/css/theme-default.css" /> -->

<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/easyui/jquery.easyui.min.js"></script>
<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/dayjs/dayjs.min.js"></script>
<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/dayjs/locale/id.js"></script>
<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/dayjs/relativeTime.js"></script>
<script>
	dayjs.locale('id')
	dayjs.extend(window.dayjs_plugin_relativeTime);
</script>

<div class="layout-wrapper layout-content-navbar">
	<div class="layout-container">

		<?php include(PATH_FILE_VIEW . "page/navigations.php" ); ?>

		<div class="layout-page">

			<?php include(PATH_FILE_VIEW . "page/navtop.php"); ?>

			<div class="content-wrapper pt-4 mb-3">
				<div id="targetIsi" class="container-xxl flex-grow-1 container-p-y">
				</div>
			</div>

		</div>

		<script src="<?= URL_PUBLIC_VIEW ?>assets/js/navigation.js<?= IN_DEVELOPMENT ?>"></script>
	</div>
</div>
