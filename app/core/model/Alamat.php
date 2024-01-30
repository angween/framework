<?php
namespace core\model;
use core\controller\DB;

defined('APP_OWNER') or exit('No direct script access allowed');

class Alamat
{
	const TBL_PROVINSI = '__master_provinsi';

	const TBL_KABUPATEN = '__master_kabupaten';

	public function get( $parameters ) //: ?array
	{
		if (empty($parameters)) return null;

		$where  = $parameters['where'] ?? '1';
		$sortir = $parameters['sortir'];
		$offset = $parameters['offset'] ?? 0;
		$limit  = $parameters['limit'] ?? 20;

		if ( $sortir == '' ) $sortir = "a.kabupaten_id";

		$query  = "SELECT 
				a.kabupaten_id a_kabupaten_id,
				b.provinsi b_provinsi,
				a.kabupaten a_kabupaten,
				CONCAT(a.kabupaten, ', ', b.provinsi) keterangan
			FROM `".self::TBL_KABUPATEN."` a 
			JOIN `".self::TBL_PROVINSI."` b USING (provinsi_id)
			WHERE $where
			ORDER BY $sortir
			LIMIT $offset, $limit";

		$rows = DB::getInstance()->query($query)->fetchAll();

		if ($rows === false || $rows === 0) return null;

		if ( count($rows) == 0) {
			return [
				'rows' => [],
				'total' => 0
			];
		}

		// Cari total
		$qTotal = "SELECT count(*) total
			FROM `" . self::TBL_KABUPATEN . "` a
			JOIN `" . self::TBL_PROVINSI . "` b USING (provinsi_id)
			WHERE $where";

		$total = DB::getInstance()->query($qTotal)->fetchArray();

		return [
			'rows' => $rows,
			'total' => $total['total']
		];
	}

	public function getKabupaten(?string $kota): ?array
	{
		if ( ! is_null( $kota ) ) {
			$where = "a.kabupaten LIKE '%$kota%'";
		} else {
			$where = "a.provinsi_id = '3'";
		}

		$limit = "LIMIT 50";

		$query = "SELECT * 
			FROM `".TBL_KABUPATEN."` a
			WHERE $where $limit";

		$result = DB::getInstance()->query($query)->fetchAll();

		if ( $result === false || $result === 0 ) return null;

		return $result;
	}

	
	public function getKecamatan(?string $kota): ?array
	{
		if ( ! is_null( $kota ) ) {
			$where = "a.kecamatan LIKE '%$kota%'";
		} else {
			$where = "1";
		}

		$limit = "LIMIT 50";

		$query = "SELECT * 
			FROM `".TBL_KECAMATAN."` a
			WHERE $where $limit";

		$result = DB::getInstance()->query($query)->fetchAll();

		if ( $result === false || $result === 0 ) return null;

		return $result;
	}

	public function getKabupatenID(string $name): ?int
	{
		$query = "SELECT a.kabupaten_id 
			FROM `".TBL_KABUPATEN."` a
			WHERE a.kabupaten = ?";

		$stmt = DB::getInstance()->query($query, $name)->fetchArray();

		if ( $stmt === false || $stmt === 0 ) return null;

		return $stmt['kabupaten_id'];
	}
}