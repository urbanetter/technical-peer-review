<?php

namespace App\Controller;

use App\Entity\Assessment;
use App\Entity\Developer;
use App\Form\AssessmentType;
use App\Repository\AssessmentRepository;
use App\Repository\DeveloperRepository;
use App\Repository\TopicRepository;
use App\TechnicalPeerFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        ]);
    }

    #[Route('/assessments/{key}/for/{id}', name: 'app_assessments_developer')]
    public function developer(string $key, string $id, DeveloperRepository $developerRepository, AssessmentRepository $assessmentRepository): Response
    {
        $source = $developerRepository->findOneBy(['key' => $key]);
        $target = $developerRepository->find($id);

        $title = ($source->getId() === $target->getId()) ? "Self assessment" : "Assessment of " . $target->getName();
        $topics = $source->getTeam()->getTopics();

        $assessments = $assessmentRepository->findBy([
            'source' => $source,
            'target' => $target,
        ]);

        $assessedTopics = array_map(fn(Assessment $assessment) => $assessment->getTopic(), $assessments);
        $unassessed = array_diff($topics->toArray(), $assessedTopics);

        foreach ($unassessed as $topic) {
            $newAssessment = new Assessment();
            $newAssessment->setSource($source);
            $newAssessment->setTarget($target);
            $newAssessment->setTopic($topic);
            $assessments[] = $newAssessment;
        }

        return $this->render('assessments/developer.html.twig', [
            'title' => $title,
            'assessments' => $assessments,
        ]);
    }

    #[Route('/assessments/{key}/for/{id}/topic/{topic}', name: 'app_assessments_topic', methods: ['PUT'])]
    public function assessment(Request $request, string $key, string $id, string $topic, DeveloperRepository $developerRepository, TopicRepository $topicRepository, AssessmentRepository $assessmentRepository, EntityManagerInterface $entityManager)
    {
        $source = $developerRepository->findOneBy(['key' => $key]);
        $target = $developerRepository->find($id);
        $topic = $topicRepository->find($topic);

        $assessment = $assessmentRepository->findOneBy([
            'source' => $source,
            'target' => $target,
            'topic' => $topic
        ]);

        if (!$assessment) {
            $assessment = new Assessment();
            $assessment->setSource($source);
            $assessment->setTarget($target);
            $assessment->setTopic($topic);
        }

        $value = $request->getContent();

        $assessment->setValue($value);
        $entityManager->persist($assessment);
        $entityManager->flush();

        return $this->render('assessments/assessment.html.twig', [
            'assessment' => $assessment,
        ]);
    }
}
