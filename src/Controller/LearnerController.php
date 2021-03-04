<?php

namespace App\Controller;

use App\Entity\Learner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/learners", name="learner_")
 */
class LearnerController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        $learners = $this->getDoctrine()->getRepository(Learner::class)->findAll();
        return $this->json($learners);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show($id): Response
    {
        $learner = $this->getDoctrine()->getRepository(Learner::class)->find($id);

        return $this->json($learner);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $doctrine = $this->getDoctrine()->getManager();
        $requestData = $request->getContent();
        $requestData = json_decode($requestData);

        $learner = new Learner();
        $learner->load((array) $requestData);

        $errors = $validator->validate($learner);
        if (count($errors) > 0) {
            return $this->json([
                'message' => (string) $errors,
            ], Response::HTTP_BAD_REQUEST);
        }

        $doctrine->persist($learner);
        $doctrine->flush();

        return $this->json($learner);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(
        Request $request,
        ValidatorInterface $validator,
        $id
        ): Response {

        $doctrine          = $this->getDoctrine()->getManager();
        $learnerRepository = $this->getDoctrine()->getRepository(Learner::class);

        $requestData = json_decode($request->getContent());

        $learner = $learnerRepository->find($id);
        $learner->setLogin($requestData->login)
            ->setReferenceNumber($requestData->referenceNumber ?? null)
            ->setSurname($requestData->surname ?? null)
            ->setFirstName($requestData->firstName ?? null)
            ->setEmail($requestData->email ?? null)
            ->setLanguage($requestData->language ?? null)
            ->setTimezone($requestData->timezone ?? null)
            ->setEntityId($requestData->entityID ?? 1)
            ->setManagerId($requestData->managerID ?? null)
            ->setEnabled($requestData->enabled ?? true)
            ->setEnabledFrom($requestData->enabledFrom ?? null)
            ->setEnabledUntil($requestData->enabledUntil ?? null)
            ->setCustomFields($requestData->customFields ?? null);

        $errors = $validator->validate($learner);
        if (count($errors) > 0) {
            return $this->json([

            ], Response::HTTP_BAD_REQUEST);
        }

        $doctrine->flush();

        return $this->json($learner);
    }
}
