<?php

namespace Modera\SecurityBundle\Tests\Functional\Entity;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadata;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\SecurityBundle\Entity\Group;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\Entity\PermissionCategory as PermissionCategoryEntity;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Alex Plaksin <alex.plaksin@modera.net>
 * @copyright 2016 Modera Foundation
 */
class GroupRepositoryTest extends FunctionalTestCase
{
    /**
     * @var SchemaTool
     */
    private static $st;

    /**
     * {@inheritdoc}
     */
    public static function doSetUpBeforeClass(): void
    {
        static::$st = new SchemaTool(static::$em);
        static::$st->dropSchema(static::getTableClassesMetadata());
        static::$st->createSchema(static::getTableClassesMetadata());
    }

    public static function doTearDownAfterClass(): void
    {
        static::$st->dropSchema(static::getTableClassesMetadata());
    }

    public function testFindByRefName()
    {
        $emptyGroupList = static::$em->getRepository(Group::class)->findByRefName('test');
        $this->assertCount(0, $emptyGroupList);

        $group = new Group();
        $group->setName('test');
        $group->setRefName('test');

        static::$em->persist($group);
        static::$em->flush();

        $oneGroupList = static::$em->getRepository(Group::class)->findByRefName('test');
        $this->assertCount(1, $oneGroupList);
        $this->assertEquals($group, $oneGroupList[0]);

        $anotherEmptyList = static::$em->getRepository(Group::class)->findByRefName('testNew');
        $this->assertCount(0, $anotherEmptyList);

        return $group;
    }

    /**
     * There is unique constrain present on refName field. And this constrain is NOT case sensitive.
     * So findByRefName search is NOT case sensitive.
     *
     * @depends testFindByRefName
     *
     * @param Group $group
     */
    public function testFindByRefName_RefNameCases(Group $group)
    {
        $oneGroupList = static::$em->getRepository(Group::class)->findByRefName('Test');
        $this->assertCount(1, $oneGroupList);
        $this->assertEquals($group, $oneGroupList[0]);

        $anotherOneGroupList = static::$em->getRepository(Group::class)->findByRefName('tesT');
        $this->assertCount(1, $anotherOneGroupList);
        $this->assertEquals($group, $anotherOneGroupList[0]);

        $lastOneGroupList = static::$em->getRepository(Group::class)->findByRefName('TEST');
        $this->assertCount(1, $lastOneGroupList);
        $this->assertEquals($group, $lastOneGroupList[0]);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getIsolationLevel(): string
    {
        return static::IM_CLASS;
    }

    /**
     * Db Tables used in test.
     *
     * @return array
     */
    private static function getTableClasses()
    {
        return [
            User::class,
            Group::class,
            Permission::class,
            PermissionCategoryEntity::class,
        ];
    }

    /**
     * @return ClassMetadata[]
     */
    private static function getTableClassesMetadata()
    {
        $metaData = array();
        foreach (static::getTableClasses() as $class) {
            $metaData[] = static::$em->getClassMetadata($class);
        }

        return $metaData;
    }
}
