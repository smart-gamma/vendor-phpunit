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

	/**
	 * Initializes a new instance of the ServiceTest class.
	 *
	 * @param string      $key   unique name for the lock
	 * @param string|null $path  put lock file in this folder, use null for system temp dir
	 */  
    function __construct()
    {
            $this->init();
            parent::__construct();
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
