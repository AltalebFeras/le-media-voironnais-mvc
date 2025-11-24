<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Repositories\FriendRepository;
use src\Repositories\UserRepository;
use Exception;

class FriendController extends AbstractController
{
    private $friendRepo;
    private $userRepo;

    public function __construct()
    {
        $this->friendRepo = new FriendRepository();
        $this->userRepo = new UserRepository();
    }

    /**
     * Display user's friends list
     */
    public function displayFriendsList(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $friendsPerPage = 12;

            $friends = $this->friendRepo->getUserFriends($idUser, $currentPage, $friendsPerPage);
            $totalFriends = $this->friendRepo->countUserFriends($idUser);
            $totalPages = (int)ceil($totalFriends / $friendsPerPage);

            // Get pending requests
            $pendingRequests = $this->friendRepo->getPendingRequests($idUser);
            $sentRequests = $this->friendRepo->getSentRequests($idUser);

            $this->render('user/mes_amis', [
                'friends' => $friends,
                'pendingRequests' => $pendingRequests,
                'sentRequests' => $sentRequests,
                'totalFriends' => $totalFriends,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'title' => 'Mes amis'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('dashboard', ['error' => 'true']);
        }
    }

    /**
     * Send friend request
     */
    public function sendFriendRequest(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $friendUiid = isset($_POST['friend_uiid']) ? htmlspecialchars(trim($_POST['friend_uiid'])) : null;

            if (!$friendUiid) {
                throw new Exception("Utilisateur invalide");
            }

            // Get friend ID from UIID
            $friend = $this->userRepo->getUserByUiid($friendUiid);
            if (!$friend) {
                throw new Exception("Utilisateur introuvable");
            }

            $idFriend = $friend->getIdUser();

            // Validate that user is not trying to add themselves
            if ($idUser === $idFriend) {
                throw new Exception("Vous ne pouvez pas vous ajouter vous-même comme ami");
            }

            $this->friendRepo->sendFriendRequest($idUser, $idFriend);

            // Send notification to the friend
            $currentUser = $this->userRepo->getUserById($idUser);
            $type = 'invitation';
            $title = 'Nouvelle demande d\'amitié';
            $message = $currentUser->getFirstName() . ' ' . $currentUser->getLastName() . ' souhaite devenir votre ami.';
            $url = 'mes_amis';
            $priority = 0;

            $this->sendNotification($idFriend, $type, $title, $message, $url, $priority);

            $_SESSION['success'] = "Demande d'amitié envoyée avec succès";
            $this->redirect('profil/' . $friend->getSlug());
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_amis', ['error' => 'true']);
        }
    }

    /**
     * Accept friend request
     */
    public function acceptFriendRequest(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $friendUiid = isset($_POST['friend_uiid']) ? htmlspecialchars(trim($_POST['friend_uiid'])) : null;

            if (!$friendUiid) {
                throw new Exception("Utilisateur invalide");
            }

            $friend = $this->userRepo->getUserByUiid($friendUiid);
            if (!$friend) {
                throw new Exception("Utilisateur introuvable");
            }

            $idFriend = $friend->getIdUser();
            $this->friendRepo->acceptFriendRequest($idUser, $idFriend);

            // Send notification to the friend
            $currentUser = $this->userRepo->getUserById($idUser);
            $type = 'invitation';
            $title = 'Demande d\'amitié acceptée';
            $message = $currentUser->getFirstName() . ' ' . $currentUser->getLastName() . ' a accepté votre demande d\'amitié.';
            $url = 'mes_amis';
            $priority = 0;

            $this->sendNotification($idFriend, $type, $title, $message, $url, $priority);

            $_SESSION['success'] = "Demande d'amitié acceptée";
            $this->redirect('mes_amis');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_amis', ['error' => 'true']);
        }
    }

    /**
     * Refuse friend request
     */
    public function refuseFriendRequest(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $friendUiid = isset($_POST['friend_uiid']) ? htmlspecialchars(trim($_POST['friend_uiid'])) : null;

            if (!$friendUiid) {
                throw new Exception("Utilisateur invalide");
            }

            $friend = $this->userRepo->getUserByUiid($friendUiid);
            if (!$friend) {
                throw new Exception("Utilisateur introuvable");
            }

            $idFriend = $friend->getIdUser();
            $this->friendRepo->refuseFriendRequest($idUser, $idFriend);

            $_SESSION['success'] = "Demande d'amitié refusée";
            $this->redirect('mes_amis');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_amis', ['error' => 'true']);
        }
    }

    /**
     * Remove friend
     */
    public function removeFriend(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $friendUiid = isset($_POST['friend_uiid']) ? htmlspecialchars(trim($_POST['friend_uiid'])) : null;

            if (!$friendUiid) {
                throw new Exception("Utilisateur invalide");
            }

            $friend = $this->userRepo->getUserByUiid($friendUiid);
            if (!$friend) {
                throw new Exception("Utilisateur introuvable");
            }

            $idFriend = $friend->getIdUser();
            $this->friendRepo->removeFriend($idUser, $idFriend);

            $_SESSION['success'] = "Ami supprimé de votre liste";
            $this->redirect('mes_amis');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_amis', ['error' => 'true']);
        }
    }

    /**
     * Block friend
     */
    public function blockFriend(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $friendUiid = isset($_POST['friend_uiid']) ? htmlspecialchars(trim($_POST['friend_uiid'])) : null;

            if (!$friendUiid) {
                throw new Exception("Utilisateur invalide");
            }

            $friend = $this->userRepo->getUserByUiid($friendUiid);
            if (!$friend) {
                throw new Exception("Utilisateur introuvable");
            }

            $idFriend = $friend->getIdUser();
            $this->friendRepo->blockFriend($idUser, $idFriend);

            $_SESSION['success'] = "Utilisateur bloqué";
            $this->redirect('mes_amis');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_amis', ['error' => 'true']);
        }
    }

    /**
     * Search for users to add as friends (AJAX)
     */
    public function searchUsers(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $idUser = $_SESSION['idUser'] ?? null;
            if (!$idUser) {
                echo json_encode(['success' => false, 'error' => 'Utilisateur non authentifié']);
                exit;
            }

            $query = isset($_POST['query']) ? trim($_POST['query']) : '';

            if (strlen($query) < 2) {
                echo json_encode(['success' => false, 'error' => 'La recherche doit contenir au moins 2 caractères']);
                exit;
            }

            $users = $this->friendRepo->searchUsers($idUser, $query);

            $results = [];
            foreach ($users as $user) {
                $results[] = [
                    'uiid' => $user['uiid'],
                    'slug' => $user['slug'],
                    'name' => $user['firstName'] . ' ' . $user['lastName'],
                    'avatar' => $user['avatarPath'] ?? BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png',
                    'bio' => $user['bio'] ?? ''
                ];
            }

            echo json_encode(['success' => true, 'users' => $results]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}
