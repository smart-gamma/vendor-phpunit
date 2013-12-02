<?php
namespace Gamma\PhpUnit\Tester;

/**
 * PhpUnit Extension for Symfony2 repositories unit tests 
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /*
     * Mapper type (ORM/ODM) 
     * @var string $repositoryType
     */     
     protected $repositoryType = 'LaMelle\Framework\Repository\CacheRepository';//'Doctrine\ORM\EntityRepository';
     
    /*
     * Mock of repository 
     * @var \Doctrine\ORM\Mock_EntityRepository $mockRepository 
     */ 
    protected $mockRepository;

    /*
     * Testing Repository name (must be set in child class)
     * @var string $repositoryName ('BundleName::RepositoryName')
     */ 
    protected $repositoryName = '';

    /*
     * Common entity returned by repository (must be set in child class)
     * @var string $entity ('Sufix\BundleName\Entity\EntityName')
     */ 
    protected $entity = '';

    /*
     * List of emulating repository's methods (must be set in child class)
     * @var array $repositoryMethods
     */     
    protected $repositoryMethods = array(); 

    /*
     * Create mock of repository
     */
    function __construct() 
    {
        /* @var $this->mockRepository \Doctrine\ORM\Mock_EntityRepository */
        $this->mockRepository = $this->getMock($this->repositoryType, $this->repositoryMethods, array(), '', false);
    }
    
    /*
     * Get mock of Repository
     * @return  \Doctrine\ORM\Mock_EntityRepository  
     */    
    public function getMockRepository()
    {
      return $this->mockRepository;
    } 
    
    /*
     * Get Repository Name
     * @return  string  
     */ 
    public function getRepositoryName()
    {
      return $this->repositoryName;
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
