<?php

namespace Modera\BackendSecurityBundle\Tests\Fixtures\App\Contributions;

use Modera\BackendSecurityBundle\Section\Section;
use Modera\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Artem Brovko <artem.brovko@modera.com>
 * @copyright 2021 Modera Foundation
 */
class ClientDIContributor implements ContributorInterface
{
    public function getItems(): array
    {
        return [
            new Section('section1', 'Section 1', 'icon-1', 'Some.ui.class'),
        ];
    }

}
