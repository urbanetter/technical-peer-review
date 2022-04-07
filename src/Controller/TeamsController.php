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

    #[Route('/teams/{name}/avg', name: 'app_teams_avg')]
    public function avg(Team $team, ChartBuilderInterface $chartBuilder, TopicRepository $topicRepository): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_RADAR);
        $data = $topicRepository->teamAverages($team);

        $chart->setData([
            'labels' => array_map(fn($topic) => $topic['name'], $data),
            'datasets' => [
                [
                    'label' => 'Team average',
                    'data' => array_map(fn($topic) => $topic['avg'], $data),
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

        return $this->render('teams/avg.html.twig', [
            'team' => $team,
            'chart' => $chart,
        ]);
    }
}
