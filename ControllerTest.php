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
    /*
     * App request
     * @var Symfony\Component\HttpFoundation\Request $request
     */    
    protected $request;
 
    /*
     * App session
     * @var Symfony\Component\HttpFoundation\Session $session
     */    
    protected $session;
    
    /*
     * Templating engine
     * @var Symfony\Component\Templating\EngineInterface $twig
     */ 
    protected $twig;  
    
    /*
     * Target test controller
     * @var Symfony\Bundle\FrameworkBundle\Controller\Controller $controller
     */    
    protected $controller;

	/**
	 * Initializes a new instance of the ControllerTest class.
	 *
	 * @param string $controller path to controller to test
	 * @param bool   $isMockEmulation switcher for twig mock emulation or real render
	 */    
    function __construct($controller, $isMockEmulation = false)
    {
            parent::__construct();

            $this->request = new Request();

            $this->session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session', array('getFlashBag','getName', 'isStarted'), array(), '', false);
            
            $this->session->expects($this->any())
                          ->method('getFlashBag')
                          ->will($this->returnValue($this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface')));
            
            $this->request->setSession($this->session);
            $this->container->enterScope('request');
            $this->container->set('request', $this->request, 'request');

            //Mock templating
            if($isMockEmulation)
            {
                $this->twig = $this->getMockBuilder('\Twig_Environment')
                                   ->setMethods(array('render','exists','supports','renderResponce'))
                                   ->getMock();

                $this->twig->expects($this->any())
                           ->method('render')
                           ->will($this->returnValue('success'));
                
                $this->twig->expects($this->any())
                           ->method('renderResponce')
                           ->will($this->returnValue('success'));
                
                $this->twig->setLoader($this->getMockBuilder('\Twig_LoaderInterface')->getMock());
                $this->container->set('twig', $this->twig);
            }  
            //Real twig render 
            else   
                $this->twig = $this->container->get('twig'); 

            $this->controller = new $controller;
            $this->controller->setContainer($this->container);
    }
}
