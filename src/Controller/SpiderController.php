<?php

namespace App\Controller;

use App\Entity\Assessment;
use App\Entity\Developer;
use App\Repository\AssessmentRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class SpiderController extends AbstractController
{
    #[Route('/spider/{key}', name: 'app_spider')]
    public function index(Developer $developer, ChartBuilderInterface $chartBuilder, TopicRepository $topicRepository, AssessmentRepository $assessmentRepository): Response
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
                    'data' => array_map(fn($topic) => $topic['average'], $teamAverage),
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
                    'data' => array_map(fn($topic) => $topic['average'], $externalAssessments),
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

        return $this->render('assessments/spider.html.twig', [
            'developer' => $developer,
            'chart' => $chart,
        ]);
    }

}