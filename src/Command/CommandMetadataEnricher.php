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

namespace Broadway\Bundle\BroadwayBundle\Command;

use Broadway\Domain\Metadata;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Enricher that adds information about the excecuted console command.
 */
class CommandMetadataEnricher implements MetadataEnricher
{
    /**
     * @var ConsoleCommandEvent|null
     */
    private $event;

    /**
     * {@inheritdoc}
     */
    public function enrich(Metadata $metadata): Metadata
    {
        if (null === $this->event || null === $this->event->getCommand()) {
            return $metadata;
        }

        $data = [
            'console' => [
                'command' => get_class($this->event->getCommand()),
                'arguments' => method_exists($this->event->getInput(), '__toString') ? (string) $this->event->getInput() : '',
            ],
        ];
        $newMetadata = new Metadata($data);

        return $metadata->merge($newMetadata);
    }

    public function handleConsoleCommandEvent(ConsoleCommandEvent $event): void
    {
        $this->event = $event;
    }
}
