<?php

namespace Phase\Router\Request;

class Request {
    /**
     * Returns $_SERVER array
     * @return array
     */
    protected function getRequest() {
        return $_SERVER;
    }

    /**
     * Get request time integer
     * @return int
     */
    protected function getRequestTime() {
        $request = $this->getRequest();
        return $request["REQUEST_TIME"];
    }

    /**
     * Get request method
     * @return string
     */
    protected function getRequestMethod() {
        return $this->getRequest()["REQUEST_METHOD"];
    }
    /**
     * Get request URI
     * @return string
     */
    protected function getRequestURI() {
        return $this->getRequest()["REQUEST_URI"];
    }

    /**
     * Document root of requested path
     * @return string
     */
    protected function getRequestDocumentRoot() {
        return $this->getRequest()["DOCUMENT_ROOT"];
    }

    /**
     * Get directory of project
     * @return string
     */
    protected function getProjectDIR() {
        $documentRoot = explode("/", $this->getRequestDocumentRoot());
        return $documentRoot[count($documentRoot) - 1];
    }
}
