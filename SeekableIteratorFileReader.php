<?php

/**
 * This class shall implement SeekableIterator interface
 * to seek data from large text files (a Gigabyte or so)
 */
Class SeekableIteratorFileReader implements SeekableIterator {

  protected $fileName = '';
  protected $blockSize = 1;
  protected $fileHandle = null;
  protected $buffer = null;
  protected $position = 0;
  protected $isValid = true;

  public function __construct(string $name) {
    if (is_readable($name)) {
      $this->fileName = $name;
    } else {
      // Invalidate pointer position immediately and throw an exception
      $this->isValid = false;
      throw new Exception("File {$name} does not exists or inaccessable");
    }
  }

  public function getBlockSize() {
    return $this->blockSize;
  }

  public function setBlockSize(int $size = 1) {
    if ($size < 1) {
      throw new Exception('Block size must be greater than 0');
    } else {
      $this->blockSize = $size;
    }
  }

  public function open() {
    // Open file in binary mode and seek at the very beginning
    $this->fileHandle = fopen($this->fileName, 'rb');
  }

  public function close() {
    fclose($this->fileHandle);
  }

  public function readBlock() {
    if (!feof($this->fileHandle)) {
      $this->buffer = fread($this->fileHandle, $this->blockSize);
    } else {
      return false;
    }
  }

  private function updatePosition() {
    $this->position = ftell($this->fileHandle);
  }

  public function prev(): void {
    // Move pointer backward by the block size from the
    // current position and then store its location
    if (fseek($this->fileHandle,
            ($this->position - $this->blockSize)) === 0) {
      // Set new location
      $this->updatePosition();
      $this->readBlock();
    } else {
      // Otherwise mark the position invalid and throw an exception
      $this->isValid = false;
      throw new OutOfBoundsException();
    }
  }

  public function current() {
    if (empty($this->buffer)) {
      $this->readBlock();
    }

    return $this->buffer;
  }

  public function key(): int {
    return $this->position;
  }

  public function next(): void {
    // Move pointer farther by the block size and then store its location
    if (fseek($this->fileHandle,
            ($this->position + $this->blockSize)) === 0) {
      // Set new location
      $this->updatePosition();
      $this->readBlock();
    } else {
      // Otherwise mark the position invalid and throw an exception
      $this->isValid = false;
      throw new OutOfBoundsException();
    }
  }

  public function rewind(): void {
    if (rewind($this->fileHandle) === false) {
      // Mark the position invalid and throw an exception
      $this->isValid = false;
      throw new Exception('Unable to rewind file position');
    } else {
      // Set new location
      $this->updatePosition();
    }
  }

  public function seek($position): void {
    // Move pointer to an arbitrary position multiplied
    // by the block size and then store its location
    if (fseek($this->fileHandle, $position * $this->blockSize) === 0) {
      $this->position = $position;
    } else {
      // Otherwise mark the position invalid and throw an exception
      $this->isValid = false;
      throw new OutOfBoundsException("Position {$position} is out of bound");
    }
  }

  public function valid(): bool {
    return $this->isValid;
  }

}
