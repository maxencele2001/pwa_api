<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Créez un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com'); // Utilisez l'email comme nom d'utilisateur pour l'admin
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'root'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // Créez un utilisateur non admin
        $user = new User();
        $user->setEmail('john.doe@example.com'); // Utilisez l'email comme nom d'utilisateur pour l'utilisateur non admin
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'root'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
