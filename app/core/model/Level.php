<?php
namespace RLAtech\model;

defined('APP_OWNER') or exit('No direct script access allowed');

// use RLAtech\controller\App;
use RLAtech\controller\DB as DB;

class Level 
{
	const TBL_PERMISSION = '_permissions';

	public function getLevel( array $parameter )
	{
		if( empty( $parameter ) ) return ["rows"=>0, "total"=>0];

		$where = $parameter['where'];
		$offset = $parameter['offset'];
		$limit = $parameter['limit'];

		$query = "SELECT 
			a.permission_id, a.nama a_nama, a.keterangan a_keterangan, 
			a.kunci_nama a_kunci_nama, a.builtin
			FROM ".self::TBL_PERMISSION." a 
			WHERE $where
			LIMIT $offset, $limit";

		$total = "SELECT count(*) total
			FROM ".self::TBL_PERMISSION." a 
			WHERE $where";

		// cari data 
		if( ! $stmt = DB::getInstance()->query( $query ) ) return false;

		$result['rows'] = $stmt->fetchAll();

		// cari total
		if( ! $stmt = DB::getInstance()->query( $total ) ) return false;

		$result['total'] = $stmt->fetchArray()['total'];

		return $result;
	}

	public function getNavigations( array $parameter )
	{
		$where = $parameter['where'];
		$sortir = $parameter['sortir'];
		$offset = $parameter['offset'] ?? 0;
		$limit = $parameter['limit'] ?? 1000;

		if ( $sortir == '') $sortir = "a.permission_id ASC";

		$query = "SELECT a.permission_id a_permission_id, a.page_id a_page_id, b.nama b_nama, c.page c_page 
			FROM `".TBL_PERMISSION_PAGE."` a
			JOIN _permissions b USING (permission_id)
			JOIN _navigations c USING (page_id)
			WHERE $where
			ORDER BY $sortir";

		if( ! $stmt = DB::getInstance()->query( $query ) ) return ["rows" => [], "total" => 0];
 
		return [ 'rows' => $stmt->fetchAll() ];
	}

	public function put( int $levelId, string $keterangan )
	{
		$query = "UPDATE _permissions 
			SET keterangan = ?
			WHERE permission_id = ?";

		$stmt = DB::getInstance()->query( $query, $keterangan, $levelId );
		$affected = $stmt->affectedRows();

		if ( $affected === false) return false;

		if ( $affected === 0 ) return 0;

		return $affected;
	}
}