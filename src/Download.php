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

		if (!Options::get('do-not-merge-regions')) {
			if (is_array($data['children'])) {
				foreach ($data['children'] as $child) {
					$url = sprintf('http://turistautak.hu/tracks.php?id=%d&json', $child);
					$childdata = Query::fetchData($url);
					foreach ($childdata['files'] as $file) {
						$file['id'] = $childdata['id'];
						$file['region'] = $childdata['region'];
						$data['files'][] = $file;
					}
				}
			}
		}

		if (!is_array($data['files']))
			throw new \Exception('Invalid file list.');
			
		foreach ($data['files'] as $index => $file) {
			if (!Options::get('do-not-merge-regions') && $data['parent']) {
				$data['files'][$index]['skip'] = 'parent';
				continue;
			}

			if (!Format::isTrackFile($file['name'])) {
				$data['files'][$index]['skip'] = 'unhandled file extension';
				continue;
			}

			$contents = Query::fetchUrl($file['url']);
			if ($contents === false)
			throw new \Exception(sprintf('Failed to get file for track id=%d: ', $id, $file['name']));
			
			if (md5($contents) != $file['md5'])
				throw new \Exception('md5 error: ' . $file['name']);

			$storage->put(Storage::DIR_SRC . $file['name'], $contents);
			$storage->touch(Storage::DIR_SRC . $file['name'], strtotime($file['date']));
		}

		$storage->putData('turistautak', $data);

	}
}
