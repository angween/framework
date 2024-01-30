<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
use core\model\Riwayat as Model;

/**
 * Contoh isi $riwayat dalam SQL:
 * [
 *   {"tm":"2022-01-29 01:22:33", "rw":"SMP" },
 *   {"tm":"2022-01-30", "rw":"TRK%MOB:B 9761 KD,UID:1"},
 *   ...
 * ];
 */
class Riwayat
{
	private Model $model;

	protected array $kodeRiwayat = [];

	// protected array $kkodeRiwayat = [
	// 	"LIN" => "Log-In dari %ip%",
	// 	"LOU" => "Log-Out dari sistem",
	// 	"UNE" => "User didaftarkan %uid%",
	// 	"UUP" => "Biodata diperbarui oleh %uid%: %txt%",
	// 	"UU2" => "Memperbarui biodata %uid%: %txt%",
	// 	"ULV" => "Hak akses user %uid% diperbarui %lvl%",
	// 	"AJP" => "%uid% membuat jurnal %id%",
	// 	"AJT" => "%uid% memperbarui jurnal %id%",
	// 	"APJ" => "%uid% buat jurnal baru %id%",
	// 	"AUP" => "%uid% update jurnal %id% pada %txt%",
	// 	"APO" => "%uid% posting jurnal %id%",
	// 	"LUP" => "%uid% update Kelompok %txt%"
	// ];

	protected array $kodePesan = [
		"uid" => "tampilUserName",
		"id" => "tampilLink",
		"txt" => "tampilKeterangan",
		"ip" => "tampilKeterangan",
	];

	public function __construct()
	{
		$this->model = new Model();

		/** Muat semua kode Riwayat */
		if ( empty( $this->kodeRiwayat ) ) {
			$this->kodeRiwayat = $this->model->getKodeRiwayat();
		}
	}

	public function get( $parameter = [] )
	{
		print_r( $parameter );

		exit;
	}

	public function tarikRiwayatDariTable( string $table = '', string $keyword = '' ) : ?array
	{
		if ( $table == '' || $keyword == '' ) return [];

		if ( ! $result = $this->model->tarikRiwayatDariTable( $table, $keyword ) ){
			return [];
		}

		if( count( $result ) > 1 ) {
			return null;
		}

		$result = $result[0];

		$riwayatString = '';

		if ( isset( $result['riwayat'] ) ) {
			$result['riwayat'] = json_decode( $result['riwayat'], true );

			/** Bersihkan riwayat yg terlalu besar */
			$this->purgeRiwayat( $result['riwayat'], $table, $keyword );

			$riwayatString = $this->renderHTMLriwayat( $result['riwayat'] );

			$result['riwayat'] = $riwayatString;

			return $result;
		}
	}

	public function postRiwayat( array $dataRiwayat = [] ) : bool
	{
		if ( empty( $dataRiwayat ) ) return false;
		if ( ! $dataRiwayat['table'] ) return false;
		if ( ! $dataRiwayat['key_value'] ) return false;
		if ( ! $dataRiwayat['riwayat'] ) return false;

		if ( ! $this->model->postRiwayat( $dataRiwayat ) ) return false;

		return true;
	}

	public function renderHTMLumum( array $riwayat )
	{
		$riwayatString = '<dl class="row pt-5">';

		foreach ($riwayat as $_topik => $isi) {
			if ( is_null( $isi ) ) continue;

			$topik = $this->bersihkanUnderscore( $_topik );

			if ( in_array( strtolower( $topik ), ['user id', 'permission id' ] ) ) continue;

			if ( $isi == '' ) continue;

			if ( strpos($isi, ',') !== false ) {
				$isi = str_replace( ",", ", ", $isi );
			}

			$riwayatString .= '<dt class="col-sm-3 text-end text-capitalize">' . $topik . '</dt>';

			$riwayatString .= '<dd class="col-sm-9">' . $isi . '</dd>';
		}

		$riwayatString .= '</dl>';

		return $riwayatString;
	}

	private function bersihkanUnderscore( $string )
	{
		if ( $string[1] == '_' ) {
			$string = substr( $string, 2);
		}

		return str_replace("_", " ", $string );
	}

	public function renderHTMLriwayat( array $riwayat )
	{
		$riwayatString = '<dl class="row pt-2">';

		foreach ($riwayat as $kodeRiwayat) {
			$waktu = '<abbr title="' . substr( $kodeRiwayat['tm'], 11 ) . '">' .substr( $kodeRiwayat['tm'], 0, 10 ).' </abbr>';

			$riwayatString .= '<dt class="col-sm-3 text-end text-capitalize">' . $waktu . '</dt>';

			$riwayatString .= '<dd class="col-sm-9">' . $this->decodeRiwayat($kodeRiwayat['rw']) . '</dd>';
		}

		$riwayatString .= '</dl>';

		return $riwayatString;
	}

	/**
	 * Translate kode-kode riwayat jadi kata-kata yang bisa dibaca
	 */
	private function decodeRiwayat( $riwayat = '' )
	{
		if ( $riwayat == '' ) return '';

		// Ambil kode utama
		$kd_prefix = trim(substr($riwayat, 0, strpos($riwayat, '%'))) ?? $riwayat;

		// Ambil isi
		$kd_isi = explode(',', str_replace('%', '', strstr($riwayat, '%')) );

		if ( ! $kd_prefix ) $kd_prefix = $riwayat;

		if ( ! $pesan = $this->kodeRiwayat[$kd_prefix] ) {
			return "-Kode Riwayat Tidak dikenal-";
		}

		if ( strpos( $pesan, '%') === false ) return $pesan;

		foreach ($kd_isi as $kode) {
			$x = explode( ':', $kode );

			$method = $this->kodePesan[strtolower( $x[0] ) ] ?? '';

			if ( method_exists( $this, $method ) ) {
				$newText = $this->{$method}($x[1]);
			} else {
				$newText = "-";
			}

			$oldKode = '%' . strtolower($x[0]) . '%';

			$pesan = str_replace( $oldKode, $newText, $pesan );
		}

		return $pesan;
	}

	/**
	 * Daftar Decode Masing-masin Kode
	 */
	protected function tampilUserName( $id )
	{
		$user = new User();

		$userInfo = $user->model->get([
			"where" => "a.user_id = $id",
			"offset" => 0,
			"limit" => 1
		]);

		if ( count( $userInfo['rows'] ) > 0 ) {
			$displayName = $userInfo['rows'][0]['a_display_name'];

			return '<a href="#" onclick="App.cari(\'user:'.$displayName.'\')" />'.$displayName.'</a>';
		}

		return '';
	}

	protected function tampilLink( $id )
	{
		return "<a href='#' onclick='App.cari(\"$id\")'>{$id}</a>";
	}

	protected function tampilKeterangan( $txt )
	{
		return "<b>" . strtoupper( str_replace(["|","_"], [", "," "], $txt) ) . "</b>";
	}

	protected function purgeRiwayat( $riwayat, $table, $keyword )
	{
		if ( count( $riwayat ) < 1000 ) return;

		// TODO - bersihkan riwayat yang sudah capai >= 1000 baris
	}
}
?>