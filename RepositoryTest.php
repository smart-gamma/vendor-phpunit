<?php
namespace Gamma\PhpUnit\Tester;

/**
 * PhpUnit Extension for Symfony2 repositories unit tests
 *
 * @author Evgen Kuzmin <jekccs@gmail.com>
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mapper type (ORM/ODM)
     * @var string $repositoryType
     */
    protected $repositoryType = 'Doctrine\ORM\EntityRepository';

    /**
     * Mock of repository
     * @var \Doctrine\ORM\Mock_EntityRepository $mockRepository
     */
    protected $mockRepository;

    /**
     * Testing Repository name (must be set in child class)
     * @var string $repositoryName ('BundleName::RepositoryName')
     */
    protected $repositoryName = '';

    /*
     * Common entity returned by repository (must be set in child class)
     * @var string $entity ('Sufix\BundleName\Entity\EntityName')
     */
    protected $entity = '';

    /**
     * List of emulating repository's methods (must be set in child class)
     * @var array $repositoryMethods
     */
    protected $repositoryMethods = array();

    /**
     * Create mock of repository
     */
    public function __construct()
    {
        /* @var $this->mockRepository \Doctrine\ORM\Mock_EntityRepository */
        $this->mockRepository = $this->getMock($this->repositoryType, $this->repositoryMethods, array(), '', false);
    }

    /**
     * Get mock of Repository
     * @return \Doctrine\ORM\Mock_EntityRepository
     */
    public function getMockRepository()
    {
      return $this->mockRepository;
    }

    /**
     * Get Repository Name
     * @return string
     */
    public function getRepositoryName()
    {
      return $this->repositoryName;
    }
}
