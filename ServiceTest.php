<?php
namespace LaMelle\PhpUnit\Tester;

require_once __DIR__.'/../../../../../../../app/AppKernel.php';

class ServiceTest extends \PHPUnit_Framework_TestCase
{
  protected static $kernel;
  protected $container;

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
        self::$kernel = new \AppKernel('dev', true);
        self::$kernel->boot();

        $this->container = self::$kernel->getContainer();
	}  
}
