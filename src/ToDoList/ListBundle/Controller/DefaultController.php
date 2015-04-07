<?php

namespace ToDoList\ListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ToDoList\ListBundle\Form\TaskType;
use ToDoList\ListBundle\Entity\Task;

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
        return $this->render('ToDoListListBundle:List:index.html.twig', array('name' => $user->getUsername()));
    }

    /**
     * Formulaire d'ajout d'une tache
     *
     * @Route("/tache/ajouter", name="ajouter_tache")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function addAction(Request $request)
	{
		$user = $this->container->get('security.context')->getToken()->getUser();
		$form = $this->createForm(new TaskType());
		// Si la requête est en POST, c'est que le visiteur a soumis le formulaire
		if ($request->isMethod('POST'))
		{

			$form->handleRequest($request);
			$data = $form->getData();

			if ($form->isValid())
			{
				$data->setAuthor($user);
				// On l'enregistre notre objet $advert dans la base de données, par exemple
				$em = $this->getDoctrine()->getManager();
				$em->persist($data);
				$em->flush();

				return $this->redirect($this->generateUrl('listes'));
			}
			// Puis on redirige vers l'index
			return $this->redirect($this->generateUrl('ajouter_tache'));
		}

		// Si on n'est pas en POST, alors on affiche le formulaire
		return $this->render('ToDoListListBundle:List:add.html.twig', array(
			'form' => $form->createView(),
		));
	}
}
