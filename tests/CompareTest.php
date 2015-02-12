<?php namespace KolesarAndras\TuristautakOsmTrackImport;

class ConcatTest extends \PHPUnit_Framework_TestCase {

	function testSamePoint () {
		
		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="19.87654321" />');
		$point2 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="19.87654321" />');
		$this->assertTrue(Compare::samePosition($point1, $point2));
		$this->assertTrue(Compare::samePoint($point1, $point2));

		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="19.87654321" />');
		$point2 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="20.87654321" />');
		$this->assertFalse(Compare::samePosition($point1, $point2));

		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="19.87654321" />');
		$point2 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="19.87654321"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$this->assertTrue(Compare::samePosition($point1, $point2));
		$this->assertFalse(Compare::sameTime($point1, $point2));
		$this->assertFalse(Compare::samePoint($point1, $point2));

		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="19.87654321"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345678" lon="20.87654321"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$this->assertFalse(Compare::samePosition($point1, $point2));
		$this->assertTrue(Compare::sameTime($point1, $point2));
		$this->assertFalse(Compare::samePoint($point1, $point2));

		// háét számjegyre kell megegyeznie, ráadásul lefelé kerekítve
		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345670" lon="19.87654321"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345679" lon="19.87654324"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$this->assertTrue(Compare::samePosition($point1, $point2));
		$this->assertTrue(Compare::sameTime($point1, $point2));
		$this->assertTrue(Compare::samePoint($point1, $point2));

	}

}

