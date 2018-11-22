<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PracownikType;
use AppBundle\Entity\Pracownik;

class PracownicyController extends Controller {

    public function registerAction() {
        $pracownik = new Pracownik();
        $form = $this->createForm(new PracownikType(), $pracownik, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
                        'AppBundle:Pracownicy:register.html.twig', array('form' => $form->createView())
        );
    }

    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new PracownikType(), new Pracownik());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $pracownik = $form->getData();

            $em->persist($pracownik->getUser());
            $em->flush();

            return $this->redirectToRoute('showPracownicy');
        }

        return $this->render(
                        'AppBundle:Pracownicy:register.html.twig', array('form' => $form->createView())
        );
    }

    /**
     * @Route("/pracownicy", name="showPracownicy")
     */
    public function showAction() {
        $pracownicy = $this->getDoctrine()->getRepository('AppBundle:Pracownik')->findAll();

        return $this->render('AppBundle:Pracownicy:show.html.twig', array('pracownicy' => $pracownicy));
    }

    /**
     * @Route("/pracownicy/add", name="addPracownik")
     */
    public function addAction(Request $request) {
        $pracownik = new Pracownik();
        $form = $this->createForm(PracownikType::class, $pracownik);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pracownik);
            $em->flush();
            $this->addFlash(
                    'notice', 'Dodano pracownika!'
            );
            return $this->redirectToRoute('showPracownicy');
        }

        return $this->render('AppBundle:Pracownicy:add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/pracownicy/delete", name="deletePracownik")
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getReference('AppBundle:Pracownik', $request->get('id'));
        $em->remove($user);
        $em->flush();
        $this->addFlash(
                'notice', 'Usunięto pracownika!'
        );
        return $this->redirectToRoute('showPracownicy');
    }

    /**
     * @Route("/pracownicy/edit", name="editPracownik")
     */
    public function editAction(Request $request) {
        $pracownik = $this->getDoctrine()->getRepository('AppBundle:Pracownik')->find($request->get('id'));
        $form = $this->createForm(PracownikType::class, $pracownik);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pracownik);
            $em->flush();
            $this->addFlash(
                    'notice', 'Zapisano zmiany!'
            );
            return $this->redirectToRoute('showPracownicy');
        }

        return $this->render('AppBundle:Pracownicy:edit.html.twig', array('form' => $form->createView()));
    }

}
