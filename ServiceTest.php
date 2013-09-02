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
  protected static $kernel;
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
	 * @return void
	 */
	protected function init()
	{
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        $this->container = self::$kernel->getContainer();
	}  
}
