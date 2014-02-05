<?php

namespace Gamma\PhpUnit\Tester\Test;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PhpUnit Extension for Symfony2 controllers unit tests
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
abstract class ControllerTest extends ServiceTest
{
    /**
     * App request
     * @var Symfony\Component\HttpFoundation\Request $request
     */
    protected $request;

    /**
     * App session
     * @var Symfony\Component\HttpFoundation\Session $session
     */
    protected $session;

    /**
     * Twig env
     * @var \Twig_Environment $twig
     */
    protected $twig;

    /**
     * Templating engine
     * @var Symfony\Component\Templating\EngineInterface $templating
     */
    protected $templating;

    /**
     * Target test controller
     * @var Symfony\Bundle\FrameworkBundle\Controller\Controller $controller
     */
    protected $controller;

    /**
     * Initializes a new instance of the ControllerTest class.
     *
     * @param string $controller      path to controller to test
     * @param bool   $isMockEmulation switcher for twig mock emulation or real render
     */
    public function __construct($controller, $isMockEmulation = false)
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
            if ($isMockEmulation) {
                //Twig emulation
                $this->twig = $this->getMockBuilder('\Twig_Environment')
                                   ->setMethods(array('render', 'exists', 'supports'))
                                   ->getMock();

                $this->twig->expects($this->any())
                           ->method('render')
                           ->will($this->returnValue('success'));

                $this->twig->setLoader($this->getMockBuilder('\Twig_LoaderInterface')->getMock());
                $this->container->set('twig', $this->twig);

                //Templating emulation
                $this->templating = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
                                   ->setMethods(array('render', 'exists', 'supports', 'renderResponse'))
                                   ->getMock();

                $this->templating->expects($this->any())
                           ->method('renderResponse')
                           ->will($this->returnValue(new Response()));
            }
            //Real twig render
            else {
                $this->twig = $this->container->get('twig');
                $this->templating = $this->container->get('templating');
            }

            $this->controller = new $controller;
            $this->controller->setContainer($this->container);
    }

    /**
     * Set of phpunit additional assert to test controller action responce ok
     * @param \Symfony\Component\HttpFoundation\Response $result - controller action answer
     */
    public function assertActionResponded($result)
    {
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals(200,$result->getStatusCode());
        $this->assertNotNull($result->getContent());
    }
}
