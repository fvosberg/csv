<?php
namespace Rattazonk\CSV\Tests\Unit;

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

		$this->subject->readString('"foo","bar"Ebar,"' . "\n" . 'foo"');

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
			['fo"o', '"fo""o",'],
			['foo', 'foo,'],
			['foo', '"foo",'],
			['fo,o', '"fo,o",'],
			['fo"o', '"fo""o",'],
			['foo"', 'foo",'],
			['fo"o', 'fo"o,'],
			['fo"o', "fo\"o\nbaar"]
		];
	}

	/**
	 * @test
	 * @dataProvider secondFieldValues
	 */
	public function getFollowingFieldByNextFieldInCurrentRow($expected, $input) {
		$this->subject->readString($input);
		$this->subject->getNextFieldInCurrentRow();

		self::assertEquals(
			$expected,
			$this->subject->getNextFieldInCurrentRow()
		);
	}

	public function secondFieldValues() {
		return [
			// expected => input
			['bar', 'foo,bar'],
			['bar', '"foo","bar"'],
			['bar', '"foo",bar'],
			['ba"r', '"fo""o","ba""r"'],
			['ba"r', 'fo"o,ba"r']
		];
	}

	/**
	 * @test
	 * @dataProvider multipleLines
	 */
	public function getNextFieldInCurrentRowReturnsFalseAfterTheLastColumn($expected, $input) {
		$this->subject->readString($input);
		$this->subject->getNextFieldInCurrentRow();
		$this->subject->getNextFieldInCurrentRow();

		self::assertFalse($this->subject->getNextFieldInCurrentRow());
	}

	public function multipleLines() {
		return [
			// expected => input
			['bar', "foo,bar\nbar,foo"],
			['bar', "\"foo\",\"bar\"\n\"bar\",\"foo\""],
			['bar', "\"foo\",bar\nbar,\"foo\""],
			['ba"r', '"fo""o","ba""r"' . "\n" . '"ba""r","fo""o"'],
			['ba"r', 'fo"o,ba"r' . "\n" . 'ba"r,fo"o']
		];
	}

	/**
	 * @test
	 */
	public function canReadFile() {
		$this->subject->readFile(
			$this->getFixturePath('SimpleCommaSeparatedFile')
		);

		self::assertEquals(
			[
				['FirstFirst', 'FirstSecond', 'FirstThird"'],
				['Second"First', 'SecondSecond', 'SecondThird']
			],
			$this->subject->toArray()
		);
	}

	/**
	 * @test
	 */
	public function firstLineAsKeys() {
		$this->subject->setFirstLineAsKeys(TRUE);
		$this->subject->readString(
			'"first col","second col"' . "\n" .
			'"foo","bar"' . "\n" .
			'"baz","bafoo"'
		);

		self::assertEquals(
			['first col' => 'foo', 'second col' => 'bar'],
			$this->subject->getNextLine()
		);

		self::assertEquals(
			['first col' => 'baz', 'second col' => 'bafoo'],
			$this->subject->getNextLine()
		);

		self::assertFalse(
			$this->subject->getNextLine()
		);
	}

	protected function getFixturePath($file) {
		return __DIR__ . "/../Fixtures/$file.csv";
	}
}
