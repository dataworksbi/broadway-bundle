<?php

declare(strict_types=1);

/*
 * This file is part of the broadway/broadway-bundle package.
 *
 * (c) 2020 Broadway project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\Bundle\BroadwayBundle\DependencyInjection\CompilerPass;

use Broadway\Bundle\BroadwayBundle\DependencyInjection\RegisterSnapshotStoreCompilerPass;
use Broadway\Snapshotting\Snapshot\SnapshotRepository;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterSnapshotStoreCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterSnapshotStoreCompilerPass());
    }

    /**
     * @test
     */
    public function it_sets_the_snapshot_store_alias_to_in_memory_by_default(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasAlias('broadway.snapshot_store', 'broadway.snapshot_store.in_memory');
    }

    /**
     * @test
     */
    public function it_sets_the_public_snapshot_store_alias(): void
    {
        $this->container->setParameter('broadway.snapshot_store.service_id', 'my_snapshot_store');

        $this->setDefinition('my_snapshot_store', new Definition(SnapshotRepository::class));

        $this->compile();

        $this->assertContainerBuilderHasAlias('broadway.snapshot_store', 'my_snapshot_store');
        $this->assertTrue($this->container->getAlias('broadway.snapshot_store')->isPublic());
    }

    /**
     * @test
     */
    public function it_throws_when_configured_snapshot_store_has_no_definition(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Service id "my_snapshot_store" could not be found in container');
        $this->container->setParameter('broadway.snapshot_store.service_id', 'my_snapshot_store');

        $this->compile();
    }

    /**
     * @test
     */
    public function it_throws_when_configured_snapshot_store_does_not_implement_event_store_interface(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Service "stdClass" must implement interface "Broadway\Snapshotting\Snapshot\SnapshotRepository".');
        $this->container->setParameter('broadway.snapshot_store.service_id', 'my_snapshot_store');

        $this->setDefinition('my_snapshot_store', new Definition(\stdClass::class));

        $this->compile();
    }
}
