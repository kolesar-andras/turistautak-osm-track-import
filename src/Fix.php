<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * Garmin MapSource fájlok javítása, hogy olvassa a gpsbabel
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.15
 *
 */

class Fix {

	function gdb ($file) {

		$out = '';	
		$position = 0;
		$mps_type = self::get_string($file, $position);

		if (substr($mps_type, 0, 4) != 'MsRc')
			throw new \Exception ('Not a Garmin mps/gdb file.');

		if ($mps_type != 'MsRcd') { $gdb = true; } else { $gdb = false; }
		
		$filesize = strlen($file);
		$vege = false;
		
		$out = substr($file, 0, $position);

		while (!$vege && ($position < $filesize)) {
			$blockstart = $position;
			$blocksize = self::get_long($file, $position);
			$type = $file[$position++];
			$change = 0;
			$extra = 0;

			switch ($type) {
				case 'D': // version
					$mps_version = self::get_word($file, $position);
					break;

				case 'A': // about
					if ($gdb) {
						$position = $blockstart + $blocksize + 5;
						$product = self::get_string($file, $position);
						$extra = strlen($product)+1;
					}
					break;
					
				case 'W': // waypoint
					$change = -8;
					break;

				case 'V': // vége
					$vege = true;
					continue;
					
			}
			
			// kiírjuk a blokkot
			$block = substr($file, $blockstart + 5, $blocksize + $change + $extra);
			$out .= pack('V', $blocksize + $change) . $type . $block;
			$position = $blockstart + $blocksize + 5 + $extra;

		}
		return $out;
	}

	function get_long (&$file, &$position) {
		$long = ord($file[$position]) | (ord($file[$position+1]) << 8) | (ord($file[$position+2]) << 16) | (ord($file[$position+3]) << 24);
		$position += 4;
		return $long;
	}

	function get_double (&$file, &$position) {

		$str = $file[$position+0] . $file[$position+1] . $file[$position+2] . $file[$position+3] . $file[$position+4] . $file[$position+5] . $file[$position+6] . $file[$position+7];
		$position += 8;
		$double = unpack('dvalue', $str);
		return $double['value'];
	}

	function get_single (&$file, &$position) {

		$str = $file[$position+0] . $file[$position+1] . $file[$position+2] . $file[$position+3];
		$position += 4;
		$double = unpack('fvalue', $str);
		return $double['value'];
	}

	function get_byte (&$file, &$position) {

		return ord($file[$position++]);

	}

	function get_word (&$file, &$position) {

		$word = ord($file[$position]) | (ord($file[$position+1]) << 8);
		$position += 2;
		return $word;

	}

	function get_string (&$file, &$position) {

		$out = '';
		while (ord($chr = $file[$position++]) != 0) {
			$out .= $chr;
		}
		return $out;
	}

}
