<?php

namespace LaMelle\PhpUnit\Tester;

use Symfony\Component\HttpFoundation\Request;

/**
 * PhpUnit Extension for Symfony2 controllers unit tests 
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
class ControllerTest extends ServiceTest
{
    protected $request;
    protected $controller;
    protected $twig;

	/**
	 * Initializes a new instance of the ControllerTest class.
	 *
	 * @param string $controller path to controller to test
	 * @param bool   $isTwigEmulation switcher for twig mock emulation or real render
	 */    
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
            // real twig render 
            else   
                $this->twig = $this->container->get('twig'); 

            $this->controller = new $controller;
            $this->controller->setContainer($this->container);
    }
   
}
