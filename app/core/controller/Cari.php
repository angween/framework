<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\App;

use RLAtech\model\Cari as Model;

class Cari 
{
	private $searchFor = null;

	private $searchKeyword = null;

	private Model $model;

	private $searchController;

	public function __construct()
	{
		$this->model = new Model();
	}

	public function index( $parameter )
	{
		$keyword = $parameter['payload']['cari'] ?? null;

		if ( ! isset( $keyword ) ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Parameter kurang!'
			]);
		}

		// Pecah pencarian berdasar controller dan keywordnya
		$this->kenaliDataDiCari( trim( $keyword ) );

		if ( $this->searchFor === null ) {
			App::respon(
				contentType: ["text/html"],
				result: "Maaf, pencarian <b>". $this->searchKeyword."</b> tidak dikenali!"
			);
		}

		// Cek Controller yg mau di cari
		if ( $this->searchController === null ) {
			App::respon(
				contentType: ['application/json'],
				result: [
					'status' => 'gagal',
					'respon' => 'Pencarian tidak dikenali!'
				]
			);
		}

		$controllerName = "RLAtech\\controller\\" . $this->searchController;

		$controllerAddon = "addons\\controller\\" . $controllerName;

		if ( class_exists( $controllerName ) ) {
			$controller = new $controllerName;
		} elseif ( class_exists( $controllerAddon ) ) {
			$controller = new $controllerAddon;
		} else {
			App::respon(
				contentType: ['application/json'],
				result: [
					'status' => 'gagal',
					'respon' => 'Handler data dicari tidak ada!'
				]
			);
		}

		$keyword = $this->searchKeyword;

		if ( ! method_exists($controller, 'cari') || ! is_callable( [ $controller, 'cari' ] ) ) {
			$this->pencarianKosong( $keyword );
		}

		// Jika ada - ambil datanya dari controller bersangkutan
		$result = $controller->cari( $keyword );

		if ( empty( $result['umum'] ) || ! isset( $result['umum'] ) ) {
			$this->pencarianKosong( $keyword );
		}

		// Olah data jadi HTML
		$respon = $this->renderHTML( $result );
		
		// Kirim ke client
		App::respon(
			contentType: ["text/html"],
			result: $respon
		);
	}

	private function renderHTML( $result )
	{
		$navItem = "<ul class='nav nav-pills' role='tablist'>";
		$navBody = "<div class='tab-content'>";
		$first = true;

		foreach ($result as $_judul => $isi) {
			/** Tab pertama auto active dan terpilih */
			$judul = ucfirst( $_judul );

			if ( $first ) { 
				$active = 'active';
				$selected = 'true';
				$first = false;
			} else {
				$active = '';
				$selected = '';
			}

			$navItem .= "<li class='nav-item'>
					<a 
						href='#' type='button' class='nav-link $active' 
						role='tab' data-bs-toggle='tab' data-bs-target='#tab$judul' 
						aria-controls='tab$judul' aria-selected='$selected'
					>
						$judul
					</a>
				</li>\n";

			$navBody .= "<div class='tab-pane fade show $active' id='tab$judul' role='tabpanel'>$isi</div>\n";
		}

		$navItem .= "</ul>\n";
		$navBody .= "</div>\n";


		return $navItem.$navBody;
	}

	private function pencarianKosong( $keyword )
	{
		App::respon(
			contentType: ["text/html"],
			result: "Method pencarian untuk {$this->searchController} tidak ditemukan."
		);
	}

	private function kenaliDataDiCari( $keyword )
	{
		if ( strpos( $keyword, ":" ) !== false ) {
			$arrayCari = explode( ':', $keyword );

			if ( count( $arrayCari ) > 1 ) {
				$this->searchFor = $arrayCari[0];
				$this->searchKeyword = $arrayCari[1];
			}
		} else {
			$this->searchFor = substr( $keyword, 0, 1 );

			$this->searchKeyword = $keyword;
		}

		$this->searchFor();
		
		return true;
	}

	/**
	 * Cari Conroller yang akan handle pencarian
	 * berdasar dari kode prefix nya
	 */
	private function searchFor() : void
	{
		$searchFor = strtoupper( $this->searchFor );

		$controller = null;

		if ( \in_array( $searchFor, $this->model->searchPattern ) ) {
			$controller = $this->model->searchData[ 
				array_search(
					$searchFor,
					array_column( $this->model->searchData, 'key_code' )
				)]['controller'];
		}

		$this->searchController = $controller;
	}
}

?>