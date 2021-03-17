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
 * @Route("/v2.0/managers", name="manager")
 */
class ManagerController extends AbstractController
{
    public $cache;

    public function __construct() {
        $this->cache = new RedisAdapter(RedisAdapter::createConnection($_SERVER['REDIS_PROTOCOL'] . $_SERVER['REDIS_HOST']));
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $managerList = [];;

        $page  = $request->query->get('page')  ?? 1;
        $limit = $request->query->get('limit') ?? 5;

        $qb = $this->getDoctrine()->getManager();
        $qb = $qb->createQueryBuilder();
        $qb->select('m.id')
            ->from('App:Manager', 'm')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
        $managerIds = $qb->getQuery()->getResult();

        foreach ($managerIds as $managerId) {
            $manager = [];
            $cachedManager = $this->cache->getItem('manager_list.' . $managerId['id']);
            if (!$cachedManager->isHit()) {
                $manager = $this->getDoctrine()->getRepository(Manager::class)->find($managerId['id']);

                $cachedManager->set(serialize($manager));
                $this->cache->save($cachedManager);
            } else {
                $manager = unserialize($cachedManager->get());
            }
            $managerList[] = $manager;
            $cachedManager->expiresAfter(60);
        }

        return $this->json($managerList);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show($id): Response
    {
        $cachedManager = $this->cache->getItem('manager_list.' . $id);
        if (!$cachedManager->isHit()) {
            $manager = $this->getDoctrine()->getRepository(Manager::class)->find($id);

            $cachedManager->set(serialize($manager));
            $this->cache->save($cachedManager);
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

        $newManager = $this->cache->getItem('manager_list.' . $manager->getId());
        if (!$newManager->isHit()) {
            $newManager->set(serialize(json_decode($this->json($manager)->getContent(), true)));
            $this->cache->save($newManager);
            $newManager->expiresAfter(3600);
        }

        return $this->json($manager);
    }
}
