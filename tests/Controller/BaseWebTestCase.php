<?php

namespace App\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Persistence\ObjectManager;
use Exception;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BaseWebTestCase extends WebTestCase
{
    /**
     * Default content type.
     */
    protected const DEFAULT_MIME_TYPE = 'application/json';

    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = self::createClient([],[
            'HTTP_HOST' => '127.0.0.1:8080',
        ]);
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function loadFixture(FixtureInterface $fixture)
    {
        $loader = new Loader();
        $loader->addFixture($fixture);
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures(), true);
    }
}
