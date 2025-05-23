<?php

namespace Modera\ConfigBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Modera\ConfigBundle\Notifying\NotificationCenter;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2021 Modera Foundation
 */
class ConfigurationEntryEventListener
{
    private NotificationCenter $notificationCenter;

    public function __construct(NotificationCenter $notificationCenter)
    {
        $this->notificationCenter = $notificationCenter;
    }

    public function postPersist(ConfigurationEntry $entity, LifecycleEventArgs $args): void
    {
        $this->notificationCenter->notifyConfigurationEntryAdded($entity);
    }

    public function postUpdate(ConfigurationEntry $entity, LifecycleEventArgs $args): void
    {
        $changesSet = $args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity);

        foreach ($changesSet as $field => $changes) {
            if ('updatedAt' === $field) {
                continue;
            }

            $hasValuableChanges = $changes[0] != $changes[1];

            if ($hasValuableChanges) {
                $this->notificationCenter->notifyConfigurationEntryUpdated($entity);

                return;
            }
        }
    }

    public function postRemove(ConfigurationEntry $entity, LifecycleEventArgs $args): void
    {
        $this->notificationCenter->notifyConfigurationEntryRemoved($entity);
    }
}
