<?php

namespace Tests;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Roles/permissions are infrastructure, not per-test fixture data: any
     * test using RefreshDatabase gets them seeded automatically so it never
     * has to remember to do so before calling assignRole()/can().
     */
    protected $seed = true;

    protected $seeder = RoleAndPermissionSeeder::class;
}
