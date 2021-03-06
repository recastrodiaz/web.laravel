<?php

namespace Dms\Web\Laravel\Tests\Integration\Fixtures\Demo;

use Dms\Core\Cms;
use Dms\Core\CmsDefinition;
use Dms\Web\Laravel\Auth\AdminPackage;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DemoCms extends Cms
{
    /**
     * Defines the structure and installed packages of the cms.
     *
     * @param CmsDefinition $cms
     *
     * @return void
     */
    protected function define(CmsDefinition $cms)
    {
        $cms->packages([
                'admin' => AdminPackage::class
        ]);
    }
}