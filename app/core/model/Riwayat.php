<?php
namespace core\model;

defined('APP_OWNER') or exit('No direct script access allowed');

// use core\controller\App;
use core\controller\DB as DB;
class Riwayat
{
	public string $table;

	public string $keyID;

	public string $keyValue;

	//public const TBL_KODERIWAYAT = '__kode_riwayat';

	public $riwayat;

	public function postRiwayat( array $dataRiwayat ) : bool
	{
		$this->table = $dataRiwayat['table'];
		$this->keyValue = $dataRiwayat['key_value'];
		$this->riwayat = $dataRiwayat['riwayat'];

		$riwayat = json_encode( $this->riwayat, true );

		// if ( $this->cekTable() ) {
		//     echo "table ada!";
		// } else {
		//     echo "table tdk ada";
		// }

		$query = "INSERT INTO `$this->table` (id, riwayat) 
			VALUES (?, '[".$riwayat."]' )
			ON DUPLICATE KEY UPDATE 
			riwayat = JSON_MERGE('".$riwayat."', riwayat)";

		if( ! $stmt = DB::getInstance()->query( $query, $this->keyValue ) ) return false;

		if( $stmt->affectedRows() > 0 ) return true;

		return false;
	}

	public function tarikRiwayatDariTable( $table, $keyword )
	{
		$_table = strval( $table );

		$_keyword = $keyword;

		$query = "SELECT * FROM `$_table` WHERE id = ?";

		if( ! $stmt = DB::getInstance()->query( $query, $_keyword ) ) return false;

		return $stmt->fetchAll();
	}

	public function getKodeRiwayat()
	{
		$result = [];

		$hasil = [];

		$query = "SELECT * FROM `".TBL_KODERIWAYAT."`";

		$rows = DB::getInstance()->query( $query )->fetchAll();

		if ( empty( $rows) ) return [];

		foreach( $rows as $row ) {
			$hasil[ $row['kode'] ] = $row['keterangan'];
		}

		return $hasil;
	}

	/**
	 * Tidak jalan !!
	 * Cek apakah table itu exists atau tidak
	 */
	public function cekTable() 
	{
		if ( $this->table == '' ) return false;

		$query = "SELECT 1 FROM `$this->table` LIMIT 1";

		$stmt = DB::getInstance()->query( $query );
		
		if( $stmt === false ) return false;

		$tes = $stmt->fetchAll();
		print_r( $tes );

		if( $stmt->fetchArray() == 1 ) return true;

		return false;
	}
}