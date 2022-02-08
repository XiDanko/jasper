<?php

namespace XiDanko\Jasper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \XiDanko\Jasper\Jasper availablePrinters()
 * @method static \XiDanko\Jasper\Jasper listParameters(string $report)
 * @method static \XiDanko\Jasper\Jasper process(string $report, string $data)
 * @method static \XiDanko\Jasper\Jasper withParameters(array $parameters, bool $validate)
 * @method static \XiDanko\Jasper\Jasper view()
 * @method static \XiDanko\Jasper\Jasper print(int $numberOfCopies = 1, ?string $printerName = null)
 * @method static \XiDanko\Jasper\Jasper pdf()
 *
 * @see \XiDanko\Jasper\Jasper
 */

class Jasper extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'jasper';
    }
}
