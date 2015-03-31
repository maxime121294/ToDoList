<?php

namespace ToDoList\ListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
	/**
     * Affiche la page d'accueil contenant les listes de l'utilisateur connecté
     *
     * @Route("/", name="listes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
        return $this->render('ToDoListListBundle:Default:index.html.twig', array('name' => $user->getUsername()));
    }
}
