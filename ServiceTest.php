<?php

namespace Gamma\PhpUnit\Tester;

use \AppKernel;

/**
 * PhpUnit Extension for Symfony2 services unit tests
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
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
     * Target test service
     * @var mixed $service
     */
    protected $service;

    /**
     * Initializes a new instance of the ServiceTest class.
     *
     * @param string $service             path to service class
     * @param bool   $construct_container flag to pass container in constructor
     */
    public function __construct($service = '', $construct_container = false)
    {
        //Kernel/container startup
        $this->init();
        parent::__construct();

        //Call container independent service
        if (!empty($service) && !$construct_container)
            $this->service = new $service;

        //Call container dependent service
        if (!empty($service) && $construct_container)
            $this->service = new $service($this->container);
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
                $mockedRepositories[$mock->getRepositoryName()] = $mock->getMockRepository();
            }
            $this->service->setEm($this->getEntityManagerMock($mockedRepositories));
        }
        // Real ORM
        else {
            $this->service->setEm($this->container->get("doctrine.orm.entity_manager"));
        }    
    }
    
    /**
     * Get mock of Entity manager
     * @param  array  $mockedRepositories set with Repository behavior 
     * @return \Doctrine\ORM\Mock_EntityManager
     */
    protected function getEntityManagerMock($mockedRepositories = array())
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
