<?php

namespace App\Crawlers;

use App\Crawlers\Categroy;

class StartCrawler
{
    public function getCategore()
    {
        $categroyOBJ = new Categroy();

        $categroyOBJ->run();
    }
}
