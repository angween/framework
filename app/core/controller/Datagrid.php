<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

class Datagrid 
{
	public static array $filterArray;

	public static array $parameter;

	/**
	 * Summary of initParameter
	 * @param mixed $post['page','row','filterRules','sort', 'order']
	 * @return array
	 */
	static function initParameter( $post = [] ) : array
	{
		self::$parameter = [];

		self::$parameter['page'] = $post['page'] ?? 1;
		self::$parameter['limit'] = $post['rows'] ?? 1000;
		self::$parameter['offset'] = (self::$parameter['page']-1)*self::$parameter['limit'];
		self::$parameter['having'] = [];
		self::$parameter['where'] = self::filter( $post['filterRules'] ?? '') ;
		//self::$parameter['filterRules'] = self::$filterArray;
		self::$parameter['sortir'] = '';

		if ( isset( $post['sort'] ) ) {
			if ( substr( $post['sort'], 1 ) == '_' ) $sortir = substr_replace( $post['sort'], '.', 1, 1 );
			else $sortir = $post['sort'];

			$order = $post['order'] ?? 'ASC';

			self::$parameter['sortir'] = $sortir . ' ' . strtoupper( $order );
		} else {
			self::$parameter['sortir'] = '';
		}

		if (empty(self::$parameter['having'])) self::$parameter['having'] = null;

		return self::$parameter;
	}

	public static function filter( $filterRules, string $where = '1' ) : string
	{
		self::$filterArray = [];

		if ( ! $filterRules ) return '1';

		if ( $filterRules == '[]' || empty( $filterRules ) ) return '1';

		if ( ! is_array( $filterRules ) ) $filterRules = json_decode( $filterRules, true );

		$where_t = [];

		foreach($filterRules as $f){
			if( isset($f['value']) && $f['value']!='' ){
				if ( ! is_array( $f['value'] ) ) $f['value'] = stripslashes( $f['value'] );

				if($f['field'][1]=='_')	$f['field'][1]=".";

				if( str_starts_with($f['field'],'having_') ) {
					// $field = str_replace('having_', '', $f['field'] );
					$field = $f['field'];

					self::$parameter['having'][] = $field . " = '" . $f['value'] . "'";

					break;
				}

				if($f['value']=='null') $line=$f['field']." is null";
				elseif($f['value']=='not null') $line=$f['field']." is not null";
				elseif($f['op']=='equal') $line=$f['field']."='".$f['value']."'";
				elseif($f['op']=='less') $line=$f['field']."<'".$f['value']."'";
				elseif($f['op']=='lessequal') $line=$f['field']."<='".$f['value']."'";
				elseif($f['op']=='lessorequal') $line=$f['field']."<='".$f['value']."'";
				elseif($f['op']=='noteq') $line=$f['field']."<>'".$f['value']."'";
				elseif($f['op']=='notequal') $line=$f['field']."<>'".$f['value']."'";
				elseif($f['op']=='bigequal') $line=$f['field'].">='".$f['value']."'";
				elseif($f['op']=='greater') $line=$f['field'].">'".$f['value']."'";
				elseif($f['op']=='greaterorequal') $line=$f['field'].">='".$f['value']."'";
				elseif($f['op']=='in') { 
					if( !is_array( $f['value'] ) ) $value = json_decode( $f['value'] );
					else $value = $f['value'];

					if( is_array( $value ) ) $value = "'" . implode( "','", $value ) . "'";
					else $value = "'". $value ."'";

					$line=$f['field']." in (".$value.")"; }
				elseif($f['op']=='beginwith') $line= $f['field'] . " like '".$f['value']."%'";
				elseif($f['op']=='endwith')   $line= $f['field'] . " like '%".$f['value']."'";
				elseif($f['op']=='between') {
					// jika formwat value between nya berupa array
					if( is_array( $f['value'] ) ) {
						$arr_val = $f['value'];

						if ($arr_val[0] != $arr_val[1]) {
							$line = "(" . $f['field'] . " >= '" . $arr_val[0] . "' and " . $f['field'] . " <= '" . $arr_val[1] . "')";
						} else {
							$line = "date(" . $f['field'] . ") = '" . $arr_val[0] . "'";
						}

					// untuk format yg lain
					} else {
						$arr_val=explode("/",$f['value']);

						if($arr_val[0]!=$arr_val[1]) $line=$f['field'].">='".$arr_val[0]."'"." and ".$f['field']." <='".$arr_val[1]."'";
						else $line="date(".$f['field'].") = '".$arr_val[0]."'";
					}
				} elseif($f['op']=='like!') {
					$line=$f['field']." like '".$f['value']."'";
				} else {
					$line=$f['field']." like '%".$f['value']."%'";
				}			

				$where_t[]=$line;
			}
		}
		// jika ada having
		if (count( self::$parameter['having'] ) > 0){
			self::$parameter['having'] = implode(' AND ', self::$parameter['having']);
		} else {
			self::$parameter['having'] = null;
		}

		// jika where sudah ada di set isi nya - gabung ke array where_t
		if ($where != '1' && $where != '') $where_t[] = $where;

		if (count($where_t) > 1) {
			self::$parameter['filterRules'] = $where_t;

			$where_t = implode(' AND ', $where_t);
		}
		elseif (empty($where_t)) $where_t='1';
		else $where_t=$where_t[0];

		return $where_t;
	}

	public static function limit(int $page = null, int $limit = null ) : int
	{
		if( ! $page || ! $limit ) { return 0; }

		return ($page-1) * $limit;
	}

	public static function addFilter( array $newFilter ) : array
	{
		$newValue = [];
	
		foreach( $newFilter as $filter ) {
			$newValue[] = [
				'field' => $filter['field'],
				'op'    => $filter['op'],
				'value' => $filter['value'],
			];
		}

		return $newValue;
	}
}
?>