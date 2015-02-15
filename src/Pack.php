<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak öszeállítása feltöltésre
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Pack extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Packing track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		$osm = $storage->getData('osm');
		
		if (count($data['files']) > 1 && Options::get('merge')) {
			$files = [];
			foreach ($data['files'] as $file) {
				$files[] = Storage::DIR_OSM . $file['name'] . '.gpx';
			}
			$filename = Storage::DIR_UPL . $osm['name'] . '.gpx';
			Babel::process($id,
				$files,
				$filename
			);
			
			$storage->gzip($filename);
			$storage->delete($filename);

		} else if (count($data['files']) > 1) {
			$files = [];
			foreach ($data['files'] as $file) {
				$filename = Storage::DIR_OSM . $file['name'] . '.gpx';
				$files[] = $filename;
			}
			
			$filename = Storage::DIR_UPL . $osm['name'] . '.zip';
			$storage->zip($filename, $files);

		} else if (count($data['files']) == 1) {
			$file = $data['files'][0];
			$in = Storage::DIR_OSM . $file['name'] . '.gpx';
			$out = Storage::DIR_UPL . $osm['name'] . '.gpx.gz';
			$storage->gzip($in, $out);
			$filename = $out;
		}

		$osm['filename'] = $filename;
		$storage->putData('osm', $osm);
	}	
}
