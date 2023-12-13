<?php

namespace App\Test\Controller;

use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/ticket/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Ticket::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ticket index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ticket[ticketType]' => 'Testing',
            'ticket[price]' => 'Testing',
            'ticket[quantityAvailable]' => 'Testing',
            'ticket[isActive]' => 'Testing',
            'ticket[event]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ticket();
        $fixture->setTicketType('My Title');
        $fixture->setPrice('My Title');
        $fixture->setQuantityAvailable('My Title');
        $fixture->setIsActive('My Title');
        $fixture->setEvent('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ticket');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ticket();
        $fixture->setTicketType('Value');
        $fixture->setPrice('Value');
        $fixture->setQuantityAvailable('Value');
        $fixture->setIsActive('Value');
        $fixture->setEvent('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ticket[ticketType]' => 'Something New',
            'ticket[price]' => 'Something New',
            'ticket[quantityAvailable]' => 'Something New',
            'ticket[isActive]' => 'Something New',
            'ticket[event]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ticket/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTicketType());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getQuantityAvailable());
        self::assertSame('Something New', $fixture[0]->getIsActive());
        self::assertSame('Something New', $fixture[0]->getEvent());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ticket();
        $fixture->setTicketType('Value');
        $fixture->setPrice('Value');
        $fixture->setQuantityAvailable('Value');
        $fixture->setIsActive('Value');
        $fixture->setEvent('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ticket/');
        self::assertSame(0, $this->repository->count([]));
    }
}
