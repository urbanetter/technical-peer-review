<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Entity\Topic;
use App\Repository\DeveloperRepository;
use App\Repository\TopicRepository;
use App\TechnicalPeerFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssessmentsController extends AbstractController
{
    #[Route('/assessments/{key}', name: 'app_assessments')]
    public function index(Developer $developer, TechnicalPeerFeedback $technicalPeerFeedback): Response
    {
        return $this->render('assessments/index.html.twig', [
            'developer' => $developer,
            'todos' => $technicalPeerFeedback->getTodos($developer),
            'next' => $technicalPeerFeedback->getNextTarget($developer)
        ]);
    }

    #[Route('/assessments/{source}/for/{target}', name: 'app_assessments_developer')]
    public function developer(
        string $source,
        string $target,
        DeveloperRepository $developerRepository,
        TechnicalPeerFeedback $technicalPeerFeedback,
    ): Response
    {
        $source = $developerRepository->findOneBy(['key' => $source]);
        $target = $developerRepository->find($target);

        if ($target->getTeam()->getId() !== $source->getTeam()->getId()) {
            throw $this->createAccessDeniedException('Nope');
        }

        $title = ($source->getId() === $target->getId()) ? "Self assessment" : "Assessment of " . $target->getName();
        $topics = $source->getTeam()->getTopics();

        $assessments = array_map(fn(Topic $topic) => $technicalPeerFeedback->getAssessment($source, $target, $topic), $topics->toArray());

        return $this->render('assessments/developer.html.twig', [
            'title' => $title,
            'source' => $source,
            'assessments' => $assessments,
        ]);
    }

    #[Route('/assessments/{source}/for/{target}/topic/{topic}', name: 'app_assessments_topic', methods: ['PUT'])]
    public function assessment(
        Request $request,
        string $source,
        string $target,
        string $topic,
        DeveloperRepository $developerRepository,
        TopicRepository $topicRepository,
        TechnicalPeerFeedback $technicalPeerFeedback,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $source = $developerRepository->findOneBy(['key' => $source]);
        $target = $developerRepository->find($target);
        $topic = $topicRepository->find($topic);

        if ($target->getTeam()->getId() !== $source->getTeam()->getId()) {
            throw $this->createAccessDeniedException('Nope');
        }

        $assessment = $technicalPeerFeedback->getAssessment($source, $target, $topic);

        $value = (int) $request->getContent();

        $assessment->setValue($value);
        $entityManager->persist($assessment);
        $entityManager->flush();

        return $this->render('assessments/assessment.html.twig', [
            'assessment' => $assessment,
        ]);
    }
}
