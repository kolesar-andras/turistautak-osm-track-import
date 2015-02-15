<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * feladatok végrehajtása
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */
 
class TaskManager {
	
	const INVERT = 'do-not-';

	function process () {
		$defaultTasks = array(
			'download',
			'tag',
			'convert',
			'compare',
			'simplify',
			'pack',
			'upload',
		);

		$moreTasks = array(
			'delete',
			'id',
		);

		$allTasks = array_merge($defaultTasks, $moreTasks);

		// ha nem ad meg paracsot, akkor az összeset végrehajtjuk
		if (!Options::all()->getOperands()) {
			$tasks = $defaultTasks;
		} else {
			$tasks = Options::all()->getOperands();
		}

		// megnézzük, nincs-e köztük olyan, amit nem ismerünk
		$inverted = false;
		foreach ($tasks as $task) {
			if (substr($task, 0, strlen(self::INVERT)) == self::INVERT) {
				$task = substr($task, strlen(self::INVERT));
				$inverted = true;
			}
			if (!in_array($task, $allTasks)) {
				throw new \Exception(sprintf('Unknown task %s', $task));
			}
		}
		
		// azt adta meg, amit nem szeretne végrehajtani
		if ($inverted) {
			$out = [];
			foreach ($defaultTasks as $task)
				if (!in_array(self::INVERT . $task, $tasks)) 
					$out[] = $task;	
			$tasks = $out;
		}

		// lekérdezzük az azonosítókat
		$query = new Query;
		$ids = $query->process();

		// kifejezetten csak értelmes sorrendben vagyunk hajlandóak végrehajtani
		if (Options::get('one-by-one')) {
			$todo = $ids;
		} else {
			$todo = array($ids);
		}
		foreach ($todo as $ids) {
			foreach ($allTasks as $task) {
				if (in_array($task, $tasks)) {
					$className = __NAMESPACE__ . '\\' . ucfirst($task);
					if (!class_exists($className))
						throw new \Exception(sprintf('Not implemented: %s', $task));
					$taskObject = new $className;
					$taskObject->processIds($ids);
				}
			}
		}
	}
}

