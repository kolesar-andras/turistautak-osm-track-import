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

	function __construct ($id) {
		$this->id = $id;
	}

	function dir () {
		return sprintf('tracks/%d/', $this->id);
	}

	function put ($filename, $contents) {
		$dir = $this->dir();
		@mkdir($dir, 0755, true);
		$path = $dir . $filename;
		$ret = @file_put_contents($path, $contents);
		if ($ret === false)
			throw new \Exception(sprintf('Failed to create file: %s', $path));
	}
	
	function touch ($filename, $timestamp) {
		$dir = $this->dir();
		$path = $dir . $filename;
		$ret = touch($filename, $timestamp);
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
		return '.' . $name . '.json';
	}
	
}
