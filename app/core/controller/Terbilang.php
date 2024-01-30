<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

class Terbilang
{
	public $awal;
	public $koma;
	public $respon;
	public $responDgnKoma;
	private $isMinus = false;
	private $minus = '';
	private $ones = ['', 'SATU', 'DUA', 'TIGA', 'EMPAT', 'LIMA', 'ENAM', 'TUJUH', 'DELAPAN', 'SEMBILAN', 'SEPULUH'];
	private $tens = ['', 'SEPULUH', 'DUA PULUH', 'TIGA PULUH', 'EMPAT PULUH', 'LIMA PULUH', 'ENAM PULUH', 'TUJUH PULUH', 'DELAPAN PULUH', 'SEMBILAN PULUH'];

	private function convertBillions($num)
	{
		if ($num >= 1000000000) {
			return $this->convertBillions(floor($num / 1000000000)) . " MILYAR " . $this->convertMillions($num % 1000000000);
		} else {
			return $this->convertMillions($num);
		}
	}

	private function convertMillions($num)
	{
		if ($num >= 1000000) {
			return $this->convertMillions(floor($num / 1000000)) . " JUTA " . $this->convertThousands($num % 1000000);
		} else {
			return $this->convertThousands($num);
		}
	}

	private function convertThousands($num)
	{
		if ($num >= 1000) {
			return $this->convertHundreds(floor($num / 1000)) . " RIBU " . $this->convertHundreds($num % 1000);
		} else {
			return $this->convertHundreds($num);
		}
	}

	private function convertHundreds($num)
	{
		if ($num > 99) {
			return $this->ones[floor($num / 100)] . " RATUS " . $this->convertTens($num % 100);
		} else {
			return $this->convertTens($num);
		}
	}

	private function convertTens($num)
	{
		if ($num <= 10) {
			return $this->ones[$num];
		} elseif ($num > 10 && $num < 20) {
			return $this->ones[$num % 10] . ' BELAS';
		} else {
			return $this->tens[floor($num / 10)] . " " . $this->ones[$num % 10];
		}
	}

	public function __construct(float $number)
	{
		if ($number == 0) {
			echo "-";
		} else {
			if ($number < 0) {
				$number           = abs($number);
				$this->isMinus = true;
				$this->minus   = 'MINUS ';
			}
			$this->awal = $number;

			$number = floor($number);

			$this->koma = ($this->awal - $number) * 100;
			$this->koma = $this->convertTens($this->koma);

			if ($this->koma != '') {
				$this->koma = ' KOMA ' . $this->koma;
			}

			$this->respon        = $this->convertBillions($number);
			$this->respon        = str_replace('SATU PULUH', 'SEPULUH', $this->respon);
			$this->respon        = str_replace('SATU BELAS', 'SEBELAS', $this->respon);
			$this->respon        = str_replace('SATU RATUS', 'SERATUS', $this->respon);
			$this->responDgnKoma = $this->minus . $this->respon . $this->koma;
		}
	}

	public function __toString()
	{
		return $this->respon;
	}
}
?>