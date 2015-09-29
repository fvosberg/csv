<?php
namespace Rattazonk\CSV;

/**
 * @author Frederik Vosberg <frederik@rattazonk.com>
 */
class Csv {
	/**
	 * field separator
	 * @var string
	 */
	protected $separator = ',';

	/**
	 * field enclosure
	 * @var string
	 */
	protected $enclosure = '"';

	/**
	 * @var string
	 */
	protected $lineTerminator = "\n";

	/**
	 * @var resource
	 */
	protected $resource;

	/**
	 * @var string
	 */
	protected $currentCharacter;

	/**
	 * @var string
	 */
	protected $lastCharacter;

	/**
	 * @param resource $resource
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		$lines = [];
		$currentLine = '';
		while(($char = fgetc($this->resource)) !== FALSE) {
			if($char !== $this->lineTerminator){
				$currentLine .= $char;
			} else {
				$lines[] = $currentLine;
				$currentLine = '';
			}
		}
		// if the last char is not a line terminator the last line must be added 
		// separatly
		if($currentLine) {
			$lines[] = $currentLine;
		}
		foreach($lines as &$line) {
			$line = explode($this->separator, $line);
			foreach($line as &$column) {
				$column = trim($column, $this->enclosure);
			}
		}
		return $lines;
	}

	/**
	 * returns the value of the next field in the current row
	 * if there is no remaining field it returns FALSE
	 *
	 * @return string|FALSE
	 */
	public function getNextFieldInCurrentRow() {
		$field = '';
		$enclosed = FALSE;
		while($this->getNextCharacter()) {
			// this field ends when a separator is found
			if($this->currentCharacter === $this->separator) {
				break;
			}
			if(!$field && $this->currentCharacter == $this->enclosure) {
				$enclosed = TRUE;
				continue;
			}
			if($enclosed && $this->currentCharacter == $this->enclosure && $this->lastCharacter !== $this->enclosure) {
				continue;
			}
			$field .= $this->getCurrentCharacter();
		}
		return $field ? $field : FALSE;
	}

	/**
	 * advances the internal character pointer
	 * and returns the new char
	 *
	 * @return string
	 */
	public function getNextCharacter() {
		$this->lastCharacter = $this->currentCharacter;
		$this->currentCharacter = fgetc($this->resource);
		return $this->currentCharacter;
	}

	/**
	 * @return string
	 */
	public function getCurrentCharacter() {
		return $this->currentCharacter;
	}

	/**
	 * @param string $separator separator for fields
	 */
	public function setSeparator($separator) {
		$this->separator = $separator;
	}

	/**
	 * @param string $enclosure field enclosure
	 */
	public function setEnclosure($enclosure) {
		$this->enclosure = $enclosure;
	}

	/**
	 * @param string $lineTerminator line terminator
	 */
	public function setLineTerminator($lineTerminator) {
		$this->lineTerminator = $lineTerminator;
	}

	/**
	 * @param string $input
	 */
	public function readString($input) {
		$this->resource = fopen("data://text/plain,$input", 'r');
	}
}
