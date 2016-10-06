<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env;

use Manala\Env\Config\Ansible;
use Manala\Env\Config\Make;
use Manala\Env\Config\Vagrant;
use Manala\Env\Config\Variable\AppName;
use Manala\Env\Config\Variable\VagrantBoxVersion;
use Manala\Env\Env;
use Manala\Env\EnvEnum;
use Manala\Env\EnvFactory;

class EnvFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateEnv()
    {
        $envType = EnvEnum::create(EnvEnum::SYMFONY);
        $appName = new AppName('rch');
        $boxVersion = new VagrantBoxVersion('~> 3.0.0');
        $env = EnvFactory::createEnv($envType, $appName, $this->prophesize(\Iterator::class)->reveal());
        $expectedConfigs = [new Vagrant($envType, $appName, $boxVersion), new Ansible($envType), new Make($envType)];

        $this->assertInstanceOf(Env::class, $env);
        $this->assertEquals($expectedConfigs, $env->getConfigs());
        $this->assertCount(3, $env->getConfigs());
    }
}
