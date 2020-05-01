<?php

namespace zRo;

use Workerman\Worker;
use Workerman\Protocols\Http\Response;
use Workerman\Connection\TcpConnection;
use FastRoute\Dispatcher;

class App extends Worker
{
    /**
     * @var array
     */
    protected $routeCollector = [];

    /**
     * @var FastRouteDispatcher|null
     */
    protected $dispatcher = null;

    /**
     * App constructor.
     * @param string $socketName
     * @param array $contextOption
     */
    public function __construct($socketName = '', array $contextOption = array())
    {
        parent::__construct($socketName, $contextOption);
        $this->onMessage = [$this, 'onMessage'];
    }

    /**
     * @param $path
     * @param $callback
     */
    public function get($path, $callback)
    {
        $this->routeCollector['GET'][] = [$path, $callback];
    }

    /**
     * start
     */
    public function start()
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
           foreach ($this->routeCollector as $method => $callbacks) {
               foreach ($callbacks as $info) {
                   $r->addRoute($method, $info[0], $info[1]);
               }
           }
        });

        \Workerman\Worker::runAll();
    }

    /**
     * @param TcpConnection $connection
     * @param string $request
     * @return null
     */
    public function onMessage($connection, $request)
    {
        static $callbacks = [];
        try {
            $callback = $callbacks[$request->path()] ?? null;
            if ($callback) {
                $connection->send($callback($request));
                return null;
            }

            $ret = $this->dispatcher->dispatch($request->method(), $request->path());
            if ($ret[0] === Dispatcher::FOUND) {
                $callback = $ret[1];
                if (!empty($ret[2])) {
                    $args = array_values($ret[2]);
                    $callback = function ($request) use ($args, $callback) {
                        return $callback($request, ... $args);
                    };
                }
                $callbacks[$request->path()] = $callback;
                $connection->send($callback($request));
                return true;
            } else {
                $connection->send(new Response(404, [], '<h1>404 Not Found</h1>'));
            }
        } catch (\Throwable $e) {
            $connection->send(new Response(500, [], (string)$e));
            echo $e;
        }
    }

}