<?php

namespace ToDoList\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction()
	{
		$user = $this->container->get('security.context')->getToken()->getUser();

		if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'))
		{
			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}
		else
		{
			return $this->redirect($this->generateUrl('listes'));
		}
	}
}
