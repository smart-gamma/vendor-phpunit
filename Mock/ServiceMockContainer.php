<?php
namespace Gamma\PhpUnit\Tester\Mock;

/**
 * PhpUnit Extension for Symfony2 services mocking
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
abstract class ServiceMockContainer extends \PHPUnit_Framework_TestCase
{
    /**
     * Class of service
     * @var mixed  $mockService
     */
    protected $serviceClass;
    
    /**
     * Mock of service
     * @var mixed  $mockService
     */
    protected $serviceMock;

    /**
     * List of emulating service's methods ( set in child class)
     * @var array $repositoryMethods
     */
    protected $serviceMethods = array();

    /**
     * Create mock of service
     */
    public function __construct()
    {
        $this->serviceMock = $this->getMock($this->serviceClass, $this->serviceMethods, array(), '', false);
        /* Emulate methods of repository */
        $this->setUpMethods(); 
    }

    /**
     * Get mock of Repository
     * @return mixed
     */
    public function getServiceMock()
    {
      return $this->serviceMock;
    }

    /**
     * Get Repository Name
     * @return string
     */
    public function getServiceClass()
    {
      return $this->serviceClass;
    }
    
    /**
     * Emulating repository's methods 
     * @return void
     */ 
    abstract protected function setUpMethods();
}
