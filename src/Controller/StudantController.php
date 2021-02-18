<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Studant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/studants", name="studant_")
 */
class StudantController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->json([]);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create (Request $request, ValidatorInterface $validator): Response
    {
        $doctrine = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $dateTimeNow = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));

        $studant = new Studant;
        $studant->setFirstName($data['firstName']);
        $studant->setLastName($data['lastName']);
        $studant->setEmail($data['email']);
        $studant->setCreatedAt($dateTimeNow);
        $studant->setUpdatedAt($dateTimeNow);

        $doctrine->persist($studant);
        $doctrine->flush();

        $errors = $validator->validate($studant);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        return $this->json([
            'status' => 201,
            'message' => 'Created',
            'data' => $studant,
        ], 201);
    }

    /**
     * @Route(
     *      "/{studantID}/register/course/{courseID}",
     *      name="register",
     *      methods={"POST"}
     * )
     */
    public function courseRegister(Request $request, $studantID, $courseID): Response
    {
        $courseRepository = $this->getDoctrine()->getRepository(Course::class);
        $studantRepository = $this->getDoctrine()->getRepository(Studant::class);
        $studant = $studantRepository->find($studantID);
        $course = $courseRepository->find($courseID);

        $studant->addCourse($course);
        // $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'actor' => [
                'objectType' => 'Agent',
                'name' => $studant->getFullName(),
                'mbox' => "mailto:{$studant->getEmail()}",
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/registered',
                'display' => [
                    'en-US' => 'registered',
                    'pt-BR' => 'matrículado',
                ],
            ],
            'object' => [
                'objectType' => 'Activity',
                'id' => $request->getUri(),
                'definition' => [
                    'name' => [
                        'en-US' => "registered in a new course",
                        'pt-BR' => 'matrículado em um novo curso'
                    ],
                    'description' => [
                        'en-US' => "registered in the course \"{$course->getName()}\"",
                        'pt-BR' => "matrículado no curso \"{$course->getName()}\"",
                    ],
                ],
            ],
        ]);
    }
}
