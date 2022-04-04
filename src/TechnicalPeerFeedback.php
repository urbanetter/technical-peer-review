<?php

namespace App;

use App\Entity\Assessment;
use App\Entity\Developer;

class TechnicalPeerFeedback
{

    public function getTodos(Developer $developer): array
    {
        $team = $developer->getTeam();
        $developers = $team->getDevelopers();
        $total = count($team->getTopics());

        $todos = [];
        foreach ($developers as $target) {
            /** @var Developer $target */
            $todos[$target->getId()] = [
                'name' => ($target->getId() === $developer->getId()) ? 'Self assessment' : 'Assessment of ' . $target->getName(),
                'target' => $target->getId(),
                'current' => 0,
                'total' => $total,
            ];
        }

        foreach ($developer->getAssessments() as $assessment) {
            /** @var Assessment $assessment */
            $targetId = $assessment->getTarget()->getId();
            $todos[$targetId]['current']++;
        }

        return $todos;
    }
}