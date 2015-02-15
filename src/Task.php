<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * parancssori paraméterek elérhetősége
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Task {

	function processIds ($ids) {
		if (!is_array($ids)) {
			return $this->process($ids);
		} else {
			foreach ($ids as $id) {
				try {
					$this->process($id);
				} catch (StorageNotFoundException $e) {
					// nem baj, csak nincs ilyen könyvtár, átugorjuk
				}
			}
		}
	}
	
}
