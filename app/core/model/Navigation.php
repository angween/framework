<?php
namespace RLAtech\model;

defined('APP_OWNER') or exit('No direct script access allowed');

// use RLAtech\controller\App;
use RLAtech\controller\DB as DB;

class Navigation 
{
	const LVL_ADMIN = 1;

	public $result;

	public function get( array $parameter ) : ?array 
	{
		$where = $parameter['where'];
		$offset = $parameter['offset'];
		$limit = $parameter['limit'];

		$query = "SELECT IFNULL(c.name,' ') c_name, IFNULL(b.name,' ') b_name, 
				a.page_id a_page_id, a.page a_page, a.link a_link, a.urutan a_urutan,
				IFNULL(b.icon, a.icon) icon,
				a.active a_active, a.private a_private,
				a.addon_name a_addon_name
			FROM _navigations a
				LEFT JOIN _navigation_groups b USING (group_id)
				LEFT JOIN _navigation_sections c USING (section_id)
				LEFT JOIN `".TBL_PERMISSION_PAGE."` d on d.page_id = a.page_id and d.permission_id > 0
			WHERE $where
			GROUP BY a.page_id
			ORDER BY a.section_id, a.group_id, a.page_id
			LIMIT $offset, $limit";

		$result = DB::getInstance()->query( $query )->fetchAll();

		if ( $result == 0 ) return null;

		if ( count( $result ) == 0 ) return [];

		/* Cari total */
		$query = "SELECT count(*) total FROM (
			SELECT count(*) total
			FROM _navigations a
				LEFT JOIN _navigation_groups b USING (group_id)
				LEFT JOIN _navigation_sections c USING (section_id)
				LEFT JOIN `".TBL_PERMISSION_PAGE."` d on d.page_id = a.page_id 
			WHERE $where
			GROUP BY a.page_id ) a";

		$total['total'] = 0;
		$total = DB::getInstance()->query( $query )->fetchArray();
		
		return [
			'rows' => $result,
			'total' => $total['total']
		];
	}

	public function getMyNavigation( array $permission_id ) : ?array
	{
		if ( empty( $permission_id) ) return null;

		if ( \in_array( self::LVL_ADMIN, $permission_id ) ) {
			$permissionText = "1";	// default untuk WHERE
			$ids = "";
		} else {
			$ids = implode( ',', $permission_id );

			if ( ! $ids ) $ids = 0;

			$permissionText = "d.permission_id IN ( $ids )";
		}

		$query = "SELECT IFNULL(c.name,' ') section, IFNULL(b.name,' ') grup, 
				a.page_id, a.page, a.link, a.urutan,
				IFNULL(b.icon, a.icon) icon
			FROM _navigations a
				LEFT JOIN _navigation_groups b USING (group_id)
				LEFT JOIN _navigation_sections c USING (section_id)
				LEFT JOIN _permission_navigations d on d.page_id = a.page_id
			WHERE $permissionText
				AND a.active = 1
				OR a.private = 0
			GROUP BY a.page_id
			ORDER BY a.section_id, a.group_id, a.urutan";

		$stmt = DB::getInstance()->query( $query );
		
		$result = $stmt->fetchAll();

		if ( $result == 0 ) return null;

		if ( count( $result ) == 0 ) return [];

		return $result;
	}

	private function getLevelAccess( int $level ) 
	{
		$query = "SELECT a.permission_id, a.page_id, b.nama, c.page
			FROM `".TBL_PERMISSION_PAGE."` a 
				JOIN _permissions b USING (permission_id)
				JOIN _navigations c USING (page_id)
			WHERE a.permission_id = ?";

		return DB::getInstance()
			->query( $query, $level )
			->fetchAll();
	}

	public function putAccess( array $parameter )
	{
		if ( empty( $parameter ) ) { $this->result = ["affected"=>0]; return true; }

		// query
		$qUpdate = "UPDATE `".TBL_PERMISSION_PAGE."` SET page_id = ? WHERE permission_id = ? AND page_id = ?";
		$qInsert = "INSERT INTO `".TBL_PERMISSION_PAGE."` SET page_id = ?, permission_id = ?";
		$qTimpa = "UPDATE `".TBL_PERMISSION_PAGE."` SET page_id = ?, permission_id = ? WHERE id = ?";
		$qSetTimpa = "UPDATE `".TBL_PERMISSION_PAGE."` SET permission_id = concat('-', permission_id), page_id = concat('-', page_id) WHERE page_id = ? AND permission_id = ?";

		foreach ($parameter as $permission_id => $levelNavNew) {
			$_levelNavOld = $this->getLevelAccess( $permission_id );

			if ( $_levelNavOld != 0 ) $levelNavOld = \array_column( $_levelNavOld, 'page_id' );
			else $levelNavOld = [];

			sort($levelNavNew);

			$bolehTimpa = $this->cariLevelBolehTimpa();

			if ( ! empty( $bolehTimpa ) ) {
				$timpaMaxID = \min(array_column($bolehTimpa, 'permission_id'));

				$bolehTimpa = \array_column($bolehTimpa, 'id');
			} else {
				$bolehTimpa = null;

				$timpaMaxID = 0;
			}


			// cari beda antara Nav lama dan Nav baru
			$levelBeda = [...array_diff($levelNavOld, $levelNavNew), ...array_diff($levelNavNew, $levelNavOld)];

			$dihapus = [];
			$ditambah = [];

			foreach ($levelBeda as $beda) {
				if ( \in_array( $beda, $levelNavOld ) ) $dihapus[] = $beda;
				else $ditambah[] = $beda;
			}

			// cari angka looping terbanyak
			$loopMax = count( $ditambah );
			if ( count($dihapus) > count( $ditambah ) ) $loopMax = count( $dihapus );

			// siapakan respon variable
			$urutanBolehTimpa = 0;
			$diTukar = 0;
			$timpaTidakTerpakai = 0;
			$tambahBaru = 0;
			$hapusLama = 0;


			// echo "\nDihapus:"; print_r( $dihapus );
			// echo "\nDitambah:"; print_r( $ditambah );
			// echo "\nBoleh Timpa:"; print_r( $bolehTimpa );
			// echo "\nLoopmax:"; print_r( $loopMax ); echo "\n";
			// exit;

			for ($i = 0; $i < $loopMax; $i++) {
				$affected = 0;
				$levelMinus = '-1';

				if (isset($ditambah[$i])) {
					if (isset($dihapus[$i])) {
						$affected = DB::getInstance()
							->query($qUpdate, $ditambah[$i], $permission_id, $dihapus[$i])
							->affectedRows();

						$diTukar += $affected;

					// untuk mengurangi urutan baris di sql yg loncat-loncat
					} elseif (isset($bolehTimpa[$urutanBolehTimpa])) {
						$affected = DB::getInstance()
							->query($qTimpa, $ditambah[$i], $permission_id, $bolehTimpa[$urutanBolehTimpa])
							->affectedRows();

						$urutanBolehTimpa++;

						$timpaTidakTerpakai += $affected;
					} else {
						$affected = DB::getInstance()
							->query($qInsert, $ditambah[$i], $permission_id)
							->affectedRows();

						$tambahBaru += $affected;
					}
				}

				if (isset($dihapus[$i]) && !isset($ditambah[$i])) {
					$timpaMaxID--;

					$affected = DB::getInstance()
						->query($qSetTimpa, $dihapus[$i], $permission_id)
						->affectedRows();

					$hapusLama += $affected ?? 0;
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

	private function cariLevelBolehTimpa() : array
	{
		$query = "SELECT id, permission_id, page_id FROM `".TBL_PERMISSION_PAGE."` WHERE permission_id < 0";

		if ( ! $stmt = DB::getInstance()->query( $query ) ) return [];

		if ( ! $result = $stmt->fetchAll() ) return [];

		return $result;
	}

	private function getNavigationsDariLevel( int $level ) 
	{
		$query = "";
	}


}