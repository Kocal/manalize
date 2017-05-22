<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../bootstrap.php';

define('MANALIZE_HOME', (getenv('MANALIZE_HOME') ?: $_SERVER['HOME']).'/.manala');