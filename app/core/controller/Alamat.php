<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use \RLAtech\controller\{
	App,
	Datagrid
};

class Alamat
{
	public $model;

	public array $result;

	public array $errors;

	public function __construct()
	{
		$_model = "RLAtech\model\Alamat";

		$this->model = new $_model;
	}

	public function get( array $params ) :void
	{
		$path = $params['path'];

		$parameter = $params['payload'];
		$returnIsi = false;

		if ( empty($parameter) ) {
			App::respon( ['rows' => [], 'total' => 0]  );
		}

		/**
		 * Rapikan parameter payload
		 */
		// Cek apa ada query dari combogrid
		if (isset($parameter['q']) && $parameter['q'] != '') {
			$returnIsi = true;

			$parameter['filterRules'] = Datagrid::addFilter([
				['field' => 'a_kabupaten', 'op' => 'like', 'value' => $parameter['q']],
				['field' => 'b_provinsi',  'op' => 'like', 'value' => $parameter['q']],
			]);
		}
			
		$gridParams = Datagrid::initParameter($parameter);

		if (isset($parameter['q']) && $parameter['q'] != '') {
			$gridParams['where'] = "(" . str_replace("AND", "OR", $gridParams['where']) . ")";
		}

		$result = $this->model->get($gridParams);

		if (is_null($result)) die();

		// if ($returnIsi) {
		// 	App::respon([
		// 		$result['rows']
		// 	]);
		// }

		App::respon([
			'rows' => $result['rows'],
			'total' => $result['total']
		]);
	}


	public function getKabupaten(?array $params): ?array 
	{
		$cariKota = $params['q'] ?? null;

		$result = $this->model->getKabupaten(kota: $cariKota);

		if ( is_null( $result ) ) return null;

		return $result;
	}


	public function getKecamatan(?array $params): ?array 
	{
		$cari = $params['q'] ?? null;

		$result = $this->model->getKecamatan(kota: $cari);

		if ( is_null( $result ) ) return null;

		return $result;
	}



	public function getKabupatenID(string $kabupaten_name): ?int
	{
		$kabupaten_id = $this->model->getKabupatenID(name: $kabupaten_name);

		return $kabupaten_id;
	}
}