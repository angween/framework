<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
use core\model\Navigation as Model;

class Navigation {
	public Model $model;

	public $active = "";

	public function __construct()
	{
		$this->model = new Model();
	}

	public function index()
	{
		if ( ! App::view( PATH_CORE_VIEW . 'page/navigation/index.php' ) ) {
			Router::notFound('index');
		}
	}

	public function view( array $payload )
	{
		$params['path'] = $payload['path'];

		$payload = $payload['payload'];

		if (count($params['path']) > 0) {
			$path = 'view' . ucfirst($params['path'][0]);

			if (method_exists($this, $path)) {
				$this->{ $path}($params);
			}
		}
	}

	public function viewNavigations( $parameter = null ) 
	{
		if ( ! App::view( PATH_CORE_VIEW . 'page/navigation/navigation.php' ) ) {
			Router::notFound('Halaman');
		}
	}

	public function viewAccess( $parameter = null ) 
	{
		if ( ! App::view( PATH_CORE_VIEW . 'page/navigation/access.php' ) ) {
			Router::notFound('Halaman');
		}
	}

	private function createNavSection( $menu ) 
	{
		return "<li class='menu-header small text-uppercase'><span class='menu-header-text'>$menu</span></li>";
	}

	private function createNavGrup( array $menu ) 
	{
		return "<a href='javascript:;' class='menu-link menu-toggle'><i class='menu-icon tf-icons ".$menu['icon']."'></i><div data-link='".$menu['grup']."'>".$menu['grup']."</div></a>";
	}

	private function createNavMenu( array $menu, $section, $singleLink = false ) 
	{
		$singleMenu = "";

		$this->active = "";

		if ($section != ' ')
			$idx = $section . ' / ' . $menu['page'];
		else {
			$idx = $menu['page'];
		}

		if ( $singleLink ) $singleMenu = "<i class='menu-icon ".$menu['icon']."'></i>";

		return "<a href='".$menu['link']."' class='menu-link'>$singleMenu
			<div 
				data-link='".$menu['page']."' 
				data-idx='".$idx ."'
				data-section='".$section."'
				data-grup='".$menu['grup']."'>".$menu['page'].
			"</div></a>";
	}

	public function get( array $params = [] )
	{
		$parameter = $params['payload'];

		if ( ! App::userIsAdmin() ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Akses tidak mencukupi.',
				'rows' => [],
				'total' => 0
			]);
		}

		$params = Datagrid::initParameter( $parameter );

		$hasil = $this->model->get( $params );

		if ( empty( $hasil ) ) {
			$hasil = [
				'status' => 'sukses',
				'rows' => [],
				'total' => 0
			];
		} else {
			$hasil['status'] = 'sukses';
		}

		App::respon( $hasil );
	}

	public function getMyNavigation( $params = [] )
	{
		if ( empty( $params ) ) return '';

		$allNavs = $this->model->getMyNavigation( $params );

		if ( is_null( $allNavs ) ) {
			App::respon(
				contentType: ['text/html'],
				result: ''
			);
		}

		/**
		 * Urutkan menu berdasar Section dan Grup nya
		 */
		$sections = array_unique( array_column( $allNavs, 'section') );

		$grup = array_unique( array_column( $allNavs, 'grup') ); 

		$menuArray = [];


		foreach( $allNavs as $idx => $menu ){
			foreach( $sections as $section ){
				if( $menu['section'] == $section ) {
					if( $menu['grup'] != '' ) $menuArray[$section][$menu['grup']][] = $menu;
					else $menuArray[$section][] = $menu;
				}
			}
		}

		/**
		 * Jadikan format HTML
		 */
		$sectionNow = "-";
		$grupNow = "-";
		$menuHTML = "";
		$this->active = "active";

		foreach ($menuArray as $section => $grups) {
			if ( $section == " ") {

				// grups
				foreach ($grups as $grup => $menus) {
					if ( $grup == ' ') {
						foreach( $menus as $menu) {
							$menuHTML .= "<li class='menu-item mb-2 ".$this->active." menu-root'>";
							$menuHTML .= $this->createNavMenu( $menu, $section, true);
							$menuHTML .= "</li>";
						}
					} else {
						$menuHTML .= "<li class='menu-item mb-2 ".$this->active."' no-section>";
						$menuHTML .= $this->createNavGrup( $menus[0] );
						$menuHTML .= "<ul class='menu-sub'>";

						foreach ($menus as $menu) {
							$menuHTML .= "<li class='menu-item mb-2 ".$this->active."' no-section2>";
							$menuHTML .= $this->createNavMenu( $menu, $grup, true);
							$menuHTML .= "</li>";
						}
						$menuHTML .= "</ul>";
						$menuHTML .= "</li>";
					}
				}
			}

			elseif ( $sectionNow != $section ) {
				$sectionNow = $section;

				$menuHTML .= $this->createNavSection( $section );

				// grups
				foreach ($grups as $grup => $menus) {
					if ( $grup == " " ) {
						foreach( $menus as $x => $menu ) {
							$menuHTML .= "<li class='menu-item mb-2 ".$this->active." menu-root'>";
							$menuHTML .= $this->createNavMenu( $menu, $section, true);
							$menuHTML .= "</li>";
						}
						// $menuHTML .= $this->createNavMenu( $menus[0] );
					} else {
						$menuHTML .= "<li class='menu-item mb-2 ".$this->active." menu-root'>";
						$menuHTML .= $this->createNavGrup( $menus[0] );

						$menuHTML .= "<ul class='menu-sub'>";
						foreach ($menus as $idx => $menu) {
							$menuHTML .= "<li class='menu-item mb-2 ".$this->active." anu2'>";
							$menuHTML .= $this->createNavMenu( $menu, $grup );
							$menuHTML .= "</li>";
						}
						$menuHTML .= "</ul>";

						$menuHTML .= "</li>";
					}
				}
			} else {
				$menuHTML .= "<h1>#############oik############</h1>";
			}

			$active = "";
		}

		/* DEBUG */
		// print_r( $menuArray );
		// echo $menuHTML;

		return $menuHTML;
	}

	public function put( array $params = [] ) 
	{
		$path = $params['path'];

		$parameter = $params['payload'];

		if ( empty( $parameter ) ) App::respon([
			'status' => 'gagal',
			'pesan' => 'Parameter kurang.'
		]);

		if ( $path[0] == 'access' ) $this->putAccess( $parameter );
	}

	public function putAccess( array $parameter = [] )
	{
		$laporan = $this->model->putAccess( parameter: $parameter );

		$status  = 'gagal';

		foreach( $laporan as $key => $value ) {
			if ( $value > 0 ) { $status = 'sukses'; }
		}

		App::respon([
			'status' => $status,
			'detail' => $laporan
		]);
	}

	public function checkAccessLevel(
		string $accessId,
		string $path
	) : bool
	{
		if ( ! $accessId || ! $path ) return false;

		$query = "SELECT 1 ada
			FROM `".TBL_PERMISSION_PAGE."` a
			JOIN `".TBL_NAVIGATIONS."` b USING (page_id)
			WHERE $accessId 
			AND b.link = ?;";

		$stmt = DB::getInstance()->query($query, $path);

		$result = $stmt->fetchArray();
		var_dump($result);

		if ( empty( $result ) ) return false;
		return true;
	}



}
?>