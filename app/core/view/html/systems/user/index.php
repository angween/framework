<?php
defined('APP_OWNER') or exit('No direct script access allowed');
?>

<div class="container-fluid mb-3">
	<div class="h5 mt-5 mb-3 text-secondary border-bottom border-secondary">
		Setting Pengguna
	</div>
</div>

<div class="overflow-hidden">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item ps-4">
			<a href="#" type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tabPengguna" aria-controls="tabPengguna" aria-selected="true">
				Pengguna
			</a>
		</li>
		<li class="nav-item">
			<a href="#" type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tabKelompok" aria-controls="tabKelompok" aria-selected="false">
				Kelompok
			</a>
		</li>
	</ul>
</div>

<div class="tab-content">
	<div class="tab-pane fade show active" id="tabPengguna" role="tabpanel">
		<p>a</p>
	</div>
	<div class="tab-pane fade" id="tabKelompok" role="tabpanel">
		<p>b</p>
	</div>
</div>

<script>
$(function() {
	/** Tangkap perpindahan TAB */
	const tabs = {
		'#tabPengguna': 'user/viewUser',
		'#tabKelompok': 'level/viewLevel'
	}

	$('a[data-bs-toggle="tab"]').on('shown.bs.tab', (event) => {
		let target = $(event.target).data();

		if (!target.bsTarget) return

		const link = tabs[target.bsTarget];

		if (link) App.tabLoad({
			target: target.bsTarget,
			link: link
		})
	})

	/** Initial awal - muat halaman users */
	App.tabLoad({
		target: '#tabPengguna',
		link: 'user/viewUser'
	})
})
</script>