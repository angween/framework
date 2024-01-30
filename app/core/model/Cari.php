<?php
namespace core\model;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
use core\controller\DB as DB;

class Cari
{
	const TBL_CARIPREFIX = '_search_kode';

	public $searchData = [];

	public $searchPattern = [];

	public function __construct()
	{
		if ( empty( $this->searchData ) ) $this->get( [ 'type' => 'pola']);
	}

	public function get( array $parameter )
	{
		if ( $parameter['type'] == 'pola' ) {
			$query = "SELECT * FROM `".self::TBL_CARIPREFIX."` WHERE active = 1";

			$this->searchData = DB::getInstance()->query( $query )->fetchAll();

			$this->searchPattern = array_column( $this->searchData, 'key_code'	);
		}
	}
}