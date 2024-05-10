<?php


// src/Controller/ProductController.php
namespace App\Controller;


use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private $productRepository;


    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Liste products
    */
    #[Route('/product', name: 'list_product', methods: ['GET'])]
    public function listPhotoRequest()
    {
        // Récupération de la liste
        $list = $this->productRepository->findAll();

        // Sérialisation des résultats
        $Products = [];
        foreach($list as $l){ 
            $Products[] = [
                'id' => $l->getId(),
                'name' => $l->getName(),
                'description' => $l->getDescription(),
                'price' => $l->getPrice(),
            ];
        }

        return $this->json(["all products" => $Products], 200);
    }
}
