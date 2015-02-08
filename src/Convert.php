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
		$dir = $storage->dir();
		foreach ($data['files'] as $file) {
			$format = Format::formatForFilename($file['name']);
			if ($format == '')
				throw new \Exception('Unknown format for file: ' . $file['name']);
			$cmd = sprintf('gpsbabel -i %s -f %s -o gpx -F %s 2>&1',
				escapeshellarg($format),
				escapeshellarg($dir . $file['name']),
				escapeshellarg($dir . $file['name'] . '.gpx'));
			$output = shell_exec($cmd);
			if ($output != '') {
				$storage->put('cgpsmapper.log', $output);
				echo $cmd, "\n";
				echo $output;
				throw new \Exception('cgpsmapper printed errors');
			} else {
				$storage->delete('cgpsmapper.log');
			}
		}
	}
}
