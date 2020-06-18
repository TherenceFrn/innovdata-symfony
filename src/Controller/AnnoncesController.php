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

    // fonction qui permet de récuperer toutes les annonces de la BDD, et ensuite de les afficher sous forme de card avec twig et bootstrap
    public function index(AnnonceRepository $repo)
    {
        // on recupere toutes les annonces de la bdd
        $annonces = $repo->findAll();

        return $this->render('annonces/index.html.twig', ['annonces' => $annonces]);
    }


    /**
     * @Route("annonce/{id}/view", name="app_annonce_index", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */

    // fonction qui permet de récuperer une annonce dans la BDD grâce à son id
    // et qui permet d'aller récupérer le drapeau correspondant au pays mentionné dans l'annonce grâce à l'API REST countries
    public function visualiser(Request $request, EntityManagerInterface $em): Response
    {

        $annonce = $em->getRepository('App:Annonce')->find($request->get('id'));

        // on créé un client qui permet de faire des requetes HTTP
        $client = HttpClient::create();
        // ensuite on fait la requete, en fonction du pays demandé dans l'annonce
        $response = $client->request('GET', 'https://restcountries.eu/rest/v2/name/'.$annonce->getCountry());
        // ensuite on récupère le contenu de la réponse, et on le met sour forme de tableau
        $content = $response->toArray();

        // puis on retourne la vue avec l'annonce et le contenu de la reponse de la requete
        return $this->render('annonces/annonce.html.twig', ['annonce' => $annonce, "api_content" => $content]);

    }

}
