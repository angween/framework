<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?= URL_PUBLIC_VIEW ?>assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Halaman tidak ditemukan</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= URL_PUBLIC_VIEW ?>assets/img/favicon/favicon.ico" />

	<!-- <link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/libs/bootstrap/css/bootstrap.min.css" type="text/css" /> -->
	<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/css/core.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/css/theme-default.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/libs/animate/animate.min.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/libs/boxicons/css/boxicons.min.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/libs/perfectscrollbar/perfectscrollbar.css" type="text/css" />
	<link rel="stylesheet" href="<?= URL_PUBLIC_VIEW ?>assets/css/app.css<?= IN_DEVELOPMENT ?>" type="text/css" />


	<script src="<?= URL_PUBLIC_VIEW ?>assets/js/helpers.js"></script>
	<script src="<?= URL_PUBLIC_VIEW ?>assets/js/config.js"></script>

	<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/jquery/jquery-3.6.0.min.js"></script>
	<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?= URL_PUBLIC_VIEW ?>assets/js/app.js<?= IN_DEVELOPMENT ?>"></script>
  </head>

  <body>
    <!-- Content -->
	<style>
		.misc-wrapper {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			min-height: calc(100vh - (1.625rem * 2));
			text-align: center;
		}
	</style>
    <!-- Error -->
    <div class="container-xxl container-p-y">
      <div class="misc-wrapper">
        <h2 class="mb-2 mx-2">Halaman tidak ditemukan :(</h2>
        <p class="mb-4 mx-2">Waduh! 😖 Halaman dicari tidak ditemukan diserver.</p>
        <a href="index.php" class="btn btn-primary"> Kembali </a>
        <div class="mt-3">
          <img
            src="<?= URL_PUBLIC_VIEW ?>assets/images/404_light.png"
            alt="404"
            width="500"
            class="img-fluid"
            data-app-dark-img="<?= URL_PUBLIC_VIEW ?>assets/images/404_dark.png"
            data-app-light-img="<?= URL_PUBLIC_VIEW ?>assets/images/404_light.png"
          />
        </div>
      </div>
    </div>
    <!-- /Error -->

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
	<script src="<?= URL_PUBLIC_VIEW ?>assets/libs/perfectscrollbar/perfectscrollbar.js"></script>

    <!-- <script src="<?= URL_PUBLIC_VIEW ?>assets/vendor/js/menu.js"></script> -->
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?= URL_PUBLIC_VIEW ?>assets/js/main.js"></script>

    <!-- Page JS -->

  </body>
</html>
