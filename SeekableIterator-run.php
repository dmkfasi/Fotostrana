<?php

/**
 * This class shall implement SeekableIterator interface
 * to seek data from large text files (a Gigabyte or so)
 */
Class SeekableIteratorFileReader implements SeekableIterator {

  protected $_fileName = '';
  protected $_blockSize = 1;
  protected $_fileHandle = null;
  protected $_buffer = null;
  protected $_position = 0;
  protected $_isValid = true;

  public function __construct(string $name) {
    if (is_readable($name)) {
      $this->_fileName = $name;
    } else {
      // Invalidate pointer position immediately and throw an exception
      $this->_isValid = false;
      throw new Exception("File {$name} does not exists or inaccessable");
    }
  }

  public function setBlockSize(int $size = 1) {
    if ($size < 1) {
      throw new Exception('Block size must be greater than 0');
    } else {
      $this->_blockSize = $size;
    }
  }

  public function open() {
    // Open file in binary mode and seek at the very beginning
    fopen($this->_filename, 'rb');
  }

  public function close() {
    fclose($this->_fileHandle);
  }

  public function readBlock() {
    $this->_buffer = fread($this->_fileHandle, $this->_blockSize);
  }

  public function prev(): void {
    // Move pointer farther by the block size and then store its location
    if (fseek($this->_fileHandle, -$this->_blockSize) === 0) {
      $this->_position = ftell($this->_fileHandle);
    } else {
      // Otherwise mark the position invalid and throw an exception
      $this->_isValid = false;
      throw new OutOfBoundsException();
    }
  }

  public function current() {
    return $this->_buffer;
  }

  public function key(): \scalar {
    return $this->_position;
  }

  public function next(): void {
    // Move pointer farther by the block size and then store its location
    if (fseek($this->_fileHandle, $this->_blockSize) === 0) {
      $this->_position = ftell($this->_fileHandle);
    } else {
      // Otherwise mark the position invalid and throw an exception
      $this->_isValid = false;
      throw new OutOfBoundsException();
    }
  }

  public function rewind(): void {
    if (rewind($this->fileHandle) === false) {
      // Mark the position invalid and throw an exception
      $this->_isValid = false;
      throw new Exception('Unable to rewind file position');
    }
  }

  public function seek($position): void {
    // Move pointer to an arbitrary position and then store its location
    if (fseek($this->_fileHandle, $position) === 0) {
      $this->_position = $position;
    } else {
      // Otherwise mark the position invalid and throw an exception
      $this->_isValid = false;
      throw new OutOfBoundsException("Position {$position} is out of bound");
    }
  }

  public function valid(): bool {
    return $this->_isValid;
  }

}
