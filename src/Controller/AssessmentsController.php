<?php

namespace App\Controller;

use App\Entity\Assessment;
use App\Entity\Developer;
use App\Entity\Topic;
use App\Repository\AssessmentRepository;
use App\Repository\DeveloperRepository;
use App\Repository\TopicRepository;
use App\TechnicalPeerFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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

    #[Route('/assessments/{key}/spider', name: 'app_assessments_spider')]
    public function spider(Developer $developer, ChartBuilderInterface $chartBuilder, TopicRepository $topicRepository, AssessmentRepository $assessmentRepository)
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_RADAR);
        $teamAverage = $topicRepository->teamAverages($developer->getTeam());
        $selfAssessments = $assessmentRepository->selfAssessments($developer);
        $externalAssessments = $topicRepository->external($developer);

        $chart->setData([
            'labels' => array_map(fn($topic) => $topic['name'], $teamAverage),
            'datasets' => [
                [
                    'label' => 'Team average',
                    'data' => array_map(fn($topic) => $topic['avg'], $teamAverage),
                    'borderColor' => 'rgba(234, 191, 203, 1)',
                    'backgroundColor' => 'rgba(234, 191, 203, 0.2)',
                ],
                [
                    'label' => 'Self assessment',
                    'data' => array_map(fn(Assessment $assessment) => $assessment->getValue(), $selfAssessments),
                    'borderColor' => 'rgba(193, 145, 161, 1)',
                    'backgroundColor' => 'rgba(193, 145, 161, 0.2)',
                ],
                [
                    'label' => 'External assessment',
                    'data' => array_map(fn($topic) => $topic['avg'], $externalAssessments),
                    'borderColor' => 'rgba(164, 80, 139, 1)',
                    'backgroundColor' => 'rgba(164, 80, 139, 0.2)',
                ],
            ]
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'min' => 0,
                    'max' => 5,
                ]
            ],

        ]);

        return $this->render('assessments/avg.html.twig', [
            'developer' => $developer,
            'chart' => $chart,
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

        $assessment = $technicalPeerFeedback->getAssessment($source, $target, $topic);

        $value = $request->getContent();

        $assessment->setValue($value);
        $entityManager->persist($assessment);
        $entityManager->flush();

        return $this->render('assessments/assessment.html.twig', [
            'assessment' => $assessment,
        ]);
    }
}
