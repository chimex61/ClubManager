<?php
// \src\FB\TournamentBundle\Controller\TournamentController.php

namespace FB\TournamentBundle\Controller;


use FB\TournamentBundle\Entity\Season;
use FB\TournamentBundle\Entity\Tournament;
use FB\TournamentBundle\Form\TournamentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TournamentController extends Controller
{
    public function indexAction(Request $request)
    {
        // récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();

        $season = new Season();
        $form = $this->createFormBuilder($season)
            ->add('name', 'entity', array( 'class' => 'FB\TournamentBundle\Entity\Season',
                'property' => 'name',
                'empty_value' => "Sélectionner une saison"))
            ->add('filter', 'submit')
            ->getForm();

        $form->handleRequest($request);
        // récupération de la liste des tournois stockés en BDD
        if ($form->isValid())
            $tournaments = $em->getRepository('FBTournamentBundle:Tournament')->findBy(array('season' => $form->get('name')->getData()->getId()));
        else
            $tournaments = $em->getRepository('FBTournamentBundle:Tournament')->findAll();
        return $this->render('FBTournamentBundle:Tournament:index.html.twig', array('listTournament' => $tournaments, 'form' => $form->CreateView()));
    }

    public function addAction(Request $request)
    {
        $tournament = new Tournament();

        // Création du formulaire de saisie
        $form = $this->get('form.factory')->create(new TournamentType(), $tournament);

        // On fait le lien Requête<->formulaire
        $form->handleRequest($request);

        // on vérife la validité des donnnées du formulaire
        if($form->isValid()){
            // sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($tournament);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Tournoi enregitré');

            //Clear the form
            unset($tournament);
            unset($form);
            $tournament = new Tournament();
            $form = $this->get('form.factory')->create(new TournamentType(), $tournament);
        }
        return $this->render('FBTournamentBundle:Tournament:add.html.twig', array('form' => $form->createView()));
    }

    public function updateAction($id, Request $request)
    {
        // récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
        $tournament = $em->getRepository('FBTournamentBundle:Tournament')->find($id);

        if ($tournament === null)
            throw new NotFoundHttpException("Le tournoi d'id".$id." n'existe pas");
        else
        {
            $form = $this->get('form.factory')->create(new TournamentType(), $tournament);
            $form->handleRequest($request);

            if ($form->isValid()){
                $em->persist($tournament);
                $em->flush();
                $this->get('session')->getFlashBag()->add('notice', 'Tournoi mis à jour');

                // récupération des infos de la bases
                $tournaments = $em->getRepository('FBTournamentBundle:Tournament')->findAll();
                //affichage de la liste des set de maillot
                return $this->render('FBTournamentBundle:Tournament:index.html.twig', array('listTournament' => $tournaments));
            }
        }
        return $this->render('FBTournamentBundle:Tournament:update.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($id)
    {

    }

}