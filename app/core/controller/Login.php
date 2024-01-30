<?php
namespace core\controller;

//use core\model\User as Model;

use core\model\User as Model;
use SPG\controller\LoginMember;

defined('APP_OWNER') or exit('No direct script access allowed');

class Login {
	private $user = [];

	private Model $model;

	public function __construct() {
		$this->model = new Model();
	}

	public function index( $params ) 
	{
		$this->signIn( $params );
	}

	/**
	 * @param ['username', 'password']
	 */
	public function verifyPassword( $params )
	{
		$params['username'] = strtolower($params['username']);

		// tarik user
		$this->user = $this->model->getUserDetail($params);

		// cek id
		if (!$this->user['user_id']) {
			App::responGagal( 'Maaf, akun tidak terdaftar.' );
		}

		// cek active
		if ($this->user['active'] != 1) {
			App::responGagal( 'Maaf, akun belum aktif.' );
		}

		// cek password
		$password = $params['password'];
		$password_hash = $this->user['password'];

		if (!password_verify($password, $password_hash)) {
			App::responGagal( 'Maaf, Username atau Password salah.' );
		}

		return true;
	}



	/**
	 * User Login
	 * @param $param ['username', 'password', 'rememberMe']
	 */
	public function signIn( $params )
	{
		$params = $params['payload'];

		// cek apakah login member
		$params_member = $params['member_id'] ?? null;

		if ( $params_member ){
            $login_member_controller = new LoginMember();
            $login_member_controller->LoginMember();
        }
		/**
		 * Cek semua false yg ada
		 */
		if ( ! isset($params['username']) || ! $params['username'] || ! $params['password'] ) {
			App::responGagal( pesan: 'Login ditolak' );
		}

		// cek CSRF
		if ( ! isset( $params['csrf'] ) || 
			 ! App::verifyCSRFtoken( $params['csrf'] ) 
		) {
			App::responGagal(pesan: 'Token CSRF invalid!');
		}

		// verifikasi password
		$this->verifyPassword( $params );

		// update token
		App::setCSRFtoken(force: true);


		// cari ip address => $ip = '127.0.0.1';
		$ip = App::getPC();

		// login diterima

		$result['sukses'] = 1;

		// cek RememberMe nya dan set Cookie
		if( isset( $params['remember_me'] ) ) {
			setcookie( NAME . "_akunku",   $params['username'], time() + 86400 );
			setcookie( NAME . "_passku",   $params['password'], time() + 86400 );
			setcookie( NAME . "_ingataku", 'yes', time() + 86400 );
		}


		// catat riwayat sign-in 
		$riwayat = new Riwayat();

		$result['riwayat'] = $riwayat->postRiwayat(
			[
				'table' => '_user_riwayat',
				'key_id' => 'user_id',
				'key_value' => $this->user['user_id'],
				'riwayat' => [
					'tm' => date('Y-m-d H:i:s'),
					'rw' => 'LIN%IP:' . $ip['host'] . " (" . str_replace(':','=',$ip['ip']) .")"
				],
			]
		);


		// set SESSION
		if ( ! App::setSession( $this->user ) ) {
			App::respon(
				[
					'status' => 'gagal',
					'pesan' => 'Tidak bisa set SESSION.'
				],
				500,
			);
		} else {
			App::setSession( $this->user );

			App::respon([
				'status' => 'sukses',
				'pesan' => 'Login diterima'
			]);
		}
	}
}
?>