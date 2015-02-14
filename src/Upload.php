<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak feltöltése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Upload extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Uploading track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		$osm = $storage->getData('osm');
		
		if (Options::get('merge')) {
			$files = [];
			foreach ($data['files'] as $file) {
				$files[] = Storage::DIR_OSM . $file['name'] . '.gpx';
			}
			$filename = Storage::DIR_UPL . sprintf('%d', $data['id']) . '.gpx';
			Babel::process($id,
				$files,
				$filename
			);
			
			$storage->gzip($filename);
			$storage->delete($filename);

		} else {
			$filenames = [];
			foreach ($data['files'] as $file) {
				$in = Storage::DIR_OSM . $file['name'] . '.gpx';
				$out = Storage::DIR_UPL . $file['name'] . '.gpx.gz';
				$files[] = $out;
				$storage->gzip($in, $out);
			}
			
			if (count($data['files']) > 1) {
				$filename = Storage::DIR_UPL . sprintf('%d', $data['id']) . '.zip';
				$storage->zip($filename, $files, true);
			}
		}
	}	
}
