<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\model\User as Model;

use core\controller\DB as DB;

class User 
{
	const JML_KRITERIA_PASSWORD = 3;

	public $levelAdmin = [1];

	private static $cost = ['cost' => HASH_COST];

	public Model $model;

	public $errors;

	public function __construct()
	{
		$this->model = new Model;
		// if ( App::cekModelFile( '/model/User.php' ) ) {
		// 	$this->model = new Model();
		// } else {
		// 	App::respon([
		// 		'responCode' => 404,
		// 		'result' => [
		// 			'status' => 'gagal',
		// 			'pesan' => 'User:MODEL tidak ditemukan'
		// 		]
		// 	]);
		// }
	}

	public function index( $parameter = null )
	{
		if ( ! App::view( PATH_CORE_VIEW . 'page/user/index.php' ) ) {
			Router::notFound('index');
		}
	}

	public function cari( string $keyword = '' ) : array
	{
		/**
		 * Tarik info umum
		 */
		if ( $keyword == '' ) {
			App::respon(
				contentType: ["text/html"],
				result: "Pencarian $keyword tidak ditemukan."
			);
		}

		$keyword = trim( $keyword );

		$user = $this->model->get([
			'where' => "a.user_name = '$keyword' OR a.display_name = '$keyword'",
			'offset' => 0,
			'limit' => 1
		]);

		if ( ! isset( $user['rows'] ) ) {
			$this->pencarianGagal($keyword);
		}


		/**
		 * Tarik riwayat
		 */
		$riwayat = new Riwayat();

		$resRiwayat = $riwayat->tarikRiwayatDariTable( TBL_RIWAYAT, $user['rows'][0]['a_user_id'] );

		$result['umum'] = $riwayat->renderHTMLumum($user['rows'][0]);

		$result['riwayat'] = $resRiwayat['riwayat'] ?? '';

		return $result;
	}

	private function pencarianGagal( $keyword )
	{
		App::respon(
			contentType: ["text/html"],
			result: "Pencarian $keyword tidak ditemukan."
		);
	}

	private function generatePassword($plainText, $salt = NULL)
	{
		return password_hash($plainText, PASSWORD_DEFAULT, self::$cost);
	}

	private function cariHashCost()
	{
		$timeTarget = 0.05; // 50 milliseconds 

		$cost = HASH_COST;
		do {
			$cost++;
			$start = microtime(true);

			password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);

			$end = microtime(true);
		} while (($end - $start) < $timeTarget);

		self::$cost = $cost;
	}

	public function viewUser( $parameter = null ) 
	{
		if ( ! App::view( PATH_CORE_VIEW . 'page/user/user.php' ) ) {
			Router::notFound('Halaman');
		}
	}

	public function get( array $params = null ) 
	{
		$parameter = $params['payload'] ?? null;

		if ( is_null( $parameter ) ) {
			App::responGagal('Parameter kurang!');
		}

		$dataOnly = $parameter['dataOnly'] ?? null;

		$params = Datagrid::initParameter( $parameter );

		$hasil = $this->model->get( $params );

		$hasil['status'] = 'sukses';

		if ($dataOnly ) App::respon($hasil['rows']);

		App::respon($hasil);
	}

	public function getProfile()
	{
		$result = $this->model->get([
			'where' => 'a.user_id = ' . (int) App::$loggedInUser->id,
			'offset' => 0,
			'limit' => 1
		]);

		App::respon([
			'status' => 'sukses',
			'rows' => $result['rows'],
			'total' => $result['total'],
		]);

	}

	public function xxxgetRiwayat( $keyword )
	{
		$riwayat = new Riwayat();

		$riwayat->tarikRiwayatDariTable( $this->model::TBL_RIWAYAT, 'id' );
	}

	public function put( array $params = null )
	{
		$parameter = $params['payload'] ?? null;
		$isAdmin   = false;

		/** Verifikasi password */
		$password = [
			'username' => App::$loggedInUser->user,
			'password' => $parameter['myPassword']
		];

		(new Login)->verifyPassword($password);


		/** Cek Level */
		$isAdmin = $this->checkLevel(allowed: ['administrator', 'superadmin']);

		if ( ! isset( $parameter['a_user_id'] ) ) {
			App::respon([
				"status" => "gagal",
				"pesan" => "Parameter Kurang!"
			]);
		}

		$userId     = $parameter['a_user_id'];
		$update     = [];
		$level      = [];
		$kantor     = null;
		$jabatan    = null;
		$errors     = [];
		$newPassword = null;
		$needReturn = $parameter['needReturn'] ?? false;


		/**
		 * Update biodata
		 */
		if ( isset( $parameter['a_display_name'] ) ) {
			$update['display_name'] = strtoupper( $parameter['a_display_name'] );
		} else {
			$errors[] = "Nama lengkap tidak boleh kosong!";
		}

		if ( isset( $parameter['a_email'] ) ) {
			$update['email'] = $parameter['a_email'];
		} else {
			$errors[] = "Email tidak boleh kosong!";
		}

		if ( isset( $parameter['a_gender'] ) && in_array( $parameter['a_gender'], ["M", "F"] ) ) {
			$update['gender'] = $parameter['a_gender'];
		} else {
			$errors[] = "Gender User harap dipilihkan!";
		}

		if ( isset( $parameter['a_handphone'] ) ) {
			$update['handphone'] = $parameter['a_handphone'];
		} else {
			$errors[] = "Nomor handphone harap diisi!";
		}

		/** Pastikan user di non-aktif kan bukan Superadmin */
		if ( ! isset( $parameter['editMyProfile']) ) {
			if ( isset( $parameter['a_active'] ) ) {
				$update['active'] = $parameter['a_active'];
			} else {
				if ( in_array( $userId, $this->levelAdmin ) ) {
					App::respon([
						"status" => "gagal",
						"pesan" => "User level Admin tidak bisa di non-aktifkan!"
					]);
				}
	
				$update['active'] = 0;
			}
		}


		$updateLevel = false;
		$updateKantor = false;
		$updateJabatan = false;
		$updatePassword = false;


		if ($isAdmin) {
			/**
			 * Update level?
			 */
			if (isset($parameter['permission_id']) && count($parameter['permission_id']) != 0) {
				$updateLevel = true;

				$level['levels'] = $parameter['permission_id'];
			}

			/**
			 * Update Kantor?
			 */
			if (isset($parameter['f_nama'])) {
				$updateKantor = true;

				$kantor['kantor'] = $parameter['f_nama'];
			}

			/**
			 * Update Jabatan?
			 */
			if (isset($parameter['e_nama'])) {
				$updateJabatan = true;

				$jabatan['jabatan'] = $parameter['e_nama'];
			}
		}


		/**
		 * Dia tukar password?
		 */
		if ( isset($parameter['new_password']) && $parameter['new_password'] != '') {
			if ($parameter['new_password'] == $parameter['myPassword']) {
				App::responGagal("Password yang akan dirubah tidak beda dengan password yang ada sekarang.");
			} else {
				if (Validate::password($parameter['new_password'], self::JML_KRITERIA_PASSWORD) == 'valid') {
					$newPassword = $this->generatePassword($parameter['new_password']);

					$updatePassword = true;
				} else {
					App::responGagal("Password baru mesti memenuhi " . self::JML_KRITERIA_PASSWORD . " kriteria keamanan password 
					(Penuhi " . self::JML_KRITERIA_PASSWORD . " diantara ini: minimal 5 karakter, 1 huruf besar, 1 angka, 1 karakter khusus )
					");
				}
			}
		}



		/**
		 * Memulai update
		 */
		$riwayatTxt = '';

		DB::getInstance()->beginTransaction();

		try {
			$resultUpdate = $this->model->putUser( $userId, $update );

			if ( $resultUpdate['affected'] < 0 || $resultUpdate['affected'] === false ) throw new \Exception("Tidak bisa update Biodata user.");


			/** Update Kelompok */
			if ( $updateLevel && count( $level['levels'] ) > 0 ) {
				$level['user_id'] = $userId;

				$resultLevel = $this->model->putLevel( $level );
			}


			/** Update kantor */
			if ( $updateKantor ) {
				$kantor['user_id'] = $userId;

				$kantor_id = is_int($kantor['kantor']) ? $kantor['$kantor'] : 0;

				$resultKantor = (new \RLA\model\Karyawan)->putKaryawan(user_id: $userId, kantor_id: $kantor_id );
			}


			/** Update Jabatan */
			if ( $updateJabatan ) {
				$jabatan['user_id'] = $userId;

				$jabatan_id     = is_int($jabatan['jabatan']) ? $jabatan['$jabatan'] : 0;

				$resultJabatan = (new \RLA\model\Karyawan)->putKaryawan(user_id: $userId, jabatan_id: $jabatan_id );
			}


			/** Update Password */
			if ( $updatePassword ) {
				$resultPassword = null;

				$resultPassword = $this->model->putUser(user_id: $userId, parameter: ['password' => $newPassword]);
			}


			/**
			 * Update Riwayat
			 */
			$riwayatUser = [
				'updated' => null,
				'levelBaru' => null,
				'hapusLama' => null,
				'tukarPassword' => null,
			];

			if ($resultUpdate['affected'] > 0) {
				$resultUpdate['field'] = array_map( function( $x ) { return str_replace('_',' ', strtoupper($x)); }, $resultUpdate['field'] );
				$riwayatUser['updated'] = implode('|', $resultUpdate['field']);
			}

			if ( $updateLevel ) {
				if ( $resultLevel['tambahBaru'] > 0 || $resultLevel['timpaTidakTerpakai'] > 0 ) {
					$riwayatUser['levelBaru'] = $resultLevel['tambahBaru'] + $resultLevel['timpaTidakTerpakai'];
				}

				if ($resultLevel['hapusLama'] > 0 || $resultLevel['diTukar'] > 0) {
					$riwayatUser['hapusLama'] = $resultLevel['hapusLama'] + $resultLevel['diTukar'];
				}
			}

			if ( $updatePassword && $newPassword ) {
				$riwayatUser['tukarPassword'] = $resultPassword;
			}


			/**
			 * Cek apa ada perubahan,
			 * jika ada tulis riwayatnya
			 */
			if (
				! $riwayatUser['updated']
				&& !$riwayatUser['levelBaru']
				&& !$riwayatUser['hapusLama']
				&& !isset($resultJabatan)
				&& !isset($resultKantor)
				&& !$riwayatUser['tukarPassword']
			) {
				App::respon(
					contentType: ['application/json'],
					result: [
						'status' => 'sukses',
						'pesan' => 'Data sama, tidak ada perubahan disimpan!'
					]
				);
			}

			$tulisRiwayat = new Riwayat();
			$pelakuId = App::$loggedInUser->id;
			$riwayatTxt = [];

			if ( $riwayatUser['updated'] > 0 )   $riwayatTxt[] = 'BIODATA ' . $riwayatUser['updated'];
			if ( $riwayatUser['levelBaru'] > 0 ) $riwayatTxt[] = 'NEW KEL. ' . $riwayatUser['levelBaru'];
			if ( $riwayatUser['hapusLama'] > 0 ) $riwayatTxt[] = 'DEL. KEL. ' . $riwayatUser['hapusLama'];
			if ( $riwayatUser['tukarPassword'] > 0 ) $riwayatTxt[] = 'UPD. PASSWORD';
			if ( isset( $resultJabatan ) ) $riwayatTxt[] = 'UPD. JABATAN';
			if ( isset( $resultKantor ) )  $riwayatTxt[] = 'UPD. KANTOR';

			$riwayatTxt = implode('|', $riwayatTxt );

			if (!$this->postRiwayat(
				$tulisRiwayat,
				[
					'userId' => $userId,
					'riwayat' => 'UUP%UID:' . $pelakuId . ',TXT:' . $riwayatTxt
				]
			)) {
				throw new \Exception("Gagal menyimpan riwayat objek");
			}

			// Riwayat si Pelaku
			if (!$this->postRiwayat(
				$tulisRiwayat,
				[
					'userId' => $pelakuId,
					'riwayat' => 'UU2%UID:' . $userId . ',TXT:' . $riwayatTxt
				]
			)) {
				throw new \Exception("Gagal menyimpan riwayat subjek");
			}

			$respon = [
				'status' => 'sukses',
				'pesan' => 'Berhasil update ' . $riwayatTxt
			];

			DB::getInstance()->commit();
		} catch ( \Exception $e ) {
			DB::getInstance()->rollback();

			$respon = [
				'status' => 'gagal',
				'pesan' => $e->getMessage()
			];
		}


		/**
		 * Respon ke client
		 */
		if ( ! $needReturn ) App::respon( $respon );

		return $respon;
	}

	public function putProfile( array $params = null )
	{
		$parameter = $params['payload'] ?? null;

		if ( ! $parameter ) {
			App::respon(
				contentType: ['application/json'],
				result: [
					'status' => 'gagal',
					'pesan' => 'Parameter kurang!'
				]
			);
		}


		/**
		 * Validasi update diri sendiri
		 */
		if ( $parameter['a_user_id'] != App::$loggedInUser->id ) {
			App::respon(
				contentType: ['application/json'],
				result: [
					'status' => 'gagal',
					'pesan' => 'Hanya bisa edit profile diri sendiri!'
				]
			);
		}


		/**
		 * Validasi password
		 */
		$login = new Login();

		$login->verifyPassword([
			'username' => $parameter['a_user_name'],
			'password' => $parameter['myPassword']
		]);


		/**
		 * Update data
		 */
		if ( isset( $parameter['permission_id'] ) ) unset( $parameter['permission_id' ] );

		$parameter['a_active' ] = 1;
		$parameter['needReturn'] = true;
		$update = [];

		if ( isset( $parameter['new_password'] ) && $parameter['new_password'] != '' ) {
			$update['password'] = $this->putPassword([
				'a_user_id' => $parameter['a_user_id'],
				'password' => $parameter['new_password'],
				'needReturn' => true
			]);
		}

		$update['data'] = $this->put( $parameter );

		if ( $update['password']['result']['status'] == 'sukses'
			|| $update['data']['result']['status'] == 'sukses'
		) {
			$respon['status'] = 'sukses';
		} else {
			$respon['status'] = 'gagal';
		}

		$respon['pesan'] = $update['password']['result']['pesan'] . " " . $update['data']['result']['pesan'];

		App::respon([
			$respon
		]);
	}

	public function putPassword( array $params = null )
	{
		$parameter = $params['payload'];

		if (!$parameter['a_user_id'] || !$parameter['password']) {
			App::respon([
				"status" => "gagal",
				"pesan" => "Parameter Kurang!"
			]);
		}

		$userId = $parameter['a_user_id'];

		$needReturn = $parameter['needReturn'] ?? false;

		$update['password'] = $this->generatePassword( $parameter['password'] );

		$riwayatTxt = '';

		DB::getInstance()->beginTransaction();

		try {
			$resultUpdate = $this->model->putUser($userId, $update);

			if ($resultUpdate['affected'] < 0 || $resultUpdate['affected'] === false) throw new \Exception("Tidak bisa reset password user");


			// Riwayat si Korban
			$tulisRiwayat = new Riwayat();
			$pelakuId = App::$loggedInUser->id;
			$riwayatTxt = 'PASSWORD';

			if ( !$this->postRiwayat(
				$tulisRiwayat,
				[
					'userId' => $userId,
					'riwayat' => 'UUP%UID:' . $pelakuId . ',TXT:' . $riwayatTxt
				]
			) ) {
				throw new \Exception("Gagal menyimpan riwayat objek.");
			}



			// Riwayat si Pelaku
			if (!$this->postRiwayat(
				$tulisRiwayat,
				[
					'userId' => $pelakuId,
					'riwayat' => 'UU2%UID:' . $userId . ',TXT:' . $riwayatTxt
				]
			)) {
				throw new \Exception("Gagal menyimpan riwayat subjek.");
			}

			$respon = [
				'result' => [
					'status' => 'sukses',
					'pesan' => 'Berhasil update ' . $riwayatTxt . "."
				]
			];

			DB::getInstance()->commit();
		} catch (\Exception $e) {
			DB::getInstance()->rollback();

			$respon = [
				'result' => [
					'status' => 'gagal',
					'pesan' => $e->getMessage()
				]
			];
		}

		if ( ! $needReturn ) App::respon($respon);
		
		return $respon;
	}


	/**
	 * New User
	 */
	public function post( array $params ) 
	{
		$path = $params['path'];

		$parameter = $params['payload'];

		if ($parameter == null) { 
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Parameter kurang!'
			]);
		}

		$params = [
			'user_name' => strtolower( $parameter['a_user_name'] ),
			'display_name' => strtoupper( $parameter['a_display_name'] ),
			'email' => $parameter['a_email'],
			'gender' => $parameter['a_gender'],
			'handphone' => $parameter['a_handphone'],
			'permission_id' => $parameter['permission_id'],
			'help' => $parameter['a_help'] ?? 0,
			'active' => $parameter['a_active'] ?? 0,
			'password' => $parameter['new_password']
		];

		/**
		 * Validasi data
		 */
		$errors = [];
		$permission = [];

		foreach ($params as $key => $value) {
			if ( $key == 'user_name' && Validate::username($value) != 'valid' ) {
				$errors[] = "Username minimal 5 karakter hanya boleh berisi gabungan alfabet, angka, titik atau garis bawah. Misal: 'user.saya', 'usersaya_1";
			}

			if ( $key == 'display_name' && Validate::displayName($value) != 'valid' ) {
				$errors[] = "Nama hanya boleh berisi karakter. Contoh benar: 'Nama Saya'";
			}

			if ( $key == 'email' && $value != '' && Validate::email($value) != 'valid' ) {
				$errors[] = "Penulisan email tidak sah. Contoh benar: email.saya@gmail.com";
			}

			if ( $key == 'handphone' ) {
				if ( Validate::handphone($value) != 'valid' ) {
					$errors[] = "Nomor Handphone tidak memenuhi kriteria. Contoh benar: '0812341234'";
				} else {
					$jadikan62 = true;

					$params['handphone'] = Validate::handphone($value, $jadikan62);
				}
			}

			if ( $key == 'permission_id' ) {
				$permission	= $value;

				unset( $params['permission_id'] );
			}

			if ( $key == 'password' ) {
				if ( Validate::password($value) == 'valid' ) {
					$params['password'] = $this->generatePassword( $value );
				} else {
					$errors[] = Validate::$error;
				}
			}
		}


		/**
		 * Cek error
		 */
		if ( count( $errors) > 0 ) {
			App::responGagal(implode('. ', $errors) );
		}


		/**
		 * Mulai menyimpan
		 */
		DB::getInstance()->beginTransaction();

		try {
			/**
			 * Simpan user
			 */
			$newID = '';
			$pesan = [];

			$newID = $this->model->postUser(
				$params['user_name'],
				$params['display_name'],
				$params['email'],
				$params['password'],
				$params['handphone'],
				$params['active'],
				$params['gender'],
				$params['help']
			);
			
			if ( $newID === null ){
				throw new \Exception("Gagal menyimpan user baru!");
			}

			$pesan[] = "Berhasil menyimpan User baru.";

			/**
			 * Simpan Level
			 */
			if ( count( $permission ) > 0 ) {
				if ( ! $this->model->postUserLevel( $permission, $newID ) ) {
					if ( count( $this->model->errors ) > 0 ) {
						$this->errors = $this->model->errors;
	
						throw new \Exception("Gagal menyimpan Level.");
					}
				}

				$pesan[] = "Berhasil update Level.";
			}

			DB::getInstance()->commit();

			$affected = null;
			if ( $this->model->pesan ) $affected = $this->model->pesan;

			$respon = [
				'result' => [
					'status' => 'sukses',
					'pesan' => $pesan,
					'affected' => $affected
				]
			];
		} catch ( \Exception $e ) {
			DB::getInstance()->rollback();

			$respon = [
				'result' => [
					'status' => 'gagal',
					'pesan' => $e->getMessage(),
					'error' => $this->errors
				]
			];
		}

		/**
		 * Catat Riwayat
		 */
		$tulisRiwayat = new Riwayat();
		$this->postRiwayat( 
			$tulisRiwayat, 
			[
				'userId' => $newID,
				'riwayat' => 'UNE%UID:' . App::$loggedInUser->id
			]
		);

		App::respon( $respon );
	}


	/**
	 * Simpan Riwayat
	 */
	private function postRiwayat( Riwayat $tulisRiwayat, $parameter )
	{
		if (!$parameter['userId']) return false;
		
		if (!$parameter['riwayat']) return false;

		if (!$tulisRiwayat->postRiwayat(
			[
				'table' => '_user_riwayat',
				'key_value' => $parameter['userId'],
				'riwayat' => [
					'tm' => date('Y-m-d H:i:s'),
					'rw' => $parameter['riwayat']
				],
			]
		)) {
			return false;
		}

		return true;		
	}


	/**
	 * Check Level yg akses method itu
	 * @param array $allowed harus lowercase
	 * @return bool
	 */
	public function checkLevel( array $allowed ): bool
	{
		$granted = false;

		foreach (App::$loggedInUser->permissions as $permission) {
			if ( in_array( strtolower($permission), $allowed ) ) {
				$granted = true;

				break;
			}
		}

		return $granted;
	}

}
?>