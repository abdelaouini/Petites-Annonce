<?php


namespace controller;


class Twig extends Router
{
    protected $twig;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('../src/views/');

        $twig = new \Twig\Environment($loader, [
            'cache' =>false
        ]);

        $twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');

        $this->setTwig($twig);

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * @param mixed $twig
     */
    public function setTwig($twig)
    {
        $this->twig = $twig;
    }
}