<?php

namespace ToDoList\ListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ToDoList\ListBundle\Form\TaskType;
use ToDoList\ListBundle\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
	/**
     * Affiche la page d'accueil contenant les listes de l'utilisateur connecté
     *
     * @Route("/{affichage}", defaults={"affichage":"tout"}, name="listes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($affichage = "tout")
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$id = $user->getId();

    	$repository = $this
    	  ->getDoctrine()
    	  ->getManager()
    	  ->getRepository('ToDoListListBundle:Task')
    	;

    	$tasks = $repository->getTasks($affichage, $id);
    	$counterTasks = $repository->getCounterTasks($id);

        return $this->render('ToDoListListBundle:List:index.html.twig',
        	array(
        		'user' => $user,
        		'tasks' => $tasks,
        		'counterTasks' => $counterTasks,
        		'affichage' => $affichage
        	));
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
    	$id = $user->getId();

    	$repository = $this
    	  ->getDoctrine()
    	  ->getManager()
    	  ->getRepository('ToDoListListBundle:Task')
    	;

    	$counterTasks = $repository->getCounterTasks($id);

		$form = $this->createForm(new TaskType());
		// Si la requête est en POST, c'est que le visiteur a soumis le formulaire
		if ($request->isMethod('POST'))
		{
			$form->handleRequest($request);
			$task = $form->getData();

			if ($form->isValid())
			{
				$user = $this->container->get('security.context')->getToken()->getUser();
				$task->setAuthor($user);
				$em = $this->getDoctrine()->getManager();
				$em->persist($task);
				$em->flush();

				// Si le formulaire est valide on redirige vers la liste de toutes les tâches
				return $this->redirect($this->generateUrl('listes'));
			}

			$errors = array();
			$validator = $this->get('validator');
			$errorList = $validator->validate($task);
			 
			foreach($errorList as $error)
			{
				$errors['errors'][] = $error->getMessage();
			}

			// Sinon on recharge simplement la page d'ajout d'une nouvelle tâche en envoyant les erreurs correspondantes
			return $this->render('ToDoListListBundle:List:add.html.twig', array(
				'user' => $user,
				'counterTasks' => $counterTasks,
				'form' => $form->createView(),
				'errors' => new JsonResponse($errors)
			));
		}

		// Si on n'est pas en POST, alors on affiche le formulaire
		return $this->render('ToDoListListBundle:List:add.html.twig', array(
			'user' => $user,
			'counterTasks' => $counterTasks,
			'form' => $form->createView()
		));
	}

	/**
     * Suppression d'une tache
     *
     * @Route("/tache/supprimer/{id}", name="supprimer_tache")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function deleteAction($id = '')
    {
    	$em = $this->getDoctrine()->getEntityManager();

    	$task = $em->getRepository('ToDoListListBundle:Task')->find($id);

    	if (!$task) {
	        throw $this->createNotFoundException('Task not found');
	    }

	    $em->remove($task);
	    $em->flush();

	    $success = array('success'=>$this->generateUrl('listes')); 

	    return new JsonResponse($success);
    }
}
