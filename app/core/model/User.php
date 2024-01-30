<?php
namespace RLAtech\model;

defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\App;
use RLAtech\controller\DB as DB;

class User {
	//const TBL_USER = '_users';

	//const TBL_USER_PERMISSION = '_user_permission_matches';

	//const TBL_PERMISSION = '_permissions';

	//const TBL_RIWAYAT = '_user_riwayat';

	public array $errors = [];

	public $pesan;

	public $result;

	public function postUser( 
		string $user_name,
		string $display_name,
		string $email,
		string $password,
		string $handphone = '',
		int $active = 1, 
		string $gender = 'M',
		int $help = 1
	) {
		$query = "INSERT INTO `".TBL_USER."` SET 
			user_name = ?,
			display_name = ?,
			email = NULLIF(?,''),
			password = ?,
			handphone = ?,
			tgl_mendaftar = NOW(),
			active = ?,
			gender = ?,
			help = ?";

		if( ! $stmt = DB::getInstance()->query( 
			$query, $user_name, $display_name, $email, 
			$password, $handphone, $active, $gender, $help
		) ) {
			return null;
		}

		if( $stmt->affectedRows() ) {
			return $stmt->lastInsertID();
		} else { 
			$this->errors[] = "Gagal menyimpan user baru!";

			return null;
		}
	}

	/**
	 * Level untuk user baru
	 */
	public function postUserLevel( array $parameter, int $newID )
	{
		/**
		 * Siapkan variable
		 */
		$bolehTimpa = $this->cariLevelBolehTimpa();

		$bolehTimpa = array_column( $bolehTimpa, 'id' );

		$qInsert = "INSERT INTO `".TBL_USER_PERMISSION."` SET user_id = ?, permission_id = ?";

		$qTimpa = "UPDATE `".TBL_USER_PERMISSION."` SET user_id = ?, permission_id = ? WHERE id = ?";

		$affected = [
			'timpa' => 0,
			'baru' => 0
		];

		/**
		 * Timpa ID yg tidak terpakai di table
		 */
		if ( count($bolehTimpa) >= 0 ) {
			foreach ($parameter as $idx => $level ) {
				if ( ! isset( $bolehTimpa[$idx] ) || ! isset( $parameter[$idx] ) ) break;

				$timpa_id = $bolehTimpa[$idx];
				if ( ! $stmt = DB::getInstance()->query($qTimpa, $newID, $level, $timpa_id) ) {
					$this->errors[] = "Gagal timpa";
					
					return false;
				}
				
				if ( $stmt->affectedRows() <= 0 ) {
					$this->errors[] = "Timpa no affected";

					return false;
				}

				$affected['timpa']++;

				unset( $parameter[$idx] );
			}
		}

		/**
		 * Jika masih ada, insert baru ke table
		 */
		if ( count( $parameter) == 0 ) return true;

		foreach ($parameter as $idx => $level) {
			if ( ! isset( $parameter[$idx] ) ) break;

			if ( ! $stmt2 = DB::getInstance()->query( $qInsert, $newID, $level ) ) {
				$this->errors[] = "Gagal Insert";	

				return false;
			}

			if ( $stmt2->affectedRows() <= 0 ) {
				$this->errors[] = "Insert no affected";

				return false;
			}

			$affected['baru']++;

			unset( $parameter[$idx] );
		}

		$this->pesan = $affected;

		if ( count( $parameter ) > 0 ) {
			$this->errors[]	= "Masih ada level belum di-insert!";

			return false;
		}
	}

	public function getUserDetail( array $parameter )
	{
		// $tbl_bio = \RLA\controller\Parameters::T_BIODATA;

		if( empty( $parameter ) ) return false;

		$query = "SELECT a.user_id, a.user_name, a.display_name, a.handphone, 
			a.email, a.help, a.active, a.password,
			GROUP_CONCAT(b.permission_id) permission_id, 
			GROUP_CONCAT(c.nama) permissions
			FROM `".TBL_USER."` a 
			LEFT JOIN `".TBL_USER_PERMISSION."` b USING (user_id)
			LEFT JOIN `".TBL_PERMISSION."` c USING (permission_id)
			WHERE a.user_name like ? 
			LIMIT 1";

		if( ! $stmt = DB::getInstance()->query( $query, $parameter['username'] ) ){
			return false;
		}

		return $stmt->fetchArray();
	}

	public function get( array $parameter )
	{
		$result = ["rows"=>[], "total"=>0];

		if ( empty( $parameter ) ) return false;

		$where = $parameter['where'];
		$offset = $parameter['offset'];
		$limit = $parameter['limit'];
		//$rla_parameter = new \RLA\controller\Parameters;

		$query = "SELECT 
				a.user_id a_user_id, a.user_name a_user_name, 
				a.display_name a_display_name, a.handphone a_handphone, 
				a.email a_email, a.help a_help, a.active a_active, 
				GROUP_CONCAT(b.permission_id) permission_id, 
				GROUP_CONCAT(c.nama) c_nama,
				a.tgl_mendaftar a_tgl_mendaftar,
				a.gender a_gender
			FROM `".TBL_USER."` a 
				LEFT JOIN `".TBL_USER_PERMISSION."` b using (user_id)
				LEFT JOIN `".TBL_PERMISSION."` c on c.permission_id = b.permission_id
			WHERE $where
			GROUP BY a.user_id
			LIMIT $offset, $limit";

		$total = "SELECT count(*) total FROM (SELECT a.user_id total
			FROM `".TBL_USER."` a 
			LEFT JOIN `".TBL_USER_PERMISSION."` b using (user_id)
			LEFT JOIN `".TBL_PERMISSION."` c on c.permission_id = b.permission_id
			WHERE $where
			GROUP BY a.user_id) a";

		// cari data 
		if( ! $stmt = DB::getInstance()->query( $query ) ) return false;

		$result['rows'] = $stmt->fetchAll();

		// cari total
		if( ! $stmt = DB::getInstance()->query( $total ) ) return false;

		$result['total'] = $stmt->fetchArray()['total'];

		return $result;
	}

	public function getRiwayat( string $keyword )
	{
		$query = "SELECT a.riwayat 
			FROM _user_riwayat a 
			JOIN _users b on b.user_id = a.id 
			WHERE b.user_name = ? 
			OR b.display_name = ?";
		
		if( ! $stmt = DB::getInstance()->query( $query, $keyword, $keyword ) ) return false;

		return $stmt->fetchArray();
	}

	private function getLevel( $user_id = -1 )
	{
		if ( ! $user_id ) { App::respon( "Parameter kurang!" ); }

		$query = "SELECT a.permission_id FROM ".TBL_USER_PERMISSION." a WHERE a.user_id = ?";

		if ( ! $stmt = DB::getInstance()->query( $query, $user_id ) ) return false;

		if ( ! $rows = $stmt->fetchAll() ) { $this->result = []; }

		$result = $rows;

		return $result;
	}

	public function getUserById( ?int $id = null )
	{
		if( ! $id ) return false;
		
		$query = "SELECT * FROM `".TBL_USER."` WHERE user_id = ? LIMIT 1";

		if( ! $stmt = DB::getInstance()->query( $query, $id ) ) return false;

		return $stmt->fetchArray();
	}

	private function cariLevelBolehTimpa() : array
	{
		$query = "SELECT id, user_id FROM ". TBL_USER_PERMISSION ." WHERE user_id < 0 ORDER BY id";

		if ( ! $stmt = DB::getInstance()->query( $query ) ) return [];

		if ( ! $result = $stmt->fetchAll() ) return [];

		return $result;
	}

	public function putUser( int $user_id = 0, array $parameter = [] )
	{
		if ( ! $user_id || empty( $parameter ) ) {
			App::respon("Parameter kurang!.");
		}

		$affected = 0;

		$result = [];

		$field = [];
		
		foreach ($parameter as $key => $value) {
			$_counter = 0;

			$query = "UPDATE `".TBL_USER."` SET `$key` = NULLIF(?,'') WHERE user_id = ?";

			if ( ! $stmt = DB::getInstance()->query($query, $value, $user_id ) ) {
				return false;
			}

			$_counter = $stmt->affectedRows();
			
			$affected += $_counter;

			if ( $_counter > 0 ) $field[] = $key;
		}

		$result = [
			'affected' => $affected,
			'field' => $field
		];

		return $result;
	}

	public function putLevel( array $parameter )
	{
		if ( empty( $parameter ) ) { $this->result = ["affected"=>0]; return true; }

		$_levelOld = $this->getLevel( $parameter['user_id'] );

		if ( $_levelOld === false ) return false;
		$user_id = $parameter['user_id'];
		$levelNew = $parameter['levels'];
	
		$levelOld = is_array( $_levelOld ) ? $_levelOld : [];
		$levelOld = array_column( $levelOld, 'permission_id' );

		$timpaMaxID = 0;
		$bolehTimpa = $this->cariLevelBolehTimpa();
		if( count( $bolehTimpa ) > 0 && ! empty( $bolehTimpa ) ) $timpaMaxID = \min( array_column( $bolehTimpa, 'user_id') );
		$bolehTimpa = array_column( $bolehTimpa, 'id' );

		// kumpulkan perbedaan level lama dengan yg baru (akan diset)
		$levelBeda = [...array_diff($levelOld, $levelNew ), ...array_diff($levelNew, $levelOld) ];

		// pisah2in
		$dihapus = [];
		$ditambah = [];
		foreach( $levelBeda as $beda){
			if ( in_array( $beda, $levelOld ) ) $dihapus[] = $beda;
			else $ditambah[] = $beda;
		}

		// cari angka looping terbanyak
		$loopMax = count( $ditambah );
		if ( count($dihapus) > count($ditambah) ) $loopMax = count($dihapus);

		// siapkan query
		$qUpdate = "UPDATE `".TBL_USER_PERMISSION."` SET permission_id = ? WHERE user_id = ? AND permission_id = ?";
		$qInsert = "INSERT INTO `".TBL_USER_PERMISSION."` SET user_id = ?, permission_id = ?";
		$qTimpa = "UPDATE `".TBL_USER_PERMISSION."` SET user_id = ?, permission_id = ? WHERE id = ?";
		$qSetTimpa = "UPDATE `".TBL_USER_PERMISSION."` SET user_id = ? WHERE user_id = ? AND permission_id = ?";
		$qSetTimpa2 = "UPDATE `".TBL_USER_PERMISSION."` SET user_id = '-1' WHERE user_id = ? AND permission_id = ?";

		// loop
		$urutanBolehTimpa = 0;

		// siapakan respon variable
		$diTukar = 0;
		$timpaTidakTerpakai = 0;
		$tambahBaru = 0;
		$hapusLama = 0;

		// mulai query
		for ( $i=0; $i<=$loopMax; $i++ ){
			$affected = 0;

			if ( isset( $ditambah[$i] ) ) {
				if ( isset( $dihapus[$i] ) ) {
					$affected = DB::getInstance()
						->query( $qUpdate, $ditambah[$i], $user_id, $dihapus[$i] )
						->affectedRows();

					$diTukar += $affected; 
				
				// untuk mengurangi urutan baris di sql yg loncat-loncat
				} elseif ( isset( $bolehTimpa[$urutanBolehTimpa] ) ) { 
					$affected = DB::getInstance()
						->query( $qTimpa, $user_id, $ditambah[$i], $bolehTimpa[$urutanBolehTimpa] )
						->affectedRows();

					$urutanBolehTimpa++;

					$timpaTidakTerpakai += $affected;
				} else { 
					$affected = DB::getInstance()
						->query( $qInsert, $user_id, $ditambah[$i] )
						->affectedRows();

					$tambahBaru += $affected;
				}
			}

			if ( isset( $dihapus[$i] ) ) {
				if ( $timpaMaxID > 0 ) {
					$timpaMaxID--;

					$affected = DB::getInstance()
						->query( $qSetTimpa, $timpaMaxID, $user_id, $dihapus[$i] )
						->affectedRows();


					$hapusLama += $affected;
				} else {
					$affected = DB::getInstance()
						->query( $qSetTimpa2, $user_id, $dihapus[$i] )
						->affectedRows();

					$hapusLama += $affected;
				}
			}
		}

		$result = [
			'diTukar' => $diTukar,
			'timpaTidakTerpakai' => $timpaTidakTerpakai,
			'tambahBaru' => $tambahBaru,
			'hapusLama' => $hapusLama
		];

		return $result;
	}
}
