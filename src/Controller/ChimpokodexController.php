<?php

namespace App\Controller;

use App\Entity\Chimpokodex;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChimpokodexRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ChimpokodexController extends AbstractController
{
    /**
     * Renvoie toutes les entrées chimpokomon du chimpokodex
     *
     * @return JsonResponse
     */
    #[Route('/api/chimpokodex', name: 'chimpokodex.getAll', methods: ['GET'])]
    public function getAllChimpokodex(ChimpokodexRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $chimpokodexs = $repository->findAll();
        $jsonChimpokodex = $serializer->serialize($chimpokodexs, 'json');
        return new JsonResponse($jsonChimpokodex,200, [], true);
    }
     
    
    
    /**
     * Get Chimpokodex entry by id
     *
     * @param Chimpokodex $chimpokodex
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/chimpokodex/{idChimpokodex}', name: 'chimpokodex.get', methods: ['GET'])]
    #[ParamConverter("chimpokodex", options: ["id" => "idChimpokodex"])]
    public function getChimpokodex(Chimpokodex $chimpokodex, SerializerInterface $serializer): JsonResponse
    {
        $jsonChimpokodex = $serializer->serialize($chimpokodex, 'json');
        return new JsonResponse($jsonChimpokodex,200, [], true);
    }

    #[Route('/api/chimpokodex', name: 'chimpokodex.post', methods: ['POST'])]
/**
 * Create new Chimpokodex entry
 *
 * @param Request $request
 * @param SerializerInterface $serializer
 * @param EntityManagerInterface $manager
 * @param UrlGeneratorInterface $urlGenerator
 * @return JsonResponse
 */
    public function createChimpokodex(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $chimpokodex = $serializer->deserialize($request->getContent(),Chimpokodex::class,"json");
        $dateNow = new \DateTime();
        $chimpokodex
        ->setStatus('on')
        ->setCreatedAt($dateNow)
        ->setUpdatedAt($dateNow);
        // $chimpokodex = new Chimpokodex();
        // $chimpokodex->setName("Chaussure")->setPvMax(100)->setStatus("on");
        $entityManager->persist($chimpokodex);
        $entityManager->flush();


        $jsonChimpokodex = $serializer->serialize($chimpokodex, 'json');

        $location = $urlGenerator->generate('chimpokodex.get', ["idChimpokodex" => $chimpokodex->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonChimpokodex,Response::HTTP_CREATED, ["Location" => $location], true);
    }
     #[Route('/api/chimpokodex/{id}', name: 'chimpokodex.update', methods: ['PUT'])]
    public function updateChimpokodex(Chimpokodex $chimpokodex,Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedChimpokodex = $serializer->deserialize($request->getContent(), Chimpokodex::class , 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $chimpokodex]);
        $request->toArray();// Recupere le body en format "Array" ( [param1 => param1, param2 => param2, ....]) 
        $updatedChimpokodex->setUpdatedAt(new \DateTime());
        $entityManager->persist($updatedChimpokodex);
        $entityManager->flush();
        
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/chimpokodex/{id}', name: 'chimpokodex.delete', methods: ['DELETE'])]
    public function deleteChimpokodex(Chimpokodex $chimpokodex, EntityManagerInterface $entityManager): JsonResponse
    {

        $entityManager->remove($chimpokodex);
        $entityManager->flush();
        
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    
    //     public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/ChimpokodexController.php',
    //     ]);
    // }

}
