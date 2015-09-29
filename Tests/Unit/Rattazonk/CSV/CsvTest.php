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
	 * @dataProvider oneLinerWithComma
	 */
	public function canReadCsvStringWithOneLine($output, $input) {
		$this->subject->readString($input);

		self::assertEquals($output, $this->subject->toArray());
	}

	public function oneLinerWithComma() {
		return [
			[[['foo','bar','foo']], 'foo,bar,foo']
		];
	}

	/**
	 * @test
	 */
	public function canReadCsvStringWithMultipleLines() {
		$lines = [
			'foo,bar,barfoo',
			'"bar","foobar","baz"'
		];

		$this->subject->readString(implode("\n", $lines));

		self::assertEquals([
				['foo', 'bar', 'barfoo'],
				['bar', 'foobar', 'baz']
			],
			$this->subject->toArray()
		);
	}

	/**
	 * @test
	 */
	public function canConfigureSeparator() {
		$this->subject->setSeparator(';');

		$this->subject->readString('foo;bar;foo,foo');

		self::assertEquals([['foo', 'bar', 'foo,foo']], $this->subject->toArray());
	}

	/**
	 * @test
	 */
	public function canReadEnclosedCsvString() {
		$this->subject->readString('"foo","bar","fooo"');

		self::assertEquals([['foo', 'bar', 'fooo']], $this->subject->toArray());
	}

	/**
	 * @test
	 */
	public function canConfigureEnclosure() {
		$this->subject->setEnclosure('&');

		$this->subject->readString('&foo&,&bar&,&fooo&');

		self::assertEquals([['foo', 'bar', 'fooo']], $this->subject->toArray());
	}

	/**
	 * @test
	 */
	public function canConfigureLineTerminator() {
		$this->subject->setLineTerminator('E');

		$this->subject->readString('"foo","bar"E"bar,"' . "\n" . 'foo"');

		self::assertEquals(
			[['foo', 'bar'], ['bar', "\nfoo"]],
			$this->subject->toArray()
		);
	}

	/**
	 * @test
	 */
	public function getNextCharacter() {
		$this->subject->readString('"foo","bar",foobaz');

		self::assertEquals('"', $this->subject->getNextCharacter());
		self::assertEquals('"', $this->subject->getCurrentCharacter());

		self::assertEquals('f', $this->subject->getNextCharacter());
		self::assertEquals('f', $this->subject->getCurrentCharacter());
	}

	/**
	 * @test
	 * @dataProvider firstFieldValues
	 */
	public function getFirstFieldByNextFieldInCurrentRow($expected, $input) {
		$this->subject->readString($input);

		self::assertEquals($expected, $this->subject->getNextFieldInCurrentRow());
	}

	public function firstFieldValues() {
		return [
			// expected => input
			['foo', 'foo,'],
			['foo', '"foo",'],
			['fo"o', '"fo""o",'],
			['fo"o', 'fo"o,']
		];
	}

	/**
	 * @depends getFirstFieldByNextFieldInCurrentRow
	 */
	public function getFollowingFieldByNextFieldInCurrentRow($subject) {
	}
}
