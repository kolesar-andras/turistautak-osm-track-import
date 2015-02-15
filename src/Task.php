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
			return $this->processWithLog($ids);
		} else {
			foreach ($ids as $id) {
				$ret = $this->processWithLog($id);
			}
			return $ret;
		}
	}

	function processWithLog ($id) {
		try {
			return $this->process($id);
		} catch (\Exception $e) {
			$explode = explode('\\', get_class($this));
			$task = array_pop($explode);
			$message = sprintf("[%s %d] %s\n",
				$task, $id, $e->getMessage());
			file_put_contents('php://stderr', $message);
		}
	}	
}
