<?php

namespace App\Service\utilisateurs;

use App\Dto\utilisateurs\UtilisateursDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\utilisateurs\RolesRepository;
use App\Service\utils\BaseService;
use App\Service\utils\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\utilisateurs\UtilisateursRepository;
use Exception;

class UtilisateursService extends BaseService
{
    private UtilisateursRepository $repository;
    private RolesRepository $roleRepository;
    
    private ValidationService $validationService;
    


    public function __construct(EntityManagerInterface $em, UtilisateursRepository $utilisateurRepository, RolesRepository $roleRepository, ValidationService $validationService)
    {
        $this->em = $em;
        $this->repository = $utilisateurRepository;
        $this->roleRepository = $roleRepository;
        $this->validationService = $validationService;
         parent::__construct($em);
    }
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Utilisateurs $user L'utilisateur à créer
     */
    public function createUserByRole(Utilisateurs $user): Utilisateurs
    {

        $plainPassword = $user->getMdp();
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        $user->setMdp($hashedPassword);

        $user = $this->save($user);

        return $user;
    }
    
    public function updateUser(int $idUser, array $data): Utilisateurs
    {
        
        $user = $this->repository->find($idUser);
        if (!$user) {
            throw new Exception('Utilisateur non trouvé pour id=' . $idUser);
        }
        if (isset($data['prenom'])) {
            $prenom = $data['prenom'] ? mb_convert_case($data['prenom'], MB_CASE_TITLE, "UTF-8") : null;
            $user->setPrenom($prenom);
        }

        if (isset($data['nom'])) {
            $nom = mb_strtoupper($data['nom'], 'UTF-8');
            $user->setNom($nom);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['adresse'])) {
            $user->setAdresse($data['adresse']);
        }

        if (isset($data['id_role'])) {
            $role = $this->roleRepository->getById($data['id_role']);
            if (!$role) {
                throw new \InvalidArgumentException('Rôle introuvable');
            }
            $user->setRole($role);
        }


        if (isset($data['mdp']) && !empty($data['mdp'])) {
            $hashedPassword = password_hash($data['mdp'], PASSWORD_BCRYPT);
            $user->setMdp($hashedPassword);
        }

        $this->em->flush();

        return $user;
    }
    public function createUser(Utilisateurs $user, $role_id = 2): Utilisateurs
    {
        $role = $this->roleRepository->find($role_id); // 2 correspond au rôle "Utilisateur"
        if (!$role) {
            throw new Exception("Role non trouvé pour id=" . $role_id);
        }
        $user->setRole($role);
        return $this->createUserByRole($user);
    }

    public function login(string $email, string $plainPassword): ?Utilisateurs
    {
        $user = $this->repository->login($email, $plainPassword);

        return $user;
    }
    public function getValidatedUser(int $userId, string $role): Utilisateurs
    {
        $user = $this->getById($userId);
        $this->validationService->throwIfNull($user, "$role avec l'ID $userId introuvable.");
        return $user;
    }
    public function saveDto(UtilisateursDto $dto): Utilisateurs
    {
        $user = new Utilisateurs();
        $nom = mb_strtoupper($dto->getNom(), 'UTF-8');
        $prenom = $dto->getPrenom() ? mb_convert_case($dto->getPrenom(), MB_CASE_TITLE, "UTF-8") : null;
        $user->setEmail($dto->getEmail());
        $user->setMdp($dto->getMdp());
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setAdresse($dto->getAdresse());
        
        return $this->createUser($user,$dto->getIdRole());
    }
    public function changerMdp(Utilisateurs $user,string $nouveauMdp): Utilisateurs
    {
        $hashedPassword = password_hash($nouveauMdp, PASSWORD_BCRYPT);

       $user->setMdp($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
    public function cloneUtilisateur(Utilisateurs $user): Utilisateurs
    {
        $newUser = new Utilisateurs();
        $newUser->setEmail($user->getEmail());
        $newUser->setMdp($user->getMdp());
        $newUser->setNom($user->getNom());
        $newUser->setPrenom($user->getPrenom());
        $newUser->setAdresse($user->getAdresse());
        $newUser->setRole($user->getRole());
        
        return $newUser;
    }
    public function getAllUtilisateurByRole(int $roleId,?OrderCriteria $orderCriteria = new OrderCriteria()): array
    {

         $conditions = [];
         $conditions[] = new ConditionCriteria('role', $roleId, '=');
        return $this->search($conditions, $orderCriteria);
    }
    public function getAllUtilisateurs(): array
    {
        return $this->getAllUtilisateurByRole(2);
    }
    public function rechercheByNomPrenom(string $nom, string $prenom,OrderCriteria $orderCriteria, PaginationCriteria $paginationCriteria): array
    {
        $conditions = [];
        $conditions[] = new ConditionCriteria('nom', $nom, 'like');
        $conditions[] = new ConditionCriteria('role', 2, '=');
        $conditions[] = new ConditionCriteria('prenom', $prenom, 'like');
        $conditions[] = new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<');

        
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }
}
