<?php
namespace Tests\Unit\Rattazonk\CSV;

use Rattazonk\CSV\Csv;

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
		$this->subject = new Csv();
	}

	/**
	 * @test
	 */
	public function canBeInstantiated() {
		self::assertInstanceOf('\Rattazonk\CSV\Csv', $this->subject);
	}

	/**
	 * @test
	 */
	public function canReadCsvStringWithOneLine() {
		$this->subject->readString('foo,bar,foo');

		self::assertEquals(['foo','bar','foo'], $this->subject->toArray());
	}

	/**
	 * @test
	 */
	public function canConfigureSeparator() {
		$this->subject->setSeparator(';');

		$this->subject->readString('foo;bar;foo,foo');

		self::assertEquals(['foo', 'bar', 'foo,foo'], $this->subject->toArray());
	}
}
