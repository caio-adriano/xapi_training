<?php

namespace App\Controller\v2;

use App\Entity\Learner;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route("v2/learners")
 */
class LearnerController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("", name="get_all_learners")
     */
    public function index()
    {
        $learnerRespositoy = $this->getDoctrine()->getRepository(Learner::class);
        $learners = $learnerRespositoy->findAll();

        $view = $this->view($learners);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/{id}", name="get_one_learner")
     */
    public function show($id): Response
    {
        $learnerRespository = $this->getDoctrine()->getRepository(Learner::class);

        $learner = $learnerRespository->find($id);
        $view    = $this->view($learner);

        return $this->handleView($view);
    }
}
