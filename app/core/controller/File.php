<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
class File {
	// public $path = 'controller/Import';
	public $path = 'view' .DS. 'images';

	public string $copyPath;

	private string $date;

	public array $fileName;

	public function __construct()
	{
		$this->date = date('ym') . DS . date('d');
	}

	public function copyFile( 
		array $source, 
		string $target, 
		string $filename 
	) : bool {
		$this->copyPath = $this->path . DS . $target . DS . $this->date;

		$this->pastikanFolderAda($this->copyPath);

		$urut = 0;

		foreach ($source as $key => $file) {
			$ext = explode('.', $file['full_path']);

			$ext = end($ext);

			$urutan = '';

			if ( $urut > 0 ) $urutan = sprintf("_%02d", $urut);

			$sourceFile = $file['tmp_name'];

			$newFile = $this->copyPath . DS . $filename . $urutan . "." . $ext;

			if ( ! move_uploaded_file( $sourceFile, $newFile ) ) {
				return false;
			}

			$this->fileName[ $filename ][ $urut ] = $this->date . DS . $filename . "." . $ext;

			$urut++;
		}

		return true;
	}

	private function pastikanFolderAda( string $path )
	{
		if (is_dir( $path ) ) return true;

		$prev_path = substr($path, 0, strrpos($path, DS, -2) + 1);

		$return = $this->pastikanFolderAda($prev_path);

		return ($return && is_writable($prev_path)) ? mkdir($path) : false;
	}
}