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
		foreach ($data['files'] as $file) {
			Babel::process($id, $file['name'], $file['name'] . '.gpx');
		}
	}
}
