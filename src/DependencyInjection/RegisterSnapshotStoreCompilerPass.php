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

namespace Broadway\Bundle\BroadwayBundle\DependencyInjection;

use Broadway\Snapshotting\Snapshot\SnapshotRepository;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterSnapshotStoreCompilerPass extends CompilerPass
{
    public function process(ContainerBuilder $container): void
    {
        $serviceParameter = 'broadway.snapshot_store.service_id';
        if (!$container->hasParameter($serviceParameter)) {
            $container->setAlias('broadway.snapshot_store', 'broadway.snapshot_store.dbal');

            return;
        }

        $serviceId = $container->getParameter($serviceParameter);
        if (false === is_string($serviceId)) {
            return;
        }

        $this->assertDefinitionImplementsInterface($container, $serviceId, SnapshotRepository::class);

        $container->setAlias('broadway.snapshot_store', new Alias($serviceId, true));
    }
}
