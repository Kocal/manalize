<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Handler;

use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvEnum;
use Manala\Manalize\Exception\HandlingFailureException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class Diff
{
    const EXIT_SUCCESS_DIFF = 1;
    const EXIT_SUCCESS_NO_DIFF = 0;

    private $envName;
    private $cwd;
    private $fs;
    private $colorSupport;
    private $lastExitCode = 0;
    private $errorOutput = '';

    /**
     * @param EnvEnum $envName
     * @param string  $cwd          The working dir
     * @param bool    $colorSupport
     */
    public function __construct(EnvEnum $envName, string $cwd, bool $colorSupport = true)
    {
        $this->envName = $envName;
        $this->cwd = $cwd;
        $this->colorSupport = $colorSupport;

        $this->fs = new Filesystem();
    }

    public function handle(callable $notifier): int
    {
        $resourcesPath = $this->createTmpEnv();

        $colorOpt = $this->colorSupport ? '--color' : '--no-color';

        $process = new Process("git diff --diff-filter=d --no-index --patch $colorOpt . $resourcesPath", $this->cwd);

        $process->run(function ($type, $buffer) use ($resourcesPath, $notifier) {
            $buffer = strtr($buffer, [
                "b$resourcesPath" => 'b',
                "a$resourcesPath" => 'a',
                'a/./' => 'a/',
                'b/./' => 'b/',
            ]);

            $notifier($type, $buffer);
        });

        $this->lastExitCode = $process->getExitCode();

        if (!$this->isSuccessful()) {
            $this->errorOutput = $process->getErrorOutput();

            throw new HandlingFailureException(sprintf(
                'An error occurred while running process "%s". Use "%s::getErrorOutput()" for getting the error output.',
                $process->getCommandLine(),
                __CLASS__
            ));
        }

        $this->fs->remove($resourcesPath);

        return $this->lastExitCode;
    }

    public function getExitCode(): int
    {
        return $this->lastExitCode;
    }

    public function isSuccessful(): bool
    {
        // git-diff is also successful if the exit code is `1`
        return in_array($this->lastExitCode, [static::EXIT_SUCCESS_NO_DIFF, static::EXIT_SUCCESS_DIFF], true);
    }

    public function hasDiff(): bool
    {
        return $this->lastExitCode === static::EXIT_SUCCESS_DIFF;
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }

    private function createTmpEnv(): string
    {
        $tmpPath = manala_get_tmp_dir('diff_');

        $this->fs->mkdir($tmpPath);

        for (
            $dump = Dumper::dump(unserialize(file_get_contents("$this->cwd/ansible/.manalize")), $tmpPath);
            $dump->valid();
            $dump->next()
        );

        return $tmpPath;
    }
}
