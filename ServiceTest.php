<?php
namespace LaMelle\PhpUnit\Tester;

/*
 * tmp solution to work in local dev/capifony target env
 * 
 */
$path = strpos(realpath(__DIR__),'shared/vendor') ? realpath(__DIR__).'/..' : realpath(__DIR__);
require_once $path . '/../../../../../../app/AppKernel.php';

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
  
    /*
     * App kernel
     * @var \AppKernel $kernel
     */
    protected static $kernel;
    
    /*
     * DI container
     * @var Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    protected $container;

    /*
     * Target test service
     * @var mixed $service
     */    
    protected $service;
    
	/**
	 * Initializes a new instance of the ServiceTest class.
	 *
	 * @param string      $service   path to service class
     * @param bool        $construct_container flag to pass container in constructor  
	 */  
    function __construct($service = '', $construct_container = false)
    {
            //Kernel/container startup 
            $this->init();
            parent::__construct();
            
            //Call container independent service
            if(!empty($service) && !$construct_container)
                $this->service = new $service;
            
            //Call container dependent service
            if(!empty($service) && $construct_container)
                $this->service = new $service($this->container);
    }
  
	/**
     * Boot Kernel and container init
	 * @return void
	 */
	protected function init()
	{
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        $this->container = self::$kernel->getContainer();
	}
    
    /*
     * Get mock of Entity manager
     * @param array $testRepositorySet set with Repository behavior - Set has Name of repository and bunch of emulated methods 
     * @return \Doctrine\ORM\Mock_EntityManager
     */
    protected function getEntityManagerMock($testRepositorySet = array())
    {
        $mockEntityManager = $this->getMock('\Doctrine\ORM\EntityManager',
                               array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false); 
        
        $mockEntityManager->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));
        
        $mockEntityManager->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        
        $mockEntityManager->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        // Add used repositories with emulated methods
        if(sizeof($testRepositorySet))
            foreach($testRepositorySet as $testRepository)
            {
                $mockEntityManager->expects($this->once())
                    ->method('getRepository')
                    ->with($testRepository->getRepositoryName())
                    ->will($this->returnValue($testRepository->getMockRepository()));
            }
        
        return $mockEntityManager;
    }
}
