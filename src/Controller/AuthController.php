<?php
namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    private $passwordEncoder;
    private $jwtEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        // Récupérer les données de connexion depuis la requête
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];

        // Rechercher l'utilisateur dans la base de données
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Identifiants invalides'], 401);
        }

        // Générer le token JWT
        $token = $this->generateToken($user);

        // Retourner le token dans la réponse JSON
        return new JsonResponse(['token' => $token, 'role' => $user->getRoles()[0]]);
    }

    private function generateToken(User $user): string
    {
        // Générer le token JWT en utilisant LexikJWTAuthenticationBundle
        return $this->jwtEncoder->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600, // Temps de validité du token en secondes (1 heure)
        ]);
    }

    /**
     * @Route("/getroles", name="get_roles", methods={"GET"})
     */
    public function getRoles() {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        // Récupérer le rôle de l'utilisateur
        $roles = $user->getRoles();

        return new JsonResponse(['role' => $roles[0]]);
    }
}
