<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Carousel;
use App\Entity\Customer;
use App\Entity\FaqCategory;
use App\Entity\CarouselImage;
use App\Entity\CarouselElement;
use App\Entity\Faq;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GeneralFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        // Récupération de la liste des images présentes dans 'carousels_images_url' 
        $directory = "./public" . $_ENV['carousels_images_url'];
        $imagesList = array_diff(scandir($directory), array('..', '.'));
        shuffle($imagesList);

        $faker = Factory::create('fr_FR');

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // FAQ
        // Création de catégories
        // Création de Faqs
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        for ($i=0; $i<mt_rand(4,8) ; $i++) { 
            $category = new FaqCategory();
            $category
                ->setLabel($faker->word)
                ->setStatus(true)
            ;
            $manager->persist($category);
            for ($j=0; $j < mt_rand(8,30) ; $j++) { 
                $formats = ['text', 'html'];
                // $rand = mt_rand(0, count($formats)-1);
                $rand = $faker->randomFloat(0,0,1);
                $format = $formats[$rand];
                $faq = new Faq();
                if ( $format == 'html' ) {
                    $paragraphs = $faker->paragraphs(mt_rand(3,8));
                    $content = '<p><code>Démo format html</code> :</p><p>' . implode('</p><p>', $paragraphs) . '</p>';
                } else {
                    $content = 'Format "text" : ' . $faker->paragraphs(mt_rand(3,8), true);
                }
                $title = $faker->words(mt_rand(2,5), true);
                $faq
                    ->setNum($faker->unique()->numberBetween(101,99999))
                    ->setTitle($title)
                    ->setContent($content)
                    ->setCategory($category)
                    ->setFormat($format)
                ;
                $manager->persist($faq);
            }
        }
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // Gestion des rôles utilisateur
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $genders = ['male', 'female'];

        // Génération de la compagnie (Customer) Belair et d'un admin Belair
        $customer = new Customer();
        $customer
            ->setIdBelair(1)
            ->setName("Bel Air Informatique")
            ->setZipCode("91940")
            ->setCity("Les Ulis");
        $manager->persist($customer);

        $user = new User();
        $avatar = 'https://randomuser.me/api/portraits/';
        $avatarId = '1.jpg';
        $avatar .= 'men/' . $avatarId;
        $hash = $this->encoder->encodePassword($user, 'carolles');
        $user
            ->setFirstName('Arnaud')
            ->setLastName('FROUIN')
            ->setEmail('arnaud.frouin@belair-info.fr')
            ->setPicture($avatar)
            ->setHash($hash)
            ->addUserRole($adminRole)
            ->setCustomer($customer);
        $manager->persist($user);

        // Génération de quelques customers et de leurs utilisateurs

        for ($i=0; $i < mt_rand(10,20) ; $i++) { 
            $customer = new Customer();
            $idBelair = $i+1000;
            $nameLong = $faker->company;
            $zipCode = $faker->postcode;
            $city = $faker->city;
            $customer
            ->setIdBelair($idBelair)
            ->setName($nameLong)
            ->setZipCode($zipCode)
            ->setCity($city);
            $manager->persist($customer);
            
            for ($j=0; $j < mt_rand(1,6) ; $j++) { 
                $user = new User();
                $gender = $faker->randomElement($genders);
                $avatar = 'https://randomuser.me/api/portraits/';
                $avatarId = $faker->numberBetween(2, 99) . '.jpg';
                $avatar .= ($gender == 'male' ? 'men/' : 'women/') . $avatarId;
                $hash = $this->encoder->encodePassword($user, 'password');

                $user
                    ->setFirstName($faker->firstName($gender))
                    ->setLastName($faker->lastName)
                    ->setEmail($faker->email)
                    ->setPicture($avatar)
                    ->setHash($hash)
                    ->setCustomer($customer);

                $manager->persist($user);
            }
        }

        // Generation du Carousel de la HomePage
        $carousel = (new Carousel())
            ->setStatus(true)
            ->setName('Home page')
            ->setSlug('homepage')
            ->setDescription("Le carousel qui apparait sur la page d'accueil du site institutionnel")
            ->setStatus(true)
        ;
        $manager->persist($carousel);
        for ($j=0; $j < 5 ; $j++) { 
            $title = substr($faker->words(3, true), 0, 59);
            $element = (new CarouselElement())
                ->setTitle($title)
                ->setDescription($faker->sentence(mt_rand(4,12)))
                ->setImage($faker->randomElement($imagesList))
                ->setCarousel($carousel)
            ;
            $manager->persist($element);
        }


        for ($i=0; $i < 6; $i++) { 
            $carousel = (new Carousel())
                ->setStatus( mt_rand(0,1) )
                ->setName($faker->sentence(3, true))
                ->setDescription($faker->sentence(mt_rand(4,10)))
            ;
            $manager->persist($carousel);
            for ($j=0; $j < mt_rand(2,6) ; $j++) { 
                $title = substr($faker->words(3, true), 0, 59);
                $element = (new CarouselElement())
                    ->setTitle($title)
                    ->setDescription($faker->sentence(mt_rand(4,12)))
                    ->setImage($faker->randomElement($imagesList))
                    ->setCarousel($carousel)
                ;
                $manager->persist($element);
            }
        }

        $manager->flush();
    }
}
