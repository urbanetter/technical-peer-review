<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Topic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class TeamsController extends AbstractController
{
    #[Route('/teams/{name}', name: 'app_teams')]
    public function index(Team $team, ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_RADAR);
        $values = [2,3,5,4];


        $chart->setData([
            'labels' => array_map(fn(Topic $topic) => $topic->getName(), $team->getTopics()->toArray()),
            'datasets' => [
                [
                    'label' => 'Team average',
                    'data' => $values,
                ]
            ]
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'min' => 0,
                    'max' => max($values),
                ]
            ]
        ]);

        return $this->render('teams/index.html.twig', [
            'team' => $team,
            'chart' => $chart,
        ]);
    }
}
