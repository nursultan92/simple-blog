<?php

namespace Nurolopher\BlogBundle\Model;

use Nurolopher\BlogBundle\Model\om\BaseCategory;

class Category extends BaseCategory
{
    public function __toString()
    {
        return $this->getTitle();
    }
}
