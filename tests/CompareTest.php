<?php namespace KolesarAndras\TuristautakOsmTrackImport;

class ConcatTest extends \PHPUnit_Framework_TestCase {

	function testDigits () {
		$this->assertEquals('47.1234567', Compare::osmDigits(47.12345678));
	}

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

		// hét számjegyre kell megegyeznie
		// legfeljebb az utolsóban lötyöghet egyet kerekítés miatt
		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345670" lon="19.87654321"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$point2 = new \SimpleXMLElement('<trkpt lat="47.12345679" lon="19.87654324"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$this->assertTrue(Compare::samePosition($point1, $point2));
		$this->assertTrue(Compare::sameTime($point1, $point2));
		$this->assertTrue(Compare::samePoint($point1, $point2));

		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345670" lon="19.8765433"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$point2 = new \SimpleXMLElement('<trkpt lat="47.12345670" lon="19.8765438"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$this->assertFalse(Compare::samePosition($point1, $point2));

		$point1 = new \SimpleXMLElement('<trkpt lat="47.12345670" lon="19.8765433"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$point2 = new \SimpleXMLElement('<trkpt lat="47.12345670" lon="19.8765434"><time>2010-08-11T19:16:26Z</time></trkpt>');
		$this->assertTrue(Compare::samePosition($point1, $point2));

	}

}

