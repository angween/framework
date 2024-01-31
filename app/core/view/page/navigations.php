<?php
defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;

?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand d-flex justify-content-center">
		<a href="<?= URL_ROOT ?>" class="app-brand-link">
			<span class="app-brand-logo">
				<img src="view/images/silungkang_logo.png" class="img-fluid w-100" />
			</span>
			<!-- <span class="app-brand-text menu-text fw-bolder ms-2"><?= COMPANY_NAME ?></span> -->
		</a>

		<a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<?= App::myNavigations() ?>
	</ul>
</aside>


