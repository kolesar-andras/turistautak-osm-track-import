<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak átalakítása
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Convert extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Converting track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		$storage->rmdir(Storage::DIR_GPX);
		foreach ($data['files'] as $file) {
			if (isset($file['skip'])) continue;
			Babel::process($id,
				Storage::DIR_SRC . $file['name'],
				Storage::DIR_GPX . $file['name'] . '.gpx'
			);
		}
		if (!Options::get('keep-turistautak') &&
			!Options::get('keep-all'))
			$storage->rmdir(Storage::DIR_SRC);
		
	}
}
