<?php

namespace App\DataFixtures;


use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
//use Cocur\Slugify\Slugify;
use App\Entity\Image;
use App\Entity\Roles;
use App\Entity\Booking;
use App\Entity\Comment;
use App\DataFixtures\AppFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AppFixtures extends Fixture
{
    
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
    //je stocke l'encoder dans ma propriété $this->encoder dont je pourrai me servir dans toutes les fonctions de ma fixture
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('FR-fr');
        //$slugify=new Slugify(); géré depuis l'entité ad


        //ici, nous gérons les roles puisque l'entité Roles ne contient rien
        $adminRole = new Roles();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Julie')
                  ->setLastName('Jeannet')
                  ->setEmail('jeannet.julie@gmail.com')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://randomuser.me/api/portraits/')
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>')
                  ->addUserRole($adminRole);

        $manager->persist($adminUser);


        //ici, nous gérons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for($i = 1; $i <= 10; $i++){
            $user = new User();

            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1 , 99) . '.jpg';
            //if($genre=="male") $picture = $picture.'men/'.$pictureId;
            //else $picture = $picture.'women/'.$pictureId;
            $picture = $picture . ($genre == 'male' ? 'men/' : 'women/'). $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');
            
            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

        $manager ->persist($user);
        $users[] = $user;

        }


        // Nous gérons les annonces
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();
        
            $title=$faker->sentence(5);
            // $coverImage=$faker->imageUrl(1000,350);
            $coverImage = "https://picsum.photos/1200/350?random=" . mt_rand(1, 55000);
            $introduction=$faker->paragraph(2);
            $content ='<p>'.join('<p></p>', $faker->paragraphs(5)). '</p>';
            //$slug=$slugify->slugify($title);

            $user = $users[mt_rand(0, count($users) -1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                //->setSlug($slug) n'est plus nécessaire si géré depuis l'entité ad
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user);

            for ($j = 1; $j <= mt_rand(2,5); $j++){
                $image = new Image();
                
                // $image->setUrl($faker->imageUrl())
                $image->setUrl("https://picsum.photos/640/480?random=" . mt_rand(0, 55000))
                      ->setCaption($faker->sentence()) 
                      ->setAd($ad);
                      
                      $manager->persist($image);

            
            //gestion des réservations
            for($j=1; $j<= mt_rand(0,10); $j++){
                $booking = new Booking;
                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                $duration = mt_rand(3, 10);

                $endDate = (clone $startDate)->modify("+$duration days");
                //Gestion de la date de fin
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) -1)];
                $comment = $faker->paragraph();
                
                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);

                        $manager->persist($booking);

                        //gestion des commentaires
                        if(mt_rand(0,1)){
                            $comment = new Comment();
                            $comment->setContent($faker->paragraph())
                                    ->setRating(mt_rand(1,5))
                                    ->setAuthor($booker)
                                    ->setAd($ad);
                                    //la date de création va se faire automatiquement dans l'entité comment
                            
                            $manager->persist($comment);
                        }

                }
                      
                      
            }   

            $manager->persist($ad);
    }




        $manager->flush();
    }
}
