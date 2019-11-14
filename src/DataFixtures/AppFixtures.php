<?php

namespace App\DataFixtures;


use App\Entity\Ad;
use App\Entity\Image;
use Faker\Factory;
//use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    


    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('FR-fr');
        //$slugify=new Slugify(); géré depuis l'entité ad
        
        for($i = 1; $i <= 30; $i++){
        
            $title=$faker->sentence(5);
            $coverImage=$faker->imageUrl(1000,350);
            $introduction=$faker->paragraph(2);
            $content ='<p>'.join('<p></p>', $faker->paragraphs(5)). '</p>';
            //$slug=$slugify->slugify($title);

            $ad = new Ad();
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                //->setSlug($slug) n'est plus nécessaire si géré depuis l'entité ad
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,5));

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
