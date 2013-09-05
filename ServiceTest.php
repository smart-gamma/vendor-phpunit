<?php
namespace LaMelle\PhpUnit\Tester;

require_once __DIR__.'/../../../../../../app/AppKernel.php';

/**
 * PhpUnit Extension for Symfony2 services unit tests 
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
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
}
