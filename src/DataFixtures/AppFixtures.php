<?php

namespace App\DataFixtures;

use App\Entity\Candidate;
use App\Entity\Stage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    private static $candidateTitles = [
        'Chef de projet',
        'Developpeur web PHP/Symfony',
        'Developpeur web C#',
        'Developpeur web J2EE',
        'Data Analyst'
    ];
    
    private static $labels = [
        'Yellow',
        'Red',
        'Blue',
        'Purple'
    ];

    private static $stagesTitles = [
        'En cours',
        'Refus',
        'Entretien tétéphonique',
        'Entretien physique'
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $stages = [];

        for($j = 0; $j < 4; $j++){
            $stage = new Stage();
            $stage->setName($faker->randomElement(self::$stagesTitles));
            $manager->persist($stage);
            $stages[] = $stage;
        }

        for($u = 0; $u < 10; $u++){
            $user = new User();
            $chrono = 1;

            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user,"test"));

            $manager->persist($user);

            for($i = 0; $i <mt_rand(5,20); $i++){
                $cadidate = new Candidate();
                $cadidate->setTitle($faker->randomElement(self::$candidateTitles))
                        ->setCompany($faker->company)
                        ->setLink($faker->url)
                        ->setComment($faker->paragraph($nbSentences = 3, $variableNbSentences = true))
                        ->setLabel($faker->randomElement(self::$labels))
                        ->setCreateAt($faker->dateTimeBetween('-6 months'))
                        ->setChrono($chrono)
                        ->setUser($user)
                        ->setStage($faker->randomElement($stages));
                
                $chrono++;
                $manager->persist($cadidate);
            }
        }
        
        $manager->flush();
    }
}
