<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class ApiController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    #[Route('/api', name: 'api')]
    // url racine
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to the task manager api!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/createTask', name: 'createTask', methods: ['POST'])]
    // fonction qui créé les tâches
    public function createTask(Request $request): JsonResponse
    {
        $task = new Task();

        $data = json_decode($request->getContent(), true);

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStatus(true);
        $this->em->persist($task);
        $this->em->flush();

        return $this->json([
            'message' => 'The task has been successfully updated!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/deleteTask/{id}', name: 'deleteTask', methods: ['GET'])]
    // fonction qui supprime les tâches
    public function deleteTask(?Task $task): JsonResponse
    {
        if (!$task) {
            return $this->json([
                'error' => 'Tâche non trouvée.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        $this->em->remove($task);
        $this->em->flush();

        return $this->json([
            'message' => 'The task has been successfully removed!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/findTask/{id}', name: 'findTask', methods: ['GET'])]
    // fonction qui retrouve une tâche avec son id
    public function findTask(?Task $task): JsonResponse
    {

        if (!$task) {
            return $this->json([
                'error' => 'Tâche non trouvée.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json([
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->isStatus() ? "Active" : "Terminée",
        ]);
    }

    #[Route('/api/updateTask/{id}/', name: 'updateTask', methods: ['PUT'])]
    // fonction qui met à jour les données d'une tâche
    public function updateTask(Request $request, ?Task $task): JsonResponse
    {
        if (!$task) {
            return $this->json([
                'error' => 'Tâche non trouvée.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $this->em->persist($task);
        $this->em->flush();

        return $this->json([
            'message' => 'The task has been successfully updated!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/finishTask/{id}/', name: 'finishTask', methods: ['PUT'])]
    // fonction qui met à jour le statut d'une tâche
    public function finishTask(?Task $task): JsonResponse
    {
        if (!$task) {
            return $this->json([
                'error' => 'Tâche non trouvée.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $task->setStatus(false);
        $this->em->persist($task);
        $this->em->flush();

        return $this->json([
            'message' => 'The task has been successfully updated!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/getAllTasks/', name: 'getAllTasks', methods: ['GET'])]
    // fonction qui récupère toutes les tâches
    public function getAllTasks(): JsonResponse
    {
        $repository = $this->em->getRepository(Task::class);
        $tasks = $repository->findAll();
       
        $tasks = json_encode($tasks);

        return new JsonResponse($tasks, json: JSON_UNESCAPED_UNICODE);
    }
}
