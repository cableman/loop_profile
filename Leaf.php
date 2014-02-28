<?php

class Leaf extends LoopNode {
  private $body;

  public function __construct($title, $body) {
    parent::__construct($title);
    $this->body = $body;
  }

  public function getBody() {
    return $this->body;
  }
} 
