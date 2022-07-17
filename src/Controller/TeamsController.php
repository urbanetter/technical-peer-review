<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\TopicRepository;
use App\TechnicalPeerFeedback;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class TeamsController extends AbstractController
{
    #[Route('/teams/{name}', name: 'app_teams')]
    public function index(Team $team, TechnicalPeerFeedback $technicalPeerFeedback): Response
    {
        return $this->render('teams/index.html.twig', [
            'team' => $team,
            'topics' => $technicalPeerFeedback->getTopicTodos($team),
            'developers' => $technicalPeerFeedback->getDeveloperTodos($team),
        ]);
    }

    #[Route('/teams/{name}/spider', name: 'app_teams_spider')]
    public function avg(Team $team, ChartBuilderInterface $chartBuilder, TopicRepository $topicRepository): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_RADAR);
        $data = $topicRepository->teamAverages($team);

        $chart->setData([
            'labels' => array_map(fn($topic) => $topic['name'], $data),
            'datasets' => [
                [
                    'label' => 'Max value',
                    'data' => array_map(fn($topic) => $topic['max'], $data),
                    'borderColor' => 'rgba(234, 191, 203, 1)',
                    'backgroundColor' => 'rgba(234, 191, 203, 0.2)',
                ],
                [
                    'label' => 'Min value',
                    'data' => array_map(fn($topic) => $topic['min'], $data),
                    'borderColor' => 'rgba(193, 145, 161, 1)',
                    'backgroundColor' => 'rgba(193, 145, 161, 0.2)',
                ],
                [
                    'label' => 'Team average',
                    'data' => array_map(fn($topic) => $topic['average'], $data),
                    'borderColor' => 'rgba(164, 80, 139, 1)',
                    'backgroundColor' => 'rgba(164, 80, 139, 0.2)',
                ]
            ]
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'min' => 1,
                    'max' => 5,
                ]
            ],

        ]);

        return $this->render('teams/spider.html.twig', [
            'team' => $team,
            'chart' => $chart,
        ]);
    }

    #[Route('/teams/{name}/confidence', name: 'app_teams_bubbles')]
    public function bubbles(Team $team, TechnicalPeerFeedback $technicalPeerFeedback): Response
    {
        $data = $technicalPeerFeedback->teamConfidences($team);
        return $this->render('teams/confidence.html.twig', [
            'team' => $team,
            'data' => $data,
        ]);
    }
}
