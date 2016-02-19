<?php

namespace Dms\Web\Laravel\Util;

use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Util\Debug;
use Illuminate\Cache\Repository as Cache;

/**
 * The entity module map class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EntityModuleMap
{
    const CACHE_EXPIRY = 60;

    /**
     * @var ICms
     */
    protected $cms;

    /**
     * @var array
     */
    protected $map;

    /**
     * EntityModuleMap constructor.
     *
     * @param ICms  $cms
     * @param Cache $cache
     */
    public function __construct(ICms $cms, Cache $cache)
    {
        $installedModulesHash = md5(implode('__', $cms->getPackageNames()));
        $moduleMapKey         = 'dms:module-map:' . $installedModulesHash;

        $this->cms = $cms;
        $this->map = $cache->remember($moduleMapKey, self::CACHE_EXPIRY, function () {
            $map = [];

            foreach ($this->cms->loadPackages() as $package) {
                foreach ($package->loadModules() as $module) {
                    if ($module instanceof IReadModule) {
                        $map[$module->getObjectType()] = $package->getName() . '.' . $module->getName();
                    }
                }
            }

            return $map;
        });
    }

    /**
     * @param string $entityType
     *
     * @return IReadModule
     * @throws InvalidArgumentException
     */
    public function loadModuleFor(string $entityType) : IReadModule
    {
        if (!isset($this->map[$entityType])) {
            throw InvalidArgumentException::format('Invalid call to %s: unknown entity type, expecting one of (%s), %s given',
                __METHOD__, Debug::formatValues(array_keys($this->map)), $entityType
            );
        }

        list($packageName, $moduleName) = explode('.', $this->map[$entityType]);

        return $this->cms->loadPackage($packageName)->loadModule($moduleName);
    }
}