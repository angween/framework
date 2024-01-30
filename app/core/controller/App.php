<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\{
	Scheduler,
	Navigation
};

new App;

class App {
	const LVL_ADMIN = 1;

	static $result = null;

	static $tampilError = true;

	static $loggedInUser = null;

	static $contentType = 'json';

	public function __construct()
	{
		// baca INI file
		$this->loadINI();

		// nama2 table
		$this::loadTableNames();

		// skedul harian
		Scheduler::check();

		// baca session
		session_start();

		if ( ! $this->cekSession() ) {
			self::setSession();
		}
	}

	public static function userIsAdmin() : bool
	{
		if ( in_array( self::LVL_ADMIN, self::$loggedInUser->permission_id ) ) return true;

		return false;
	}

	private function loadINI()
	{
		$databaseINI = 'dev_database.ini';

		if ( ! IS_LOCALHOST ) $databaseINI = 'prod_database.ini';

		if ( ! $webINI = parse_ini_file( $databaseINI, true) ) {
			App::respon(
				contentType: ['application/json','charset=utf-8'],
				result: [
					'status' => 'gagal',
					'pesan' => 'Tidak bisa memuat Configurasi!'
				]
			);
		}

		$_inDevelopment = $webINI['app']['IN_DEVELOPMENT'];
		if ( $_inDevelopment ) $_inDevelopment = "?_x=" . date('mHis');
		else $_inDevelopment = '';

		define('NAME',           $webINI['app']['NAME']);
		define('EMAIL',          $webINI['app']['EMAIL']);
		define('KEYWORDS',       $webINI['app']['KEYWORDS']);
		define('HASH_COST',      $webINI['app']['HASH_COST']);
		define('IN_DEVELOPMENT', $_inDevelopment);

		define('COMPANY_NAME',    $webINI['company']['NAME']);
		define('COMPANY_ADDRESS', $webINI['company']['ADDRESS']);
		define('COMPANY_PHONE',   $webINI['company']['PHONE']);

		define('PROGRAM_PATH', $webINI['company']['PROGRAM_PATH']);
		define('DB_HOSTNAME',  $webINI['database']['HOSTNAME']);
		define('DB_USERNAME',  $webINI['database']['USERNAME']);
		define('DB_PASSWORD',  $webINI['database']['PASSWORD']);
		define('DB_DATABASE',  $webINI['database']['DATABASE']);
		define('DB_PORT',      $webINI['database']['PORT']);
		define('SESSION_NAME', $webINI['database']['SESSION']);

		$nav = new Navigation;
	}

	public static function loadTableNames() : bool
	{
		define('TBL_USER',            '_users');
		define('TBL_PERMISSION',      '_permissions');
		define('TBL_CONFIGURATION',   '_configuration');
		define('TBL_USER_PERMISSION', '_user_permission');
		define('TBL_NAVIGATIONS',     '_navigations');
		define('TBL_PERMISSION_PAGE', '_permission_navigations');
		define('TBL_RIWAYAT',         '_user_riwayat');
		define('TBL_KODERIWAYAT',     '_kode_riwayat');
		define('TBL_KECAMATAN',       '__master_kecamatan');
		define('TBL_KABUPATEN',       '__master_kabupaten');
		define('TBL_PROVINSI',        '__master_provinsi');

		return true;
	}

	public static function isAnyEmpty( array $parameter, array $exceptions ) : bool
	{
		foreach ( $parameter as $key => $value ) {
			if ( in_array( $key, $exceptions ) ) continue;

			if ( empty( $value ) ) { return true; }
		}

		return false;
	}

	public static function cekModelFile( $pathFile )
	{
		if ( file_exists( PATH_FILE . $pathFile ) ) {
			require_once( PATH_FILE . $pathFile );

			return true;
		}

		return false;
	}

	public static function setAssetsIsLoaded()
	{
		if ( $_SESSION[SESSION_NAME]->user ) {
			$_SESSION[SESSION_NAME]->assetsLoaded = true;

			self::$loggedInUser->assetsLoaded = true;
		}
	}

	private function cekSession()
	{
		if( isset( $_SESSION[SESSION_NAME] ) ) {
			if ( ! isset( $_SESSION[SESSION_NAME]->user ) ) return false;
			
			self::$loggedInUser = $_SESSION[SESSION_NAME];

			return true;
		}

		return false;
	}

	public static function setSession( array $userdetails = [] ) : bool
	{
		// set session dari saat login
		if ( ! empty( $userdetails ) ) {
			$loggedInUser = new \stdClass;
			$loggedInUser->id = $userdetails['user_id'];
			$loggedInUser->user = $userdetails['user_name'];
			$loggedInUser->name = $userdetails['display_name'];
			$loggedInUser->handphone = $userdetails['handphone'];
			$loggedInUser->email = $userdetails['email'];
			$loggedInUser->help = $userdetails['help'];
			$loggedInUser->jabatan_id = $userdetails['jabatan_id'] ?? '';
			$loggedInUser->kantor_id = $userdetails['kantor_id'] ?? '';
			$loggedInUser->start = time();
			$loggedInUser->permission_id = explode( ',', $userdetails['permission_id'] ?? '' );
			$loggedInUser->permissions = explode( ',', $userdetails['permissions'] ?? 0 );
			$loggedInUser->assetsLoaded = 0;
			$loggedInUser->photo = $userdetails['photo_path'] ?? '';

			self::$loggedInUser = $loggedInUser;

			$_SESSION[SESSION_NAME] = $loggedInUser;

			self::setCSRFtoken();

			return true;
		}

		self::setCSRFtoken();

		return false;
	}

	static function setCSRFtoken( bool $force = false )
	{
		if ( 
			! isset( $_SESSION[SESSION_NAME]->token ) ||
			time() >= $_SESSION[SESSION_NAME]->token_expire ||
			$force
		) {
			if ( isset( $_SESSION[SESSION_NAME]->id ) ) {
				$_SESSION[SESSION_NAME]->token = self::getCSRFtoken();
				$_SESSION[SESSION_NAME]->token_expire = time() + 3600;
			} else {
				$csrf               = new \stdClass;
				$csrf->token        = self::getCSRFtoken();
				$csrf->token_expire = time() + 3600;

				$_SESSION[SESSION_NAME] = $csrf;
			}
		}
	}

	static function getCSRFtoken(): string
	{
		return bin2hex(random_bytes(32));
	}

	static function verifyCSRFtoken( string $token ): bool
	{
		if (
			!isset($_SESSION[SESSION_NAME]->token) ||
			!$_SESSION[SESSION_NAME]->token
		){
			throw new \Exception("Token CSRF tidak di set!");
		}

		if ( $_SESSION[SESSION_NAME]->token !== $token ) return false;

		self::setCSRFtoken();

		return true;		
	}

	static function setContentType( array|string $contentType )
	{
		if ( is_array( $contentType ) ){
			header("Content-Type: " . implode("; ", $contentType ) );
		} else {
			header("Content-Type: $contentType" );
		}
	}

	static function setResult( $params )
	{
		self::$result = $params;
	}

	// App::respon([
	// 	'contentType' => ['application/json','charset=utf-8'],
	// 	'result' => [
	// 		'status' => 'sukses',
	// 		'pesan' => 'Login berhasil'
	// 	]
	// ]);

	/**
	 * Respon ke client dalam bentuk json/view/redirect
	 */
	static function respon(
		string|array $result,
		null|array|string $contentType = null,
		int $responCode = 200,
		string $redirect = null,
	): void {
		http_response_code( $responCode );

		if ( isset($contentType) && ! is_null($contentType) ) {
			self::setContentType($contentType);
		} else {
			self::checkContentType(['application/json']);
		}

		if ( isset( $result ) ) self::setResult( $result );

		if ( isset( $redirect ) ) { header( "Location: $redirect" ); }

		/**
		 * Jika result == file PHP => include
		 * Jika result == html     => echo
		 * Jika result == array    => json encode
		 */
		if ( is_array( self::$result ) ) echo json_encode( self::$result );
		elseif ( str_ends_with( $result, '.php' ) ) { include( self::$result ); }
		else {
			if ( is_string($contentType) ) $contentType = [$contentType];

			if ( isset($contentType) && in_array( 'text/html', $contentType ) ) {				
				echo self::$result;
			} else {
				echo json_encode( self::$result );
			}
		}

		exit;
	}

	static function checkContentType( array|string $contentType = [] )
	{
		if (is_array(Router::$payload['accept'])) {
			$clientAccept = Router::$payload['accept'];
		} 
		elseif (is_string( Router::$payload['accept'] ) ) {
			$clientAccept = implode(',', Router::$payload['accept']);
		}
		else {
			$clientAccept = [];
		}

		$requestAccept = implode(',', $clientAccept );

		$responType = is_array( $contentType ) ? $contentType[0] : $contentType;
	
		if ( str_contains( $requestAccept, '*/*' ) ) {
			if ( $contentType ) { 
				self::setContentType( $contentType );
			} else {
				self::setContentType(['application/json', 'charset=utf-8']);			
			}

			self::$contentType = '*';
		}
		elseif ( str_contains( $requestAccept, $responType ) ) {
			// self::setContentType([, 'charset=utf-8']);
			// self::setContentType(['text/html', 'charset=utf-8']);
			self::setContentType( $requestAccept );

			if ( in_array( 'application/json', Router::$payload['accept'] ) ) {
				self::$contentType = 'json';			
			} else {
				self::$contentType = 'text';
			}
		}
	}

	static function responGagal(
		array|string $pesan,
		array $contentType = [],
		bool $accessDenied = false
	): void	{
		if (is_array(Router::$payload['accept'])) {
			$acceptedRespon = implode(',', Router::$payload['accept']);
		} elseif ( Router::$payload['accept'] != '' ) {
			$acceptedRespon = Router::$payload['accept'];
		} else {
			$acceptedRespon = 'text/html';
		}

		if ( str_contains( $acceptedRespon, 'json' ) ) {
			$result = [
				'status' => 'gagal',
				'pesan' => $pesan,
				'rows' => [],
				'total' => 0
			];
		}

		elseif ( str_contains( $acceptedRespon, 'text') ) {
			$acceptedRespon = 'text/html';

			if (is_array($pesan)) {
				$result = "GAGAL! " . implode(',', array_values( $pesan ) );
			}

			elseif ( is_string($pesan ) ) {
				$result = "GAGAL! " . $pesan;
			}
		} 

		else {
			$result = [
				'status' => 'gagal',
				'pesan' => $pesan,
				'rows' => []
			];
		}


		self::respon(
			responCode: $accessDenied ? 403 : 200,
			contentType: $acceptedRespon,
			result: $result
		);

		// result: [
		// 	'status' => 'gagal',
		// 	'pesan' => is_array($pesan) ? '' : $pesan,
		// 	'result' => is_array($pesan) ? $pesan : [],
		// 	'rows' => [],
		// 	'total' => 0
		// ]
	}

	static function responSukses(
		array|string $pesan, 
		array|string $results = [],
		bool $accessDenied = false
	): void	{
		self::respon(
			result: [
				'status' => 'sukses',
				'pesan' => is_array($pesan) ? '' : $pesan,
				'result' => is_array($results) ? $results : []
			]
		);
	}

	public static function errorHandler(
		int $tipe,
		string $msg,
		?string $filename = null,
		?int $line = null
	){
		$contentType = ['text/html', 'charset=utf-8'];
		$file = '';

		if ( self::$tampilError ) {
			$file = is_null( $file ) ? "" : "(". basename($filename, ".php") .":$line)";
		}

		$msg = "[".$tipe."] ". $msg . " " . $file;

		self::respon([
			'result' => [
				'status' => 'error',
				'pesan' => $msg
			],
			'contentType' => $contentType
		]);
	}


	public static function terbilang(float $number, bool $dgnKoma = false): string 
	{
		$terbilang = new Terbilang(number: $number);

		if ($dgnKoma) return $terbilang->responDgnKoma;
		
		return $terbilang->__toString();
	}


	public static function getPC() : array
	{
		$ip='';

		if( ! empty($_SERVER['HTTP_CLIENT_IP']) ) $ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else $ip = $_SERVER['REMOTE_ADDR'];

		return [
			'host' => gethostbyaddr($ip),
			'ip' => $ip
		];
	}

	public static function myNavigations()
	{
		$accessLevel = self::$loggedInUser->permission_id;

		$nav = new Navigation;

		return $nav->getMyNavigation( $accessLevel );
	}

	public static function view( string $path, array $parameter = [] )
	{
		if ( ! file_exists( $path ) ) {
			return false;
		}

		include( $path );

		exit();
	}

	/**
	 * Buat ID baru dgn uniqID
	 * @param string $prefix Untuk PREFIX depan
	 * @param int $len Jml digit (max:6)
	 * @return string
	 */
	public static function generateNewID(string $prefix = '', int $len = 6): string
	{
		if ($len > 14 || $len < 1) $len = 6;

		$len = -1 * abs($len);

		$urutan = substr(uniqId(), $len);

		return strtoupper($prefix . $urutan);
	}


	public static function deleteFiles( string $path, string $filename ) : bool
	{
		foreach (glob( $path . $filename . "*.*" ) as $file ) {
			unlink( $file );
		}

		return true;
	}


	public static function checkDIR( string $folderPath ) : bool
	{
		if ( file_exists( $folderPath ) ) return true;

		mkdir($folderPath, 0777, true);

		return true;
	}

	public static function checkTable( string $tblName ) : bool
	{
		$query = "SELECT 1 FROM `{$tblName}` LIMIT 1";

		$tes = DB::getInstance()->query( $query );

		if ( $tes === 1146 ) return false;

		return true;
	}

	public static function _404( $path = '' )
	{
		if ( $path ) $path = "/" . $path ."/view/";

		header("Content-Type: text/html");

		header("HTTP/1.1  404 Not Found");

		include(PATH_FILE . "/404.php");

		exit;
	}

	/**
	 * Bersihkan HTML tag
	 * @param string|null $string
	 * @param array|null $save Untuk exclude tag tertentu
	 * @return string
	 */
	public static function stripTags(?string $string, ?array $save = null): string
	{
		if (!$string)
			return '';

		return strip_tags($string, $save);
	}

	public static function stripWhiteSpaces(?string $string): string
	{
		if (!$string)
			return '';

		return preg_replace('/\s+/S', " ", $string);
	}


	public static function viewPhoto(bool $asHTML = true, string $path = '')
	{
		if (!$path)
			$path = self::$loggedInUser->photo;

		if (!$path) {
			echo '<i class="bi bi-person-circle fs-2"></i>';
		} else {
			$path = URL_ROOT . 'app/RLA/Import/poto/' . $path;
	
			if ($asHTML)
				echo "<img src='$path' alt='Photo' class='w-px-40 h-auto rounded-circle'>";
			else
				echo $path;
		}
	}
}
?>