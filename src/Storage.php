<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * azonosítók letöltése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Storage {

	var $id;
	
	const DIR_SRC = 'turistautak/';
	const DIR_GPX = 'gpx/';
	const DIR_CMP = 'compare/';
	const DIR_OSM = 'osm/';
	const DIR_UPL = 'upload/';

	function __construct ($id, $create = false) {
		$this->id = $id;
		if (!$create && !is_dir($this->dir()))
			throw new StorageNotFoundException('storage not found');
	}

	function dir () {
		return sprintf('tracks/%d/', $this->id);
	}

	function mkdirname ($filename) {
		$dir = $this->dir() . dirname($filename);
		@mkdir($dir, 0755, true);
	}

	function put ($filename, $contents) {
		$this->mkdirname($filename);
		$path = $this->dir() . $filename;
		$ret = @file_put_contents($path, $contents);
		if ($ret === false)
			throw new \Exception(sprintf('Failed to create file: %s', $path));
	}

	function delete ($filename) {
		$dir = $this->dir();
		$path = $dir . $filename;
		if (!file_exists($path)) return null;
		$ret = @unlink($path);
		if ($ret === false)
			throw new \Exception(sprintf('Failed to delete file: %s', $path));
	}
	
	function rmdir ($dir) {
		$dir = $this->dir() . $dir;
		if (!is_dir($dir)) return;
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) unlink("$dir/$file");
	    return rmdir($dir);
	}

	function touch ($filename, $timestamp) {
		$dir = $this->dir();
		$path = $dir . $filename;
		$ret = touch($path, $timestamp);
		if ($ret === false)
		throw new \Exception(sprintf('Failed to set date for file: %s', $path));
	}

	function get ($filename) {
		$dir = $this->dir();
		$path = $dir . $filename;
		$contents = @file_get_contents($path);
		if ($contents === false)
			throw new \Exception(sprintf('Failed to read file: %s', $path));
		return $contents;
	}
	
	function putData ($name, $data) {
		$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
		$json = json_encode($data, $options);
		$this->put($this->dataFilename($name), $json);
	}
	
	function getData ($name) {
		$json = $this->get($this->dataFilename($name));
		return json_decode($json, true);
	}
	
	function dataFileName($name) {
		return $name . '.json';
	}
	
	function gzip ($in, $out = null) {
		if ($out === null) $out = $in . '.gz';
		$content = $this->get($in);
		$gzip = gzencode($content);
		$this->put($out, $gzip);
	}
	
	function copy ($in, $out) {
		$dir = $this->dir();
		$this->mkdirname($dir . $out);
		$ret = copy($dir . $in, $dir . $out);
		if ($ret === false)
			throw new \Exception(sprintf('Failed to copy file %s to %s', $in, $out));
	}

	function zip ($archive, $files, $delete = false) {
		$this->mkdirname($archive);
		$zip = new \ZipArchive;
		$zip->open($this->dir() . $archive, \ZipArchive::CREATE);
		foreach ($files as $file) {
			$ret = $zip->addFile($this->dir() . $file, basename($file));
			if (!$ret) throw new \Exception('failed to add file to zip');
			if ($delete) $this->delete($file);
		}
		$zip->close();
	}
	
}

class StorageNotFoundException extends \Exception {
}
