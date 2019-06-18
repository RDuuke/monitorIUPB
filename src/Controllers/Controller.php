<?php
namespace App\Controllers;
use Slim\Container;
class Controller
{
    protected $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function __invoke($request, $response, $args) {
        return $response;
   }

}