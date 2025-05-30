<?php

namespace Modera\MjrIntegrationBundle\Config;

/**
 * Implementations of this interface are responsible for contributing configuration which later will be exposed
 * to JavaScript runtime.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface ConfigMergerInterface
{
    /**
     * @param array<mixed> $existingConfig
     *
     * @return array<mixed>
     */
    public function merge(array $existingConfig): array;
}
