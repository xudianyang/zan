<?php

namespace Zan\Framework\Network\Http;


class RequestHandler {

    private $route;

    public function __construct()
    {
        $this->route = new Router();
    }

    public function onRequest(\swoole_http_request $req, \swoole_http_response $resp)
    {
        $request = $this->requestBuilder($req);

        list($routes, $params) = $this->route->parse($request);

        (new RequestProcessor())->run($routes, $params);
    }

    public function requestBuilder($request)
    {
        $requestBuilder = new RequestBuilder($request);
        $requestBuilder->build();

        return $requestBuilder;
    }

}