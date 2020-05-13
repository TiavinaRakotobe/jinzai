<?php

namespace Admin\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdminUserBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
