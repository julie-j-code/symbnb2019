<?php

namespace App\DataFixtures;


use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
//use Cocur\Slugify\Slugify;
use App\Entity\Image;
use App\DataFixtures\AppFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
                 ->setDescription('<p>'.join('<p></p>', $faker->paragraphs(3)).'</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

        $manager ->persist($user);
        $users[] = $user;

        }


        // Nous gérons les annonces
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();
        
            $title=$faker->sentence(5);
            $coverImage=$faker->imageUrl(1000,350);
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
                
                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence()) 
                      ->setAd($ad);
                      
                      $manager->persist($image);
                      
            }   

            $manager->persist($ad);
    }

        $manager->flush();
    }
}
