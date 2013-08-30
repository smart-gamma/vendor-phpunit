<?php

namespace LaMelle\PhpUnit\Tester;

class ControllerTest extends ServiceTest
{
  protected $request;
  protected $controller;
  protected $twig;

  function __construct($controller)
  {
          parent::__construct();
          
          $this->request = new Request();
          $this->container->enterScope('request');
          $this->container->set('request', $this->request, 'request');
          
          $this->twig = $this->container->get('twig'); 
          
          $this->controller = new $controller;
          $this->controller->setContainer($this->container);
  }
  
}
