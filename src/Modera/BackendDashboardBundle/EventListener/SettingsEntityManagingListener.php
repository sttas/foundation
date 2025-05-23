<?php

namespace Modera\BackendDashboardBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Modera\BackendDashboardBundle\Entity\GroupSettings;
use Modera\BackendDashboardBundle\Entity\UserSettings;
use Modera\SecurityBundle\Entity\Group;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SettingsEntityManagingListener
{
    public function onFlush(OnFlushEventArgs $event): void
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $userSettings = new UserSettings();
                $userSettings->setUser($entity);

                $em->persist($userSettings);
                $uow->computeChangeSet($em->getClassMetadata(UserSettings::class), $userSettings);
            }
            if ($entity instanceof Group) {
                $groupSettings = new GroupSettings();
                $groupSettings->setGroup($entity);

                $em->persist($groupSettings);
                $uow->computeChangeSet($em->getClassMetadata(GroupSettings::class), $groupSettings);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof User) {
                $query = $em->createQuery(\sprintf('DELETE FROM %s us WHERE us.user = ?0', UserSettings::class));
                $query->execute([$entity]);
            }
            if ($entity instanceof Group) {
                $query = $em->createQuery(\sprintf('DELETE FROM %s us WHERE us.group = ?0', GroupSettings::class));
                $query->execute([$entity]);
            }
        }
    }
}
