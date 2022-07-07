<?php

namespace App\Controller;

use App\Entity\Confidence;
use App\Entity\Developer;
use App\Entity\Topic;
use App\Repository\ConfidenceRepository;
use App\TechnicalPeerFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfidenceController extends AbstractController
{
    #[Route('/confidences/{key}', name: 'app_confidences')]
    public function confidences(Developer $developer, TechnicalPeerFeedback $technicalPeerFeedback): Response
    {
        $confidences = $technicalPeerFeedback->getConfidences($developer);

        return $this->render('confidences/developer.html.twig', ['developer' => $developer, 'confidences' => $confidences]);
    }

    #[Route('/confidences/{key}/topic/{topic}', name: 'app_confidence')]
    public function vote(Request $request, Developer $developer, Topic $topic, ConfidenceRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $confidence = $repository->findOneBy(['developer' => $developer, 'topic' => $topic]);
        if (!$confidence) {
            $confidence = new Confidence();
            $confidence->setDeveloper($developer);
            $confidence->setTopic($topic);
        }

        $value = (int) $request->getContent();
        $confidence->setConfidence($value);

        $entityManager->persist($confidence);
        $entityManager->flush();

        return $this->render('confidences/confidence.html.twig', ['confidence' => $confidence]);
    }
}