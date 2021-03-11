<?php

namespace App\Controller\v2;

use App\Entity\Learner;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

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
}
