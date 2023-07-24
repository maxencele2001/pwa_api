<?php

namespace App\Controller;

use App\Entity\CommentNote;
use App\Entity\Student;
use App\Repository\CommentNoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CommentNoteController extends AbstractController
{
    /**
     * @Route("/commentnotes", name="create_comment_note", methods={"POST"})
     */
    public function createCommentNote(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $comment = $data['comment'];
        $note = $data['note'];
        $student = $this->getDoctrine()->getRepository(Student::class)->find(intval($data['id']));

        $user = $this->getUser();

        $commentNote = new CommentNote();
        $commentNote->setComment($comment);
        $commentNote->setNote($note);
        $commentNote->setAuthor($user);
        $commentNote->setStudent($student);

        $entityManager->persist($commentNote);
        $entityManager->flush();

        return new JsonResponse($serializer->normalize($commentNote, null, ['groups' => 'commentByStudent']));
    }

    public function getAllCommentNotes(CommentNoteRepository $commentNoteRepository): JsonResponse
    {
        $commentNotes = $commentNoteRepository->findAll();
        return $this->json($commentNotes);
    }

    public function getCommentNotesByStudent(int $id, CommentNoteRepository $commentNoteRepository, SerializerInterface $serializer): JsonResponse
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['message' => 'Student not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $commentNotes = $commentNoteRepository->findBy(['student' => $student]);
        $data = $serializer->normalize($commentNotes, null, ['groups' => 'commentByStudent']);

        return new JsonResponse($data);
    }

    public function deleteCommentNote(int $id, CommentNoteRepository $commentNoteRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commentNote = $commentNoteRepository->find($id);

        if (!$commentNote) {
            return new JsonResponse(['message' => 'CommentNote not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($commentNote);
        $entityManager->flush();

        return new JsonResponse(['message' => 'CommentNote deleted successfully']);
    }

    public function updateCommentNote(int $id, Request $request, CommentNoteRepository $commentNoteRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commentNote = $commentNoteRepository->find($id);

        if (!$commentNote) {
            return new JsonResponse(['message' => 'CommentNote not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['note'])) {
            $commentNote->setNote($data['note']);
        }

        if (isset($data['comment'])) {
            $commentNote->setComment($data['comment']);
        }

        $entityManager->persist($commentNote);
        $entityManager->flush();

        return new JsonResponse(['message' => 'CommentNote updated successfully']);
    }
}
