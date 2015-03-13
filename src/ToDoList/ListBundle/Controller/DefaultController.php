<?php

namespace ToDoList\ListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ToDoListListBundle:Default:index.html.twig', array('name' => $name));
    }
}
