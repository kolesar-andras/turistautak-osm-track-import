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
 
class TaskManager extends Options {

	function process () {
		$defaultTasks = array(
			'download',
			'convert',
			'compare',
			'simplify',
			'upload',
		);

		$moreTasks = array(
			'delete',
			'id',
		);

		$allTasks = array_merge($defaultTasks, $moreTasks);

		// ha nem ad meg paracsot, akkor az összeset végrehajtjuk
		if (!$this->getopt->getOperands()) {
			$tasks = $defaultTasks;
		} else {
			$tasks = $this->getopt->getOperands();
		}

		// megnézzük, nincs-e köztük olyan, amit nem ismerünk
		foreach ($tasks as $task) {
			if (!in_array($task, $allTasks)) {
				throw new \Exception(sprintf('Unknown task %s', $task));
			}
		}
		
		// lekérdezzük az azonosítókat
		$query = new Query($this->getopt);
		$ids = $query->process();

		// kifejezetten csak értelmes sorrendben vagyunk hajlandóak végrehajtani
		if ($this->getopt['one-by-one']) {
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
					$taskObject = new $className($this->getopt);
					$taskObject->processIds($ids);
				}
			}
		}
	}
}

