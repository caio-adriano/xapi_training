<?php

namespace App\Controller\v2;

use App\Entity\Manager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @Rest\Post("", name="create a manager")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $em      = $this->getDoctrine()->getManager();
        $dateNow = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));

        $requestData              = json_decode($request->getContent(), true);
        $requestData['createdAt'] = $dateNow;
        $requestData['updatedAt'] = $dateNow;

        $manager = new Manager();
        $manager->load($requestData);

        $errors = $validator->validate($manager);
        if (0 !== count($errors)) {
            throw new InvalidParameterException((string) $errors);
        }

        $em->persist($manager);
        $em->flush();

        $view = $this->view($manager);

        return $this->handleView($view);
    }
}
