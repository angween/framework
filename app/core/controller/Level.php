<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
use core\model\Level as Model;

class Level {
	public Model $model;

	public function __construct()
	{
		$this->model = new Model();
	}

	public function index( $parameter = null )
	{
		if ( ! App::view( 'view/html/systems/level/index.php' ) ) {
			Router::notFound('index');
		}
	}

	public function viewLevel( $parameter = null ) 
	{
		if ( ! App::view( PATH_FILE_VIEW . 'page/level/level.php' ) ) {
			Router::notFound('Halaman');
		}
	}

	public function get( array $payload )
	{
		$params['path'] = $payload['path'];

		$payload = $payload['payload'];

		if ( count( $params['path'] ) > 0 ) {
			$path = 'get' . ucfirst( $params['path'][0] );

			if ( method_exists($this, $path) ) {
				$this->{$path}( $payload );
			}
		}

		$parameter = [];
		$parameter['page'] = $payload['page'] ?? 1;
		$parameter['limit'] = $payload['rows'] ?? 500;
		$parameter['offset'] = Datagrid::limit($parameter['page'], $parameter['limit']);
		$parameter['where'] = Datagrid::filter($payload['filterRules'] ?? []);

		// jika ada Q yg dari combogrid 
		if ( isset( $payload['data']['q'] ) ) {
			$teks = $payload['data']['q'];
			$combogrid = "a.nama like '%$teks%' or a.keterangan like '%$teks%'";

			if ($parameter['where'] == []) $parameter['where'] = $combogrid;
			else $parameter['where'] .= " AND " . $combogrid;
		}

		$result = $this->model->getLevel($parameter);

		if ( $result === false ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Tidak bisa ambil data',
				'rows' => [],
				'total' => 0
			]);
		}

		$result['status'] = 'sukses';

		App::respon( $result );
	}

	public function getNavigations( array $parameter )
	{
		$params = Datagrid::initParameter( $parameter );

		$hasil = $this->model->getNavigations( $params );

		if ( $hasil['rows'] != 0 ) $hasil['status'] = 'sukses';
		else $hasil = [
			'status' => 'gagal',
			'rows' => [],
			'total' => 0
		];

		App::respon( $hasil );
	}

	public function put( array $params = null) 
	{
		$parameter = $params['payload'];

		$levelId = $parameter['permission_id'];

		$keterangan = $parameter['a_keterangan'];

		$namaKelompok = $parameter['a_nama'];

		if ( ! $levelId || ! $keterangan ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Parameter kurang!'
			]);
		}

		
		/**
		 * Update Kelompok
		 */
		$result = $this->model->put( $levelId, $keterangan );

		if ( $result === false ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Gagal update Kelompok!'
			]);
		}

		if ( $result === 0 ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Tidak ada perubahan disimpan!'
			]);
		}


		/**
		 * Simpan Riwayat
		 */
		$tulisRiwayat = new Riwayat();

		if (!$tulisRiwayat->postRiwayat(
			[
				'table' => '_user_riwayat',
				'key_id' => 'user_id',
				'key_value' => App::$loggedInUser->id,
				'riwayat' => [
					'tm' => date('Y-m-d H:i:s'),
					'rw' => 'LUP%UID:' . App::$loggedInUser->id . ',TXT:' . $namaKelompok
				]
			]
		)) {
			return false;
		}


		App::respon([
			'status' => 'sukses',
			'pesan' => 'Kelompok berhasil diupdate.'
		]);
	}
}