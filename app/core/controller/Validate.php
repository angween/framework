<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

class Validate
{
	const PASSWORD_MIN_LENGTH = 5;

	static string $error;

	static int $passwordLength = 5;

	const PASSWORD_SECURITY_POINT = 3;

	static string $polaHP = "/^(08|62)[1-9][0-9]{8,11}$/";

	static string $polaUsername = "/^[a-z0-9._]{5,20}$/";

	static string $polaDisplayname = "/^[a-z ]{5,150}$/";

	static string $polaPassword = "/^[a-z ]{5,150}$/";

	static string $polaKTP = "/^([1-3][0-9])([0-9]{2})([0-9]{2})(0[1-9]|[1-2][0-9]|3[0-1]|4[1-9]|[5-6][0-9]|7[0-1])(0[1-9]|1[012])([0-9]{2})([0-9]{4,5})$/";

	public function __construct(){}

	/**
	 * Summary of handphone
	 * @param mixed $value
	 * @param mixed $jadikan62
	 * @return string 'fail' atau 'valid' atau 62....
	 */
	static function handphone( $value, $jadikan62 = false )
	{
		$result = 'fail';

		$value = str_replace(" ", "", $value);

		if ( preg_match( self::$polaHP, $value ) ) {
			$result = 'valid';

			if ( $jadikan62 ) {
				if ( substr( $value, 0, 3 ) != '628' ) $result = '62' . substr( $value, 1 );
				else return $value;
			}
		}

		return $result;
	}

	static function username( $value )
	{
		$result = 'fail';

		if ( preg_match( self::$polaUsername, strtolower( $value ) ) ) {
			$result = 'valid';
		}

		return $result;
	}

	static function displayName( $value )
	{
		$result = 'fail';

		if ( preg_match( self::$polaDisplayname, strtolower( $value ) ) ) {
			$result = 'valid';
		}

		return $result;
	}

	static function email( string $email ) : ?string
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return 'valid';
		}

		return 'fail';
	}

	static function password( $value, $kriteria = null )
	{
		$result = 'fail';

		$hasLength    = self::PASSWORD_MIN_LENGTH;

		if ( strlen( $value ) < self::$passwordLength ) { $hasLength = 0; }

		$hasUpperCase = (int) preg_match('/[A-Z]/', $value );

		$hasLowerCase = (int) preg_match('/[a-z]/', $value );

		$hasNumbers   = (int) preg_match('/\d/', $value );

		$hasNonalphas = (int) preg_match('/\W/', $value );

		if ( is_numeric( $kriteria ) ) $jumlahSyarat = $kriteria;
		else $jumlahSyarat = self::PASSWORD_SECURITY_POINT + 1;

		if ( $hasLength + $hasUpperCase + $hasLowerCase + $hasNumbers + $hasNonalphas <  $jumlahSyarat ) {
			self::$error = "Password minimal ".self::$passwordLength." karakter atau lebih. Setidaknya penuhi ".self::PASSWORD_SECURITY_POINT." syarat diantara berikut: ada 1 huruf besar, ada 1 huruf kecil atau 1 angka atau 1 karakter spesial.";

			return 'fail';
		}

		return 'valid';
	}

}