<?php

class PaginationExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('pagination','pagination_render')
        );
    }


    public function getName()
    {
        return 'nurolopher_extension';
    }

    public function paginationRender()
    {

    }
}