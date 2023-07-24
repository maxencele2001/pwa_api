<?php

namespace App\Controller;

use App\Entity\School;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SchoolController extends AbstractController
{
    /**
     * @Route("/schools", name="create_school", methods={"POST"})
     */
    public function createSchool(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $school = new School();
        $school->setName($data['name']);
        $school->setCountry($data['country']);
        $school->setCity($data['city']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($school);
        $entityManager->flush();

        return new JsonResponse(['message' => 'School created'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/schools/{id}", name="get_school", methods={"GET"})
     */
    public function getSchool(int $id): JsonResponse
    {
        $school = $this->getDoctrine()->getRepository(School::class)->find($id);

        if (!$school) {
            return new JsonResponse(['message' => 'School not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($school);
    }

    /**
     * @Route("/schools/{id}", name="update_school", methods={"PUT"})
     */
    public function updateSchool(Request $request, int $id): JsonResponse
    {
        $school = $this->getDoctrine()->getRepository(School::class)->find($id);

        if (!$school) {
            return new JsonResponse(['message' => 'School not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $school->setName($data['name']);
        $school->setCountry($data['country']);
        $school->setCity($data['city']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'School updated']);
    }

    /**
     * @Route("/schools/{id}", name="delete_school", methods={"DELETE"})
     */
    public function deleteSchool(int $id): JsonResponse
    {
        $school = $this->getDoctrine()->getRepository(School::class)->find($id);

        if (!$school) {
            return new JsonResponse(['message' => 'School not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($school);
        $entityManager->flush();

        return new JsonResponse(['message' => 'School deleted']);
    }

    /**
     * @Route("/schools", name="get_schools", methods={"GET"})
     */
    public function getSchools(SerializerInterface $serializer): JsonResponse
    {
        $schools = $this->getDoctrine()->getRepository(School::class)->findAll();
        return new JsonResponse($schools);
    }

    
}
