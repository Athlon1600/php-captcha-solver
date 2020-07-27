<?php

namespace CaptchaSolver;

use Curl\Response;

abstract class JsonResponse
{
    protected $response;

    protected $json = array();
    protected $error_json;

    public function __construct(Response $response)
    {
        $this->response = $response;

        $json = json_decode($response->body, true);

        if (is_null($json)) {
            $this->error_json = sprintf('Invalid JSON response (%s)', json_last_error_msg());
        } else {
            $this->json = $json;
        }
    }

    public function getError()
    {
        if ($this->response->error) {
            return $this->response->error;
        } else if ($this->error_json) {
            return $this->error_json;
        }

        return null;
    }

    /**
     * @return Response
     */
    public function getCurlResponse()
    {
        return $this->response;
    }

    public function toArray()
    {
        return $this->json;
    }

    public function __toString()
    {
        return $this->response->body;
    }
}
