<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak letöltése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Download extends Options {

	function process ($id) {
		if ($this->getopt['verbose'])
			echo sprintf('Downloading track id=%d', $id), "\n";

		$url = sprintf('http://turistautak.hu/tracks.php?id=%d&json', $id);
		$data = Query::fetchData($url);
		$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
		$json = json_encode($data, $options);
		$dir = sprintf('tracks/%d/', $data['id']);
		@mkdir($dir, 0755, true);
		$path = $dir . '.turistautak.json';
		$ret = @file_put_contents($path, $json);
		if ($ret === false)
			throw new \Exception(sprintf('Failed to create file: %s', $path));
		
		if (is_array($data['files'])) foreach ($data['files'] as $file) {
			if (!preg_match('/\\.(gdb|mps|plt|wpt|gpx)$/', $file['name']))
				continue;
			$contents = @file_get_contents($file['url']);
			if ($contents === false)
			throw new \Exception(sprintf('Failed to get file for track id=%d: ', $id, $file['name']));
			$path = $dir . $file['name'];
			if (md5($contents) != $file['md5'])
				throw new \Exception('md5 error: ' . $path);
			$ret = @file_put_contents($path, $contents);
			if ($ret === false)
				throw new \Exception(sprintf('Failed to create file: %s', $path));
			$ret = touch($path, strtotime($file['date']));
			if ($ret === false)
				throw new \Exception(sprintf('Failed to set date for file: %s', $path));
		}
	}
}
