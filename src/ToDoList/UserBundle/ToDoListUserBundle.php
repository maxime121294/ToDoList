<?php

namespace ToDoList\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ToDoListUserBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
