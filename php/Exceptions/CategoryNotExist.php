<?php

namespace App\Exceptions;

class CategoryNotExist extends \Exception
{
    public function __toString()
    {
        __toString("分类不存在");
    }
}
