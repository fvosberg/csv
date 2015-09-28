<?php
namespace Tests\Unit\Rattazonk\CSV;

/**
 * @author Frederik Vosberg <frederik@rattazonk.com>
 */
class CsvTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Rattazonk\CSV\Csv
	 */
	protected $subject = NULL;

	protected function setUp() {
		parent::setUp();
		$this->subject = new \Rattazonk\CSV\Csv();
	}

	/**
	 * @test
	 */
	public function canBeInstantiated() {
		self::assertInstanceOf('\Rattazonk\CSV\Csv', $this->subject);
	}
}
