<?php

namespace App;

use App\Entity\Assessment;
use App\Entity\Developer;
use App\Entity\Team;
use App\Entity\Topic;
use App\Repository\AssessmentRepository;
use App\Repository\TopicRepository;

class TechnicalPeerFeedback
{
    public function __construct(
        private AssessmentRepository $assessmentRepository,
        private TopicRepository $topicRepository
    )
    {
    }

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

    public function getTopicTodos(Team $team): array
    {
        $total = pow($team->getDevelopers()->count(), 2);
        return array_map(fn(Topic $topic) => [
            'name' => $topic->getName(),
            'current' => $topic->getAssessments()->count(),
            'total' => $total,
        ], $team->getTopics()->toArray());
    }

    public function getDeveloperTodos(Team $team): array
    {
        $total = $team->getTopics()->count() * $team->getDevelopers()->count();
        return array_map(fn(Developer $developer) => [
            'name' => $developer->getName(),
            'current' => $developer->getAssessments()->count(),
            'total' => $total,
        ], $team->getDevelopers()->toArray());
    }

    public function getNextTarget(Developer $source): ?Developer
    {
        $assessments = $source->getAssessments()->toArray();
        $topicsCount = $source->getTeam()->getTopics()->count();
        $targets = $source->getTeam()->getDevelopers();

        $targetCounts = [];
        foreach ($assessments as $assessment) {
            /** @var Assessment $assessment */
            $targetId = $assessment->getTarget()->getId();
            $targetCounts[$targetId] = ($targetCounts[$targetId] ?? 0) + 1;
        }

        foreach ($targets as $target) {
            $targetId = $target->getId();
            if (($targetCounts[$targetId] ?? 0) < $topicsCount) {
                return $target;
            }
        }
        return null;
    }

    public function getAssessment(Developer $source, Developer $target, Topic $topic): Assessment
    {
        $assessment = $this->assessmentRepository->findOneBy([
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

        return $assessment;
    }

    public function getTopicAverages(Team $team)
    {
        return $this->topicRepository->teamAverages($team);
    }
}