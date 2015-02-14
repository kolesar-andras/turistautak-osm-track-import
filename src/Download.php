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

class Download extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Downloading track id=%d', $id), "\n";

		$url = sprintf('http://turistautak.hu/tracks.php?id=%d&json', $id);
		$data = Query::fetchData($url);
		$storage = new Storage($id);
		$storage->putData('turistautak', $data);
		
		if (is_array($data['files'])) foreach ($data['files'] as $file) {
			if (!Format::isTrackFile($file['name'])) continue;

			$contents = Query::fetchUrl($file['url']);
			if ($contents === false)
			throw new \Exception(sprintf('Failed to get file for track id=%d: ', $id, $file['name']));
			
			if (md5($contents) != $file['md5'])
				throw new \Exception('md5 error: ' . $file['name']);

			$storage->put(Storage::DIR_SRC . $file['name'], $contents);
			$storage->touch(Storage::DIR_SRC . $file['name'], strtotime($file['date']));
		}
	}
}
