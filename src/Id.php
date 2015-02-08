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

class Id extends Options {

	function process ($id) {
		$this->processIds($id);
	}

	function processIds ($ids) {
		if (!is_array($ids)) $ids = array($ids);
		echo implode(', ', $ids), "\n";
	}
}
