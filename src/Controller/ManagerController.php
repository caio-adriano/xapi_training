<?php

namespace App\Controller;

use App\Entity\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/managers", name="manager")
 */
class ManagerController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        $manager = $this->getDoctrine()->getRepository(Manager::class)->findAll();
        return $this->json($manager);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show($id): Response
    {
        $managerRepository = $this->getDoctrine()->getRepository(Manager::class);
        $manager = $managerRepository->find($id);

        return $this->json($manager);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $doctrine = $this->getDoctrine()->getManager();

        $requestData = $request->getContent();
        $requestData = json_decode($requestData);

        $manager = new Manager();
        $manager->setName($requestData->name);
        $manager->setCreatedAt($dateNow);
        $manager->setUpdatedAt($dateNow);

        $errors = $validator->validate($manager);
        if (count($errors) > 0) {
            return $this->json([
                'message' => (string) $errors,
            ], 400);
        }
        $doctrine->persist($manager);
        $doctrine->flush();


        return $this->json($manager);
    }
}
