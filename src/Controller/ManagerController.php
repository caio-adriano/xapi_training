<?php

namespace App\Controller;

use App\Entity\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
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
        $cache = new RedisAdapter(RedisAdapter::createConnection('redis://localhost'));
        $cachedManager = $cache->getItem('manager_list.' . $id);
        if (!$cachedManager->isHit()) {
            $manager = $this->getDoctrine()->getRepository(Manager::class)->find($id);

            $cachedManager->set(serialize($manager));
            $cache->save($cachedManager);
        } else {
            $manager = unserialize($cachedManager->get());
        }
        $cachedManager->expiresAfter(60);

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

        $cache = new RedisAdapter(RedisAdapter::createConnection($_SERVER['REDIS_PROTOCOL'] . $_SERVER['REDIS_HOST']));
        $newManager = $cache->getItem('manager_list.' . $manager->getId());
        if (!$newManager->isHit()) {
            $newManager->set(serialize(json_decode($this->json($manager)->getContent(), true)));
            $cache->save($newManager);
            $newManager->expiresAfter(3600);
        }

        return $this->json($manager);
    }
}
