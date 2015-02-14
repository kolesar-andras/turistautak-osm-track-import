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
		if (!is_array($in)) $in = [$in];
		$format = Format::formatForFilename($in[0]);
		if ($format == '')
			throw new \Exception('Unknown format for file: ' . $in[0]);

		$storage = new Storage($id);
		$dir = $storage->dir();
		$storage->mkdirname($out);
		$infiles = [];
		foreach ($in as $file) {
			$infiles[] = escapeshellarg($dir . $file);
		}
		$cmd = sprintf('gpsbabel -i %s -f %s %s -o gpx -F %s 2>&1',
			escapeshellarg($format),
			implode(' -f ', $infiles),
			$args,
			escapeshellarg($dir . $out));

		$output = shell_exec($cmd);
		
		$logfilename = $out . '.log';
		if ($output != '') {
			$storage->put($logfilename, $output);
			echo $cmd, "\n";
			echo $output;
			throw new \Exception('gpsbabel printed errors');
		} else {
			$storage->delete($logfilename);
		}
	}
}


