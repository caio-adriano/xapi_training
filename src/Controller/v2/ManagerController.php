<?php

namespace App\Controller\v2;

use App\Entity\Manager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Rest\Route("v2/managers")
 */
class ManagerController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("", name="get_all_managers")
     */
    public function index()
    {
        $managerRepository = $this->getDoctrine()->getRepository(Manager::class);

        $managers = $managerRepository->findAll();

        $view = $this->view($managers);
        return $this->handleView($view);
    }
}
