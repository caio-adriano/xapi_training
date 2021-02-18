<?php

namespace App\Controller;

use App\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/courses", name="course_")
 */
class CourseController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        $courses = $this->getDoctrine()->getRepository(Course::class)->findAll();
        return $this->json([
            'data' => $courses,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show ($id): Response
    {
        $course = $this->getDoctrine()->getRepository(Course::class)->find($id);
        return $this->json([
            'data' => $course,
        ]);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create (Request $request): Response
    {
        $doctrine = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $now = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));

        $course = new Course;
        $course->setName($data['name']);
        $course->setDescription($data['description']);
        $course->setSlug($data['slug']);
        $course->setCreatedAt($now);
        $course->setUpdatedAt($now);

        $doctrine->persist($course);
        $doctrine->flush();

        return $this->json([
            'data' => 'Curso criado com sucesso!',
        ]);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT", "PATCH"})
     */
    public function update ($id, Request $request)
    {
        $doctrine = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $now = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));

        $course = $this->getDoctrine()->getRepository(Course::class)->find($id);
        $course->setName($data['name']);
        $course->setDescription($data['description']);
        $course->setSlug($data['slug']);
        $course->setUpdatedAt($now);

        $doctrine->flush();

        return $this->json([
            'data' => 'Curso atualizado com sucesso!',
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"delete"})
     */
    public function delete ($id)
    {
        $doctrine = $this->getDoctrine();
        $course = $doctrine->getRepository(Course::class)->find($id);

        $manager = $doctrine->getManager();

        $manager->remove($course);
        $manager->flush();


        return $this->json([
            'data' => 'Curso deletado com sucesso!',
        ]);
    }
}
