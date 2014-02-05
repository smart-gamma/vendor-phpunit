<?php
namespace Gamma\PhpUnit\Tester\Mock;

/**
 * Mock container for Repository with cache feature
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
abstract class CacheRepositoryMockContainer extends RepositoryMockContainer
{
    /**
     * Mapper type (ORM/ODM)
     * @var string $repositoryType
     */
    protected $repositoryType = 'LaMelle\Framework\Repository\CacheRepository';
     
    function __construct() 
    {       
        /* List of emulating repository's methods */
        $this->repositoryMethods = array_merge(array("initCachePath", "getCachePrefix", "getCreatedSearchQueryCachePath"), $this->repositoryMethods); 

        /* Create moch of repository */
        parent::__construct();
        
        /* Emulate methods of repository */
        $this->setUpMethods(); 
    }  
    
    /**
     * Emulating repository's methods related cache 
     * @return void
     */     
    protected function setUpMethods() 
    {
        /**
         * initCachePath
         * @covers Gamma\Framework\Repository\CacheRepository::initCachePath
         */
        $this->repositoryMock
                 ->expects($this->any())
                 ->method("initCachePath")
                 ->with(new Symfony\Component\HttpFoundation\Request())
                 ->will($this->returnValue(null)); 
        
        /**
         * getCachePrefix
         * @covers Gamma\Framework\Repository\CacheRepository::getCachePrefix
         */
        $this->repositoryMock
                 ->expects($this->any())
                 ->method("getCachePrefix")
                 ->will($this->returnValue('EMULATION_CACHE_PREFIX'));
        
        /**
         * getCreatedSearchQueryCachePath
         * @covers Gamma\Framework\Repository\CacheRepository::getCreatedSearchQueryCachePath
         */
        $this->repositoryMock
                 ->expects($this->any())
                 ->method("getCreatedSearchQueryCachePath")
                 ->will($this->returnValue('EMULATION_CREATED_CACHE_PATH'));        
    }    
}
