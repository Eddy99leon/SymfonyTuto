<?php


// src/Controller/ProductController.php
namespace App\Controller;


use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\RequestService;


#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private $productRepository;
    private $requestService;
    private $entityManagerInterface;

    public function __construct(ProductRepository $productRepository, RequestService $requestService, EntityManagerInterface $entityManagerInterface)
    {
        $this->productRepository = $productRepository;
        $this->requestService = $requestService;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * Liste products
     */
    #[Route('/product', name: 'list_product', methods: ['GET'])]
    public function listAllProduct()
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

    /**
     * Liste of products greater than Price
     */
    #[Route('/product/greater_than/{price}', name: 'greater_than_price', methods: ['GET'])]
    public function listGreaterThan($price)
    {
        //Récuperation de la liste de produit superieur à price
        $list = $this->productRepository->findByPriceGreaterThan($price);

        //Sérialisation des resultats
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

    /**
     * Liste of products between minPrice and maxPrice
     */
    #[Route('/product/{minPrice}/{maxPrice}', name: 'between_two_price', methods: ['GET'])]
    public function listPriceBetween($minPrice, $maxPrice)
    {
        //Récuperation de la liste de produit entre deux price
        $list = $this->productRepository->findByPriceBetween($minPrice, $maxPrice);

        //Sérialisation des resultsats
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

    /**
     * get product by id
     */
    #[Route('/product/{id}', name: 'find_product_by_id', methods: ['GET'])]
    public function FindProductById($id)
    {
        //Récuperation du produit
        $product = $this->productRepository->find($id);

        // Vérification si le produit existe
        if (!$product) {
            throw $this->createNotFoundException('Le produit avec l\'id ' . $id . ' n\'existe pas.');
        }

        // Sérialisation du produit
        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ];

        return $this->json(["Product" => $data], 200);
    }

    /**
     * post product
     */
    #[Route('/product', name: 'post_product', methods: ['POST'])]
    public function PostProduct()
    {
        // Données de la requête
        [ $name, $description, $price ] = $this->requestService->getFromRequestBody('name', 'description', 'price');

        // Création du product
        $product = new Product();
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);

        $this->entityManagerInterface->persist($product);
        $this->entityManagerInterface->flush();

        // Sérialisation du produit
        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ];

        return $this->json(["message" => "Produit ajouter avec succes!", "data" => $data]);
    }

    /**
     * delete product
     */
    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function DeleteProduct($id)
    {
        // Récupérer le produit à supprimer
        $product = $this->productRepository->find($id);

        // Vérifier si le produit existe
        if (!$product) {
            return new JsonResponse(["message" => "Le produit avec l'id $id n'existe pas."], Response::HTTP_NOT_FOUND);
        }

        // Supprimer le produit
        $this->entityManagerInterface->remove($product);
        $this->entityManagerInterface->flush();

        return $this->json(["message" => "Produit supprimé avec succès!"]);
    }

    /**
     * update product
     */
    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function UpdateProduct($id, Request $request)
    {
        //Récuperation du produit à mettre à jour
        $product = $this->productRepository->find($id);

        //Vérification du product
        if(!$product){
            return new JsonResponse(["message" => "Le produit avec l'id $id n'existe pas."], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les données de la requête
        $requestData = json_decode($request->getContent(), true);

        //eto zao
        dd($requestData);

        return $this->json(["message" => "Product mise à jour avec succès", "data" => $requestData]);
    }
}
