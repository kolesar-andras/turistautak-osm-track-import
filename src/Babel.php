<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak átalakítása
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.12
 *
 */

class Babel {

	function process ($id, $in, $out, $args=null) {
		$format = Format::formatForFilename($in);
		if ($format == '')
			throw new \Exception('Unknown format for file: ' . $in);

		$storage = new Storage($id);
		$dir = $storage->dir();
		$cmd = sprintf('gpsbabel -i %s -f %s %s -o gpx -F %s 2>&1',
			escapeshellarg($format),
			escapeshellarg($dir . $in), $args,
			escapeshellarg($dir . $out));

		$output = shell_exec($cmd);
		
		if ($output != '') {
			$storage->put('gpsbabel.log', $output);
			echo $cmd, "\n";
			echo $output;
			throw new \Exception('gpsbabel printed errors');
		} else {
			$storage->delete('gpsbabel.log');
		}
	}
}


