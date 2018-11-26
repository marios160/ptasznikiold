<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Repository\ZdarzenieRepository;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Zdarzenie;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ZdarzeniaController extends Controller {

    /**
     * @Route("/zdarzenia/{currentPage}", defaults={"currentPage" = 1}, 
     *  name="showZdarzenia", requirements={"currentPage": "\d+"})
     */
    public function showAction(Request $request, $currentPage) {
        $session = $request->getSession();
        if ($request->get('find') != "") {
            $session->set('find', $request->get('find'));
        } else {
            $session->set('find', "");
        }
        $limit = 30;
        $em = $this->getDoctrine()->getManager();
        $zdarzenia = $em->getRepository('AppBundle:Zdarzenie')->findBySession($session, $currentPage, $limit);

        $maxPages = ceil($zdarzenia->count() / $limit);
        $thisPage = $currentPage;

        return $this->render('AppBundle:Zdarzenia:show.html.twig', array(
                    'zdarzenia' => $zdarzenia,
                    'maxPages' => $maxPages,
                    'thisPage' => $thisPage
        ));
        //return $this->render('AppBundle:Zdarzenia:show.html.twig', array('zdarzenia' => $zdarzenia));
    }

    /**
     * @Route("/zdarzenia/czynnosc", name="czynnoscZdarzenie")
     */
    public function czynnoscAction(Request $request) {
        $request->get('chck');
        switch ($request->get('czynnosc')) {
            case 'deleteZdarzenie':
                return $this->redirect($this->generateUrl('deleteZdarzenie', array('chck' => $request->get('chck'))));
            case 'addZdarzenie':
                return $this->redirect($this->generateUrl('addZdarzenie'));
            case 'addMultiZdarzenie':
                return $this->redirect($this->generateUrl('addMultiZdarzenie'));
            case 'editZdarzenie':
                return $this->redirect($this->generateUrl('editZdarzenie', array('chck' => $request->get('chck'))));
        }
        //while($request->get('chck'))
        // $request->get('chck')
    }

    /**
     * @Route("/zdarzenia/delete", name="deleteZdarzenie")
     */
    public function deleteAction(Request $request) {
        foreach ($request->get('chck') as $chck) {
            $em = $this->getDoctrine()->getManager();
            $zdarzenie = $em->getReference('AppBundle:Zdarzenie', $chck);
            $em->remove($zdarzenie);
            $em->flush();
        }
        $this->addFlash(
                'notice', 'Usunięto wybrane rekordy!'
        );
        return $this->redirectToRoute('showZdarzenia');
    }

    /**
     * @Route("/zdarzenia/add", name="addZdarzenie")
     */
    public function addAction(Request $request) {
        $typZdarzenia = $this->getDoctrine()->
                        getRepository('AppBundle:TypZdarzenia')->findAll();
        $pracownik = $this->getDoctrine()->
                        getRepository('AppBundle:Pracownik')->findAll();
        $magazyn = $this->getDoctrine()->
                        getRepository('AppBundle:Magazyn')->findAll();
        $karma = $this->getDoctrine()->
                        getRepository('AppBundle:Karma')->findAll();
        $count = 10;
        if(!empty($request->get('ptasznik'))){
            $count = 1;
        }
        return $this->render('AppBundle:Zdarzenia:add.html.twig', array(
                    'typZdarzenia' => $typZdarzenia,
                    'pracownicy' => $pracownik,
                    'magazyny' => $magazyn,
                    'karmy' => $karma,
                    'ptasznik' => $request->get('ptasznik'),
                    'count' => $count));
    }

    /**
     * @Route("/zdarzenia/edit", name="editZdarzenie")
     */
    public function editAction(Request $request) {
        $zdarzenia = $this->getDoctrine()->
                        getRepository('AppBundle:Zdarzenie')->findByIds($request->get('chck'));
        $typZdarzenia = $this->getDoctrine()->
                        getRepository('AppBundle:TypZdarzenia')->findAll();
        $pracownik = $this->getDoctrine()->
                        getRepository('AppBundle:Pracownik')->findAll();
        return $this->render('AppBundle:Zdarzenia:edit.html.twig', array('zdarzenia' => $zdarzenia,
                    'typZdarzenia' => $typZdarzenia,
                    'pracownicy' => $pracownik));
    }

    /**
     * @Route("/zdarzenia/addMulti", name="addMultiZdarzenie")
     */
    public function addMultiAction(Request $request) {
        $typZdarzenia = $this->getDoctrine()->
                        getRepository('AppBundle:TypZdarzenia')->findAll();
        $pracownik = $this->getDoctrine()->
                        getRepository('AppBundle:Pracownik')->findAll();
        return $this->render('AppBundle:Zdarzenia:addMulti.html.twig', array(
                    'typZdarzenia' => $typZdarzenia,
                    'pracownicy' => $pracownik,
                    'ptasznik1' => $request->get('ptasznik1'),
                    'ptasznik2' => $request->get('ptasznik2')
                ));
    }

    /**
     * @Route("/zdarzenia/editZapisz", name="editZapiszZdarzenie")
     */
    public function editZapiszAction(Request $request) {
        //echo$request->get('zdarzenia') ;
        foreach ($request->get('zdarzeniaEdit') as $p) {
            $em = $this->getDoctrine()->getManager();
            $zdarzenie = $em->getRepository('AppBundle:Zdarzenie')->find($p['id']);
            $typZdarzenia = $em->getRepository('AppBundle:TypZdarzenia')->find($p['typZdarzenia']);
            $pracownik = $em->getRepository('AppBundle:Pracownik')->find($p['pracownik']);
            $ptasznik = $em->getRepository('AppBundle:Ptasznik')->findByKodEan($p['ptasznik']);
            if (!$ptasznik) {
                throw $this->createNotFoundException(
                        'No ptasznik found for ean ' . $p['ptasznik']
                );
            }
            $zdarzenie->setTypZdarzenia($typZdarzenia);
            $zdarzenie->setPracownik($pracownik);
            $zdarzenie->setPtasznik($ptasznik[0]);
            $zdarzenie->setData(new \Datetime($p['data']));
            $zdarzenie->setOpis($p['opis']);
            $em->flush();
        }
        $this->addFlash(
                'notice', 'Zapisano zmiany!'
        );
        return $this->redirectToRoute('showZdarzenia');
    }

    /**
     * @Route("/zdarzenia/addZapisz", name="addZapiszZdarzenie")
     */
    public function addZapiszAction(Request $request) {
        //echo$request->get('zdarzenia') ;

        foreach ($request->get('zdarzenie') as $p) {

            if (empty($p['typZdarzenia']) || empty($p['pracownik']) || empty($p['ptasznik'])) {
                continue;
            }
            $em = $this->getDoctrine()->getManager();
            $typZdarzenia = $em->getRepository('AppBundle:TypZdarzenia')->find($p['typZdarzenia']);
            $pracownik = $em->getRepository('AppBundle:Pracownik')->find($p['pracownik']);
            
            $ptasznik = $em->getRepository('AppBundle:Ptasznik')->findByKodEan($p['ptasznik']);
            if (!$ptasznik) {
                throw $this->createNotFoundException(
                        'No ptasznik found for ean ' . $p['ptasznik']
                );
            }
            $zdarzenie = new Zdarzenie();
            $zdarzenie->setTypZdarzenia($typZdarzenia);
            $zdarzenie->setPracownik($pracownik);
            $zdarzenie->setPtasznik($ptasznik[0]);
            $zdarzenie->setData(new \Datetime($p['data']));
            $zdarzenie->setOpis($p['opis']);
            switch ($p['typZdarzenia']) {
                case '1':
                    $karma = $em->getRepository('AppBundle:Karma')->find($p['info']);
                    $zdarzenie->setKarma($karma);
                    break;
                case '2':
                    $zdarzenie->setRozmiar($p['info']);
                    $ptasznik[0]->setAktualnyRozmiar($p['info']);
                    break;
                case '3':
                    $magazyn = $em->getRepository('AppBundle:Magazyn')->find($p['info']);
                    $zdarzenie->setMagazyn($magazyn);
                    $ptasznik[0]->setMagazyn($magazyn);
                    break;
                case '4':
                    $ptasznik[0]->setAktualnyRozmiar("L1");
                    break;

                default:
                    break;
            }
            
            $em->persist($zdarzenie);
            $em->flush();
        }

        $this->addFlash(
                'notice', 'Dodano ptasznika!'
        );
        return $this->redirectToRoute('showZdarzenia');
    }

    /**
     * @Route("/zdarzenia/addMultiZapisz", name="addMultiZapiszZdarzenie")
     */
    public function addMultiZapiszAction(Request $request) {
        //echo$request->get('zdarzenia') ;


        $p = $request->get('zdarzenie');
        if (empty($p['typZdarzenia']) || empty($p['pracownik']) || empty($p['ptasznik1']) || empty($p['ptasznik2'])) {
            return $this->redirectToRoute('showZdarzenia');
        }
        $em = $this->getDoctrine()->getManager();
        $typZdarzenia = $em->getRepository('AppBundle:TypZdarzenia')->find($p['typZdarzenia']);
        $pracownik = $em->getRepository('AppBundle:Pracownik')->find($p['pracownik']);
        $ptasznik = $em->getRepository('AppBundle:Ptasznik')->findByKodEanRange($p['ptasznik1'], $p['ptasznik2']);
        if (!$ptasznik) {
            throw $this->createNotFoundException(
                    'No ptasznik found for ean ' . $p['ptasznik1'] . " - " . $p['ptasznik2']
            );
        }
        foreach ($ptasznik as $el) {

            $zdarzenie = new Zdarzenie();
            $zdarzenie->setTypZdarzenia($typZdarzenia);
            $zdarzenie->setPracownik($pracownik);
            $zdarzenie->setPtasznik($el);
            $zdarzenie->setData(new \Datetime($p['data']));
            $zdarzenie->setOpis($p['opis']);
            $em->persist($zdarzenie);
        }
        $em->flush();


        $this->addFlash(
                'notice', 'Dodano Zdarzenie!'
        );
        return $this->redirectToRoute('showZdarzenia');
    }

}
