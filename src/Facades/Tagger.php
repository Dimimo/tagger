<?php
/**
 *  Copyright (c) 2016-2021 Puerto Parrot. Written by Dimitri Mostrey for https// www.puertoparrot.com
 *  Contact me at admin@puertoparrot.com or dmostrey@yahoo.com
 */

namespace Dimimo\Tagger\Facades;

use Illuminate\Support\Facades\Facade;

class Tagger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tagger';
    }
}
