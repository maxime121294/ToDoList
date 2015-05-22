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
				'errors' => new JsonResponse($errors),
                'affichage' => "nouvelle"
			));
		}

		// Si on n'est pas en POST, alors on affiche le formulaire
		return $this->render('ToDoListListBundle:List:add.html.twig', array(
			'user' => $user,
			'counterTasks' => $counterTasks,
			'form' => $form->createView(),
            'affichage' => "nouvelle"
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
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('ToDoListListBundle:Task');

    	$task = $repository->find($id);

    	if (!$task) {
	        throw $this->createNotFoundException('Task not found');
	    }

        if ($task->getEnabled()) {
            $task->setEnabled(false);
            $em->persist($task);
        }
        else {
            $em->remove($task);
        }
	    $em->flush();

        // on renvoie les coumpteurs pour la maj des badges
        $counter = $repository->getCounterTasks($user->getId());

	    $success['counter'] = array('Tout' => $counter['tout'],
            'EnAttente' => $counter['en_attente'],
            'Terminees' => $counter['terminees'],
            'Supprimees' => $counter['supprimees']
        ); 

	    return new JsonResponse($success);
    }

    /**
     * Restauration d'une tache
     *
     * @Route("/tache/restaurer/{id}", name="restaurer_tache")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function restoreAction($id = '')
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('ToDoListListBundle:Task');

        $task = $repository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $task->setEnabled(true);
        $em->persist($task);
        $em->flush();

        // on renvoie les coumpteurs pour la maj des badges
        $counter = $repository->getCounterTasks($user->getId());

        $success['counter'] = array('Tout' => $counter['tout'],
            'EnAttente' => $counter['en_attente'],
            'Terminees' => $counter['terminees'],
            'Supprimees' => $counter['supprimees']
        ); 

        return new JsonResponse($success);
    }
}
