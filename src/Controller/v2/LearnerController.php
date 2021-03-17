<?php

namespace App\Controller\v2;

use App\Entity\Learner;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Rest\Route("v2.1/learners")
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

    /**
     * @Rest\Post("", name="create a learner")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $requestData = json_decode($request->getContent(), true);

        $learner = new Learner();
        if ($requestData) { $learner->load($requestData); }

        $errors = $validator->validate($learner);
        if (0 !== count($errors)) {
            throw new InvalidParameterException((string) $errors);
        }

        $em->persist($learner);
        $em->flush();

        $view = $this->view($learner);

        return $this->handleView($view);
    }

    /**

     * @Rest\Route("/{id}", name="update a learner", methods={"put", "patch"})
     */
    public function update(Request $request, ValidatorInterface $validator, $id): Response
    {
        $learnerRespositoy = $this->getDoctrine()->getRepository(Learner::class);
        $em                = $this->getDoctrine()->getManager();

        $requestData = json_decode($request->getContent(), true);

        $learner = $learnerRespositoy->find($id);

        if ($requestData) {
            $learner->load($requestData);
        }

        // validate learner
        $errors = $validator->validate($learner);
        if (0 !== count($errors)) {
            throw new InvalidParameterException((string) $errors);
        }

        $em->flush();

        $view = $this->view($learner);

        return $this->handleView($view);
    }
}
