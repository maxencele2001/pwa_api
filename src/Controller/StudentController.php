<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class StudentController extends AbstractController
{
    /**
     * @Route("/students", name="create_student", methods={"POST"})
     */
    public function createStudent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $school = $entityManager->getRepository(School::class)->find($data['school_id']);

        if (!$school) {
            return new JsonResponse(['message' => 'School not found'], Response::HTTP_NOT_FOUND);
        }

        $student = new Student();
        $student->setName($data['name']);
        $student->setSchool($school);

        $entityManager->persist($student);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Student created'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/students/{id}", name="get_student", methods={"GET"})
     */
    public function getStudent(int $id, SerializerInterface $serializer): JsonResponse
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if (!$student) {
            return new JsonResponse(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }
        $data = $serializer->normalize($student, null, ['groups' => 'student']);

        return new JsonResponse($data);
    }

    /**
     * @Route("/students/{id}", name="update_student", methods={"PUT"})
     */
    public function updateStudent(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        $school = $this->getDoctrine()->getRepository(School::class)->find(intval($data['school_id']));
        if (!$school) {
            return new JsonResponse(['message' => 'School not found'], Response::HTTP_NOT_FOUND);
        }

        $student->setName($data['name']);
        $student->setSchool($school);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Student updated']);
    }

    /**
     * @Route("/students/{id}", name="delete_student", methods={"DELETE"})
     */
    public function deleteStudent(int $id): JsonResponse
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($student);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Student deleted']);
    }

    /**
     * @Route("/students", name="get_students", methods={"GET"})
     */
    public function getStudents(): JsonResponse
    {
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        
        return new JsonResponse($students);
    }
}
