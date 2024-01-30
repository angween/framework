<?php
defined('APP_OWNER') or exit('No direct script access allowed');
?>

<div class="container-fluid mb-3">
	<div class="h5 mt-5 mb-3 text-secondary border-bottom border-secondary">
		Kelola Member
	</div>
</div>

<div class="overflow-hidden">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item ps-4">
			<a href="#" type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tabNavigasi" aria-controls="tabNavigasi" aria-selected="true">
				Navigasi
			</a>
		</li>
		<li class="nav-item">
			<a href="#" type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tabAkses" aria-controls="tabAkses" aria-selected="false">
				Akses Kelompok
			</a>
		</li>
	</ul>
</div>

<div class="tab-content">
	<div class="tab-pane fade show active" id="tabNavigasi" role="tabpanel">
		<p>a</p>
	</div>
	<div class="tab-pane fade" id="tabAkses" role="tabpanel">
		<p>b</p>
	</div>
</div>

<script>
	$(function() {
		/** Tangkap perpindahan TAB */
		const tabs = {
			'#tabNavigasi': 'navigation/view/navigations',
			'#tabAkses': 'navigation/view/access'
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

		/** Initial awal - muat halaman */
		App.tabLoad({
			target: '#tabNavigasi',
			link: 'navigation/view/navigations'
		})
	})
</script>