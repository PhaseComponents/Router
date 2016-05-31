<?php

namespace Phase\Router\Http;

class HeaderMessage {
  /**
  * Send header 405 Method Not Allowed
  * @return Boolean
  */
  public function sendHeader405() {
    if( ! headers_sent()) {
        header("HTTP/1.1 405 Method Not Allowed");
    }

    return true;
  }
  /**
  * Send header 404 Not Found
  * @return Boolean
  */
  public function sendHeader404() {
    if( ! headers_sent()) {
        header("HTTP/1.1 404 Not Found");
    }

    return true;
  }
}
