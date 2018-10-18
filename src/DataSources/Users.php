<?php

declare(strict_types=1);

namespace App\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;

class Users implements DataSource
{
    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return [
            ['username' => 'jwage'],
            ['username' => 'ocramius'],
            ['username' => 'ccovey'],
        ];
    }
}
