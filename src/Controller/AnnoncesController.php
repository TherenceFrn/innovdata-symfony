<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnoncesController extends AbstractController
{

    /**
     * @Route("/", name="app_home")
     */

    public function index(AnnonceRepository $repo)
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/AnnoncesController.php',
        // ]);

        // return new Response('Hello World !');

        //return $this->render('annonces/index.html.twig');

        // $annonce = new Annonce();

        // $annonce->setTitle('Ma 4 annonce');
        // $annonce->setDescription("Description de ma quatrième annonce");

        // $em->persist($annonce);
        // $em->flush();

        $annonces = $repo->findAll();

        return $this->render('annonces/index.html.twig', ['annonces' => $annonces]);
    }


    /**
     * @Route("annonce/{id}/view", name="app_annonce_index", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */

    public function visualiser(Request $request, EntityManagerInterface $em): Response
    {

        $annonce = $em->getRepository('App:Annonce')->find($request->get('id'));

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://restcountries.eu/rest/v2/name/'.$annonce->getCountry());
        $content = $response->toArray();

        return $this->render('annonces/annonce.html.twig', ['annonce' => $annonce, "api_content" => $content]);

    }

    /**
     * @Route("annonce", name="app_annonce")
     */

    public function annonce(): Response
    {
        return $this->redirectToRoute('app_home');
    }

}
