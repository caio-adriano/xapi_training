<?php

namespace App\Controller\v2;

use App\Entity\Manager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Rest\Route("v2/managers")
 */
class ManagerController extends AbstractFOSRestController
{
    public $cache;

    public function __construct()
    {
        $this->cache = new RedisAdapter(RedisAdapter::createConnection($_SERVER['REDIS_PROTOCOL'] . $_SERVER['REDIS_HOST']));
    }

    /**
     * @QueryParam(name="limit", default=5)
     * @QueryParam(name="page", default=1)
     *
     * @Rest\Get("", name="get_all_managers")
     */
    public function index(ParamFetcher $paramFetcher)
    {
        $limit = $paramFetcher->get('limit');
        $page  = $paramFetcher->get('page');

        $managerRepository = $this->getDoctrine()->getRepository(Manager::class);

        $managerIDs = $managerRepository->getIDsPaginated($page, $limit);

        foreach ($managerIDs as $managerID) {
            $cacheManager = $this->cache->getItem('manager_v2_list.' . $managerID['id']);

            if (!$cacheManager->isHit()) {
                $manager = $managerRepository->find($managerID['id']);

                $cacheManager->set(serialize($manager));
                $this->cache->save($cacheManager);
            } else {
                $manager = unserialize($cacheManager->get());
            }

            $managerList[] = $manager;
            $cacheManager->expiresAfter(60);
        }

        $view = $this->view($managerList);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/{id}", name="get_one_manager")
     */
    public function show($id): Response
    {
        $managerRepository = $this->getDoctrine()->getRepository(Manager::class);
        $cacheManager      = $this->cache->getItem('manager_v2_list.' . $id);

        if (!$cacheManager->isHit()) {
            $manager = $managerRepository->find($id);

            $cacheManager->set(serialize($manager));
            $this->cache->save($cacheManager);
        } else {
            $manager = unserialize($cacheManager->get());
        }

        $cacheManager->expiresAfter(60);

        $view = $this->view($manager);

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

        $newManager = $this->cache->getItem('manager_v2_list.' . $manager->getId());
        if (!$newManager->isHit()) {
            $serializedManager = serialize(json_decode($this->json($manager)->getContent(), true));

            $newManager->set($serializedManager);
            $this->cache->save($newManager);
            $newManager->expiresAfter(3600);
        }

        $view = $this->view($manager);

        return $this->handleView($view);
    }
}
