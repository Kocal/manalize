<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\Factory;

use Manala\Manalize\Requirement\Processor\AbstractProcessor;
use Manala\Manalize\Requirement\Processor\VagrantPluginProcessor;
use Manala\Manalize\Requirement\SemVer\VagrantPluginVersionParser;
use Manala\Manalize\Requirement\SemVer\VersionParserInterface;

/**
 * Factory that instantiates the concrete processor and version parser to handle vagrant plugin requirements.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class VagrantPluginHandlerFactory implements HandlerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProcessor(): AbstractProcessor
    {
        return new VagrantPluginProcessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionParser(): VersionParserInterface
    {
        return new VagrantPluginVersionParser();
    }
}
