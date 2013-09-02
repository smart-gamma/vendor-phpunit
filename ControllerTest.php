<?php

namespace LaMelle\PhpUnit\Tester;

use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends ServiceTest
{
  protected $request;
  protected $controller;
  protected $twig;

  function __construct($controller, $isTwigEmulation = false)
  {
          parent::__construct();
          
          $this->request = new Request();
          $this->container->enterScope('request');
          $this->container->set('request', $this->request, 'request');
          
          // Mock templating
          if($isTwigEmulation)
          {
            $this->twig = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\Engine')
                               ->setMethods(array('render'))
                               ->getMock();
            
            $this->twig->expects($this->any())
                      ->method('render')
                      ->will($this->returnValue('success'))
                        ;
          }  
          // real render 
          else   
            $this->twig = $this->container->get('twig'); 
          
          $this->controller = new $controller;
          $this->controller->setContainer($this->container);
  }
  
}
