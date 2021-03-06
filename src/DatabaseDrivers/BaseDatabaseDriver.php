<?php

namespace DivineOmega\ThisIsHowIRole\DatabaseDrivers;

use DivineOmega\ThisIsHowIRole\Interfaces\DatabaseDriverInterface;
use DivineOmega\ThisIsHowIRole\Utils;
use DivineOmega\ThisIsHowIRole\CacheDrivers\RWFileCacheDriver;
use DivineOmega\ThisIsHowIRole\CacheDrivers\LaravelCacheDriver;

abstract class BaseDatabaseDriver implements DatabaseDriverInterface
{
  protected $cache = null;

  public function setupCache()
  {
    if (class_exists('Illuminate\Support\Facades\Cache')) {
      $this->cache = new LaravelCacheDriver;
    } else {
      $this->cache = new RWFileCacheDriver;
    }
  }

  public function add($className, $foreignId, $role)
  {
    $roles = $this->getRoles($className, $foreignId);

    $rolesArray = explode(' ', $roles);
    $key = array_search($role, $rolesArray);
    if ($key!==false) {
      return;
    }
    $rolesArray[] = $role;
    sort($rolesArray);
    $roles = implode(' ', $rolesArray);

    $this->setRoles($className, $foreignId, $roles);

  }

  public function remove($className, $foreignId, $role)
  {
    if (Utils::testModeActive()) {
      return;
    }

    $roles = $this->getRoles($className, $foreignId);

    $rolesArray = explode(' ', $roles);
    $key = array_search($role, $rolesArray);
    if ($key===false) {
      return;
    }
    unset($rolesArray[$key]);
    sort($rolesArray);
    $roles = implode(' ', $rolesArray);

    $this->setRoles($className, $foreignId, $roles);

  }

  public function has($className, $foreignId, $role)
  {
    if (Utils::testModeActive()) {
      return true;
    }

    $roles = $this->getRoles($className, $foreignId);

    $rolesArray = explode(' ', $roles);
    $key = array_search($role, $rolesArray);

    if ($key===false) {
      return false;
    }

    return true;

  }

  public function all($className, $foreignId)
  {
    if (Utils::testModeActive()) {
      return  '';
    }

    $roles = $this->getRoles($className, $foreignId);

    $rolesArray = explode(' ', $roles);

    return $rolesArray;

  }

}
