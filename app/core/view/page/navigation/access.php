<?php
defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller as RLA;
?>

<?php if ( RLA\App::$loggedInUser->help ) { ?>
<div class="alert alert-primary alert-dismissible fade show m-4" role="alert">
	<p><strong>Cara Pakai:</strong> Centang 1 <i>Kelompok</i> yang akan di-set, kemudian pada tabel kedua pilih 1 atau lebih <i>Navigasi</i> yang boleh dia akses. Semua pilihan akan otomatis disimpan sewaktu dicentang.</p>
	<p>Perlu diperhatikan bahwa <em>Dashboard</em> akan selalu bisa dibuka semua User, dan <em>Administrator</em> akan selalu bisa buka semua Navigasi.</p>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>

<div class="row g-2" style="width: 99.9%">
	<div class="col-md-5 border-info border-end">
		<p class="ps-4 pt-4">Kelompok dipilih:</p>
		<div class="bungkus" style="min-width: unset">
			<div id="tblLevels">B</div>
		</div>
	</div>

	<div class="col-md-7">
		<p class="ps-4 pt-4">Bisa akses halaman berikut</p>
		<div class="bungkus" style="min-width: unset">
			<div id="tblNavigations">A</div>
		</div>
	</div>
</div>

<script src="<?= PATH_CORE_VIEW ?>asset/js/access.js<?= IN_DEVELOPMENT ?>"></script>