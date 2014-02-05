<?php

namespace Gamma\PhpUnit\Tester\Test;

use \AppKernel;
use Symfony\Component\HttpFoundation\Request;

/**
 * PhpUnit Extension for Symfony2 services unit tests
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
abstract class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Selector to use real twig and inhouse classes or mock them
     * External api services like Paypal/Fotolia etc are mockering any case
     *
     * @var bool
     */
    protected $isMockEmulation = true;

    /**
     * List of mocking repositories when $isMockEmulation = true;
     * 
     * @var array
     */    
    protected $emulatedRepositoriesList = array(); 
    
    /**
     * App kernel
     * @var AppKernel $kernel
     */
    protected static $kernel;

    /**
     * DI container
     * @var Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    protected $container;

    /**
     * Target class name for the instance creation
     * @var mixed $instance
     */
    protected $targetClassName;
    
    /**
     * Target test service
     * @var mixed $instance
     */
    protected $instance;

    /**
     * Selector to pass container to constructor of class
     *
     * @var bool
     */
    protected $isConstructContainer = false;
    
    /**
     * Initializes a new instance of the ServiceTest class.
     */
    public function __construct()
    {
        //Kernel/container startup
        $this->init();
        parent::__construct();

        //Call container independent service
        if (!empty($this->targetClassName) && !$this->isConstructContainer)
            $this->instance = new $this->targetClassName;

        //Call container dependent service
        if (!empty($this->targetClassName) && $this->isConstructContainer)
            $this->instance = new $this->targetClassName($this->container);
    }

    /**
     * Boot Kernel and container init
     * @return void
     */
    protected function init()
    {
        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();

        $this->container = self::$kernel->getContainer();
        $this->container->enterScope('request');
        $this->container->set('request', new Request(), 'request');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        //ORM emulation
        if ($this->isMockEmulation) {
            //pass all dependent emulated repositories to EntityManager 
            $mockedRepositories = array();
            foreach($this->emulatedRepositoriesList as $emulatedRepository) {
                $mock = new $emulatedRepository();
                $mockedRepositories[$mock->getRepositoryName()] = $mock->getRepositoryMock();
            }
            if(sizeof($this->emulatedRepositoriesList) > 0) {
                $this->instance->setEm($this->getEntityManagerMock($mockedRepositories));
            } 
            // none emulated reposirories created yet, so lets use real ones for classes with entity manger passed
            elseif(method_exists($this->instance,'setEm')) {                 
                $this->instance->setEm($this->container->get("doctrine.orm.entity_manager"));
            }  
        }
        // Real ORM
        else {
            $this->instance->setEm($this->container->get("doctrine.orm.entity_manager"));
        }    
    }
    
    /**
     * Get mock of Entity manager
     * @param  array  $mockedRepositories set with Repository behavior 
     * @return \Doctrine\ORM\Mock_EntityManager
     */
    private function getEntityManagerMock($mockedRepositories = array())
    {
        $mockEntityManager = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false);

        $mockEntityManager->expects($this->any())
                ->method('getClassMetadata')
                ->will($this->returnValue((object) array('name' => 'aClass')));

        $mockEntityManager->expects($this->any())
                ->method('persist')
                ->will($this->returnValue(null));

        $mockEntityManager->expects($this->any())
                ->method('flush')
                ->will($this->returnValue(null));
        
        $mockEntityManager->expects($this->any())
                        ->method('getRepository')
                        ->with($this->anything())
                        ->will($this->returnCallback(function($repositoryName) use ($mockedRepositories) { 
                                                            return $mockedRepositories[$repositoryName];
                                                     }
                                                     )
                              );
        
        return $mockEntityManager;
    }
}
