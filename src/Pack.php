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
		$storage->rmdir(Storage::DIR_UPL);
		$data = $storage->getData('turistautak');
		$osm = $storage->getData('osm');
		
		$todo = [];
		foreach ($data['files'] as $file) {
			if (isset($file['skip'])) continue;
			$todo[] = $file;
		}
		
		if (count($todo) > 1 && Options::get('merge')) {
			$files = [];
			foreach ($todo as $file) {
				$files[] = Storage::DIR_OSM . $file['name'] . '.gpx';
			}
			$filename = Storage::DIR_UPL . $osm['name'] . '.gpx';
			Babel::process($id,
				$files,
				$filename
			);
			
			$storage->gzip($filename);
			$storage->delete($filename);
			$filename = $filename . '.gz';

		} else if (count($todo) > 1) {
			$files = [];
			foreach ($todo as $file) {
				$filename = Storage::DIR_OSM . $file['name'] . '.gpx';
				$files[] = $filename;
			}
			
			$filename = Storage::DIR_UPL . $osm['name'] . '.zip';
			$storage->zip($filename, $files);

		} else if (count($todo) == 1) {
			$file = $todo[0];
			$in = Storage::DIR_OSM . $file['name'] . '.gpx';
			$out = Storage::DIR_UPL . $osm['name'] . '.gpx.gz';
			$storage->gzip($in, $out);
			$filename = $out;

		} else return; // nincsenek fájljai

		$osm['filename'] = $filename;
		$storage->putData('osm', $osm);
		if (!Options::get('keep-osm') &&
			!Options::get('keep-all'))
			$storage->rmdir(Storage::DIR_OSM);
	}	
}
