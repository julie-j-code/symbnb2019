<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdController extends Controller
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
       // $repo = $this->getDoctrine()->getRepository(Ad::class); si je passe repo comme paramètre de la fonction index je n'ai plus besoin de cette classe
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
            //'controller_name' => 'AdController', y a comme un doute...
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route ("ads/new", name="ads_create")
     * 
     * @return Response
     */

    public function create(Request $request, ObjectManager $manager){
        $ad=new Ad();
        
        /*$image = new Image();
        $image->setUrl('http://placehold.it/400x200')
              ->setCaption('Titre1');
        $image2 = new Image();
        $image2->setUrl('http://placehold.it/400x200')
            ->setCaption('Titre2'); 
            
           $ad->addImage($image)
              ->addImage($image2);  */ 
              
        $form=$this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);

            }
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('ads_show',[
                'slug'=>$ad->getSlug()
            ]);
        }

        return $this->render("ad/new.html.twig", [
            'form'=>$form->createView()
        ]);
    }

    /**
     * permet d'afficher le formmulaire d'étition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @return Response
     */
    
     public function edit(Ad $ad, Request $request, ObjectManager $manager){

        $form=$this->createForm(AdType::class, $ad);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);

            }
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées"
            );

            return $this->redirectToRoute('ads_show',[
                'slug'=>$ad->getSlug()
            ]);
        }            



        return $this->render('ad/edit.html.twig', [
            'form'=> $form->createView(),
            'ad'=> $ad
        ]);

    }

    /**
     * Permet d'afficher une seule annonce avec ou sans* le paraconverter
     * @Route ("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */

    public function show($slug, Ad $ad){
    //public function show($slug, AdRepository $repo){$ad = $repo->findOneBySlug($slug); *   
        return $this->render("ad/show.html.twig", [
            'ad'=>$ad
        ]);
    }

}