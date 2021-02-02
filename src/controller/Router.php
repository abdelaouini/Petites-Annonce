<?php


namespace controller;


class Router
{
    private $router;

    public function __construct()
    {
        $this->setRouter(new \AltoRouter());
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function match()
    {
        $match = $this->router->match();

        if ($match !== false){
            call_user_func($match['target'],$match['params']);
        }
        else{
             require __DIR__.'\..\..\public\notFound.html';
        }
    }

    /**
     * @param mixed $router
     * @return Router
     */
    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

}