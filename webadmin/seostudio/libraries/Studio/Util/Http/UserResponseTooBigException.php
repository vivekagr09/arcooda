<?php

namespace Studio\Util\Http;

use Exception;

class UserResponseTooBigException extends Exception {

    /**
     * @var WebRequest
     */
    protected $request;

    /**
     * Constructs a new `UserResponseTooBigException` instance for the given request.
     *
     * @param WebRequest $request
     * @param string $message
     * @param int $code
     */
    public function __construct(WebRequest $request, $message) {
        parent::__construct($message);
        $this->request = $request;
    }

    /**
     * Returns the request which triggered this exception.
     *
     * @return WebRequest
     */
    public function getRequest() {
        return $this->request;
    }

}
