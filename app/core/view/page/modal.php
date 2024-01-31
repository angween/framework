<?php
// defined('APP_OWNER') or exit('No direct script access allowed');

echo "<!-- modal -->";
$modalTheme    = isset($_POST['theme'])        ? $_POST['theme'] : 'default';
$modalID       = isset($_POST['id'])           ? $_POST['id'] : false;
$modalSize     = isset($_POST['sz'])           ? 'modal-' . $_POST['sz'] : '';
$statis        = isset($_POST['st'])           ? $_POST['st'] : 0;
$fade          = isset($_POST['nofade'])       ? '' : 'fade';
$fullscreen    = isset($_POST['fullscreen'])   ? $_POST['fullscreen'] : 0;
$scrollable    = isset($_POST['scrollable'])   ? $_POST['scrollable'] : 0;
$icon          = isset($_POST['icon'])         ? $_POST['icon']       : 'bx bx-box';
$border        = isset($_POST['border'])       ? $_POST['border']     : '';
$padding       = isset($_POST['padding'])      ? 'p-' . $_POST['padding']     : '';
$borderFooter  = isset($_POST['borderFooter']) ? 'border-top' : '';
$modalCentered = isset($_POST['centered'])     ? 'modal-dialog-centered' : '';

// <i class="menu-icon tf-icons bx bx-box"></i>
if ($modalSize == 'modal-xl') { $_modalSize = " modal-fullscreen-xl-down";} 
elseif ($modalSize == 'modal-lg') {	$_modalSize = " modal-fullscreen-lg-down"; } 
elseif ($modalSize == 'modal-md') {	$_modalSize = " modal-fullscreen-md-down"; }
elseif ($modalSize == 'modal-sm') { $_modalSize = " modal-fullscreen-sm-down"; }
else $modalSize = '';

if ($fullscreen == 1) $modalSize = " modal-fullscreen";
else $modalSize .= $_modalSize;

if ($statis == 1) $statis = " data-bs-backdrop='static' ";
else $statis = '';

if ($scrollable == 0) $scrollable = "";
else $scrollable = "modal-dialog-scrollable";

if ($modalTheme == 'default') {
	echo "
<script>
	var namaModalAwal='" . $modalID . "';
</script>
<div class='modal $fade' id='$modalID' tabindex='-1' role='dialog' $statis>
	<div class='modal-dialog $modalSize $scrollable $modalCentered' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
                <h5 class='modal-title'></h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
			</div>
			<div class='modal-body $border $padding'>
				<p>Memuat isi...</p>
			</div>
			<div class='modal-footer $borderFooter'>
			</div>
		</div>
	</div>
</div>
";
} elseif ($modalTheme == 'card') {
	echo "
<div class='modal $fade show' id='$modalID' tabindex='-1' role='dialog' $statis>
	<div class='modal-dialog $modalSize $scrollable' role='document'>
		<div class='modal-content'>
			<div class='card'>
				<div class='card-header'>
					<ul class='nav nav-tabs card-header-tabs'>
						<li class='nav-item'>
							<a class='nav-link active' aria-current='true' href='#'>Active</a>
						</li>
						<li class='nav-item'>
							<a class='nav-link' href='#'>Link</a>
						</li>
						<li class='nav-item'>
							<a class='nav-link disabled'>Disabled</a>
						</li>
					</ul>
				</div>
				<div class='card-body'>
					<h5 class='card-title'>Judul</h5>
					<p class='card-text'>Memuat isi...</p>
				</div>
			</div>
		</div>
	</div>
</div>";
}
?>
<script>
(function(){
	
})()
</script>
