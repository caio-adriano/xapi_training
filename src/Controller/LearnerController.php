<?php

namespace App\Controller;

use App\Entity\Learner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

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
        $cache = new RedisAdapter(RedisAdapter::createConnection('redis://localhost'));
        $cachedLearner = $cache->getItem('learner_list.' . $id);
        if (!$cachedLearner->isHit()) {
            $learner = $this->getDoctrine()->getRepository(Learner::class)->find($id);

            $cachedLearner->set(serialize($learner));
            $cache->save($cachedLearner);
        } else {
            $learner = unserialize($cachedLearner->get());
        }
        $cachedLearner->expiresAfter(60);

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

        $cache = new RedisAdapter(RedisAdapter::createConnection($_SERVER['REDIS_PROTOCOL'] . $_SERVER['REDIS_HOST']));
        $newLearner = $cache->getItem('learner_list.' . $learner->getId());
        if (!$newLearner->isHit()) {
            $newLearner->set(serialize(json_decode($this->json($learner)->getContent(), true)));
            $cache->save($newLearner);
            $newLearner->expiresAfter(3600);
        }

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

        $cache = new RedisAdapter(RedisAdapter::createConnection($_SERVER['REDIS_PROTOCOL'] . $_SERVER['REDIS_HOST']));
        $updateLearner = $cache->getItem('learner_list.' . $learner->getId());
        if (!$updateLearner->isHit()) {
            $updateLearner->set(serialize($learner));
            $cache->save($updateLearner);
        } else {
            $learner = unserialize($updateLearner->get());
        }
        $updateLearner->expiresAfter(3600);

        return $this->json($learner);
    }
}
