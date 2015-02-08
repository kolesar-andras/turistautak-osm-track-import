<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * parancssori felület
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

class CommandLine {

	function process () {
		$getopt = new Getopt(array(
			Option::create(null, 'id', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('numeric track id at turistautak.hu'),

			Option::create(null, 'userid', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('numeric user id at turistautak.hu'),

			Option::create(null, 'username', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('user name at turistautak.hu'),

			Option::create(null, 'osm-username', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('OSM username for upload'),

			Option::create(null, 'osm-password', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('OSM password for upload'),

			Option::create(null, 'dev', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('use OSM development API'),

			Option::create(null, 'merge', Getopt::NO_ARGUMENT)
				->setDescription('merge files into single gpx file'),

			Option::create(null, 'merge-all', Getopt::NO_ARGUMENT)
				->setDescription('merge all processed files into a single gpx file'),

			Option::create(null, 'merge-regions', Getopt::NO_ARGUMENT)
				->setDescription('find and merge parts from all regions'),

			Option::create(null, 'crosstrack', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('set treshold for gpsbabel simplify crosstrack'),

			Option::create('1', 'one-by-one', Getopt::NO_ARGUMENT)
				->setDescription('process tracks one by one'),

			Option::create('v', 'verbose', Getopt::NO_ARGUMENT)
				->setDescription('verbose output'),

			Option::create('h', 'help')
				->setDescription('this help'),
		));
		
		Options::init($getopt);

		try {
			$getopt->parse();
		} catch (UnexpectedValueException $e) {
			throw new \Exception($e->getMessage());
		}

		if (!$getopt->getOperands()
			&& !$getopt->getOptions()
			|| $getopt['help']) {
			echo $getopt->getHelpText();
			exit(1);
		}

		$taskManager = new TaskManager;
		$taskManager->process();
	
	}
}
