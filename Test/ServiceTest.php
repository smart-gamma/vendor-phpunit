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
     * Flag at the phpunit cli to switch off mock emulation and work with real classes
     *
     * Usage: phpunit -d noMock ...
     */
    const DISABLE_EMULATION_CLI_ARG = 'nomock';

    const REQUEST_SCOPE = 'request';

    /**
     * Selector to use real twig and inhouse classes or mock them
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
     *
     * @var \AppKernel $kernel
     */
    protected static $kernel;

    /**
     * DI container
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    protected $container;

    /**
     * Target class name for the instance creation
     *
     * @var mixed $instance
     */
    protected $targetClassName;
    
    /**
     * Target test service
     *
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
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->processEmulationMode();
        $this->init();

        if (!empty($this->targetClassName)) {
            if (!$this->isConstructContainer) {
                //Call container independent service
                $this->instance = new $this->targetClassName;
            } else {
                //Call container dependent service
                $this->instance = new $this->targetClassName($this->container);
            }
        }

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Boot Kernel and container init
     */
    protected function init()
    {
        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();

        $this->container = self::$kernel->getContainer();
    }

    /**
     * @param string $scope
     */
    protected function setScope($scope = self::REQUEST_SCOPE)
    {
        $this->container->enterScope($scope);
        $this->container->set('request', new Request(), $scope);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!$this->isMockEmulation) {
            $this->instance->setEm($this->container->get("doctrine.orm.entity_manager"));

            return;
        }

        if ($this->emulatedRepositoriesList) {
            //pass all dependent emulated repositories to EntityManager
            foreach($this->emulatedRepositoriesList as $emulatedRepository) {
                $mock = new $emulatedRepository();
                $mockedRepositories[$mock->getRepositoryName()] = $mock->getRepositoryMock();
            }

            $this->instance->setEm($this->getEntityManagerMock($mockedRepositories));

            return;
        }

        // none emulated reposirories created yet, so lets use real ones for classes with entity manger passed
        if(method_exists($this->instance,'setEm')) {
            $this->instance->setEm($this->container->get("doctrine.orm.entity_manager"));
        }
    }
    
    /**
     * Get mock of Entity manager
     *
     * @param array $mockedRepositories
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEntityManagerMock($mockedRepositories = array())
    {
        $methods = array(
            'getRepository',
            'getClassMetadata',
            'persist',
            'flush',
        );

        $mockEntityManager = $this->getMock('\Doctrine\ORM\EntityManager', $methods, array(), '', false);

        $mockEntityManager->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object) array('name' => 'aClass')))
        ;

        $mockEntityManager->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null))
        ;

        $mockEntityManager->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null))
        ;

        $mockEntityManager->expects($this->any())
            ->method('getRepository')
            ->with($this->anything())
            ->will($this->returnCallback(function($repositoryName) use ($mockedRepositories) {
                return $mockedRepositories[$repositoryName];
            }))
        ;
        
        return $mockEntityManager;
    }
    
    /**
     * Parse phpunit cli for disabling mock emulation mode, that is enabled by default
     *
     * @return void
     */
    private function processEmulationMode()
    {
        global $argv;
        
        foreach($argv as $arg) {
            if($arg == self::DISABLE_EMULATION_CLI_ARG) {
                $this->isMockEmulation = false;
            }
        } 
    }
}
