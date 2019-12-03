<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page}", name="admin_booking_index", requirements={"page": "\d+"})
     */
    public function index(BookingRepository $repo, $page=1 , PaginationService $pagination)
    {     
        $pagination->setEntityClass(Booking::class)
                   ->setPage($page);
                    //grâce à la requestStack, je n'ai plus besoin d'indiquer la route setRoute('admin_booking_index')           

        return $this->render('admin/booking/index.html.twig', [
            //'bookings' => $pagination->getData(),
            //'pages' => $pagination->getPages(),
            //la variable page reste celle que j'ai reçue dans ma route
            //'page' => $page
            //ou pour aller encore plus loin, me contenter de l'instruction suivante
            'pagination' => $pagination
        ]);
    }


    /**
     * Permet de corriger les réservations
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     * @return Response
     */
    
    public function edit(Booking $booking, Request $request, ObjectManager $manager){

        $form = $this->createForm(AdminBookingType::class, $booking);
        $form ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La modification a bien été enregistrée"
            );
        }


        return $this->render('admin/booking/edit.html.twig', [
            'form' =>$form->createView(),
            'booking' => $booking
        ]);

        }

        /**
         * Permet de supprimer une réservation
         * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
         *
         * @param Booking $booking
         * @param ObjectManager $manager
         * @return Response
         */
        public function delete(Booking $booking, ObjectManager $manager){

            $manager->remove($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation a bien été supprimée"
            );

            return $this-> redirectToRoute('admin_booking_index');

        }
    }

