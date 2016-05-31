<?php

namespace Phase\Router\Http;

class Middleware extends Request implements MiddlewareInterface {
    /**
    * Gateway from middleware to applications
    * Every route withour middleware defaults to
    * this middleware handle
    * @return boolean
    */
    public function handle() {
        return true;
    }

}
