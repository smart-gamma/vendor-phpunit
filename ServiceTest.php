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
	 */  
    function __construct($service = '')
    {
            $this->init();
            parent::__construct();
            
            if(!empty($service))
                $this->service = new $service;
    }
  
	/**
     * Boot init
	 * @return void
	 */
	protected function init()
	{
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        $this->container = self::$kernel->getContainer();
	}  
}
