<?php

namespace src\Controllers;

use Exception;
use src\Abstracts\AbstractController;
use src\Repositories\HomeRepository;

class HomeController extends AbstractController
{
    private $homeRepository;

    public function __construct()
    {
        $this->homeRepository = new HomeRepository();
    }

    public function displayHomepage(): void
    {
        try {
            // Fetch upcoming events (next 6 events)
            $upcomingEvents = $this->homeRepository->getUpcomingEvents(6);
            
            // Fetch recent events (last 6 events that already happened)
            $recentEvents = $this->homeRepository->getRecentEvents(6);
            
            // Fetch active enterprises (6 most recent)
            $enterprises = $this->homeRepository->getRecentActiveEnterprises(6);
            
            // Fetch active associations (6 most recent)
            $associations = $this->homeRepository->getRecentActiveAssociations(6);
            
            // Fetch featured cities (6 most active cities)
            $featuredCities = $this->homeRepository->getFeaturedCities(6);
            
            // Get statistics
            $stats = [
                'totalEvents' => $this->homeRepository->getTotalEventsCount(),
                'totalEnterprises' => $this->homeRepository->getTotalActiveEnterprisesCount(),
                'totalAssociations' => $this->homeRepository->getTotalActiveAssociationsCount(),
            ];
            
            $this->render('home/accueil', [
                'upcomingEvents' => $upcomingEvents,
                'recentEvents' => $recentEvents,
                'enterprises' => $enterprises,
                'associations' => $associations,
                'stats' => $stats,
                'title' => 'Accueil - Le Média Voironnais',
                'description' => 'Découvrez les événements, entreprises et associations de votre région'
            ]);
        } catch (Exception $e) {
            $this->render('home/accueil', [
                'upcomingEvents' => [],
                'recentEvents' => [],
                'enterprises' => [],
                'associations' => [],
                'stats' => [
                    'totalEvents' => 0,
                    'totalEnterprises' => 0,
                    'totalAssociations' => 0,
                    'totalCities' => 0
                ]
            ]);
        }
    }

    public function displayAuth(): void
    {
        $this->render('home/connexion', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function displayFormInscription(): void
    {
        $this->render('home/inscription', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function displayFormForgetPassword(): void
    {
        $this->render('home/mdp_oublie', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function displayFormResetPassword(): void
    {
        $this->render('home/reinit_mon_mot_de_passe', ['csrf_token' => $this->generateCsrfToken()]);
    }
    public function terms_of_service(): void
    {
        $this->render('home/cgu');
    }
    public function mentions_legales(): void
    {
        $this->render('home/mentions_legales');
    }
    public function page403(): void
    {
        header("HTTP/1.1 403 Forbidden");
        header("Content-Type: text/html; charset=utf-8");
        $this->render('home/403');
    }
    public function page404(): void
    {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: text/html; charset=utf-8");
        $this->render('home/404');
    }
    public function search(): void
    {
        header('Content-Type: application/json');

        // // Verify CSRF token for security
        // if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        //     echo json_encode(['error' => 'Token CSRF invalide', 'results' => []]);
        //     return;
        // }

        // Strict input validation
        if (!isset($_POST['q'])) {
            echo json_encode(['error' => 'Paramètre de recherche manquant', 'results' => []]);
            return;
        }

        $query = trim($_POST['q']);

        // Validate query length (minimum 3 characters, maximum 100)
        if (strlen($query) < 3) {
            echo json_encode(['error' => 'La recherche doit contenir au moins 3 caractères', 'results' => []]);
            return;
        }

        if (strlen($query) > 100) {
            echo json_encode(['error' => 'La recherche ne peut pas dépasser 100 caractères', 'results' => []]);
            return;
        }

        // Sanitize input - remove special characters that could be harmful
        if (!preg_match('/^[a-zA-Z0-9àáâãäåæçèéêëìíîïñòóôõöøùúûüýÿÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝŸ\s\-\'\.]+$/u', $query)) {
            echo json_encode(['error' => 'Caractères non autorisés dans la recherche', 'results' => []]);
            return;
        }

        try {
            $results = [];

            // Search users
            $users = $this->homeRepository->searchUsers($query);
            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'title' => htmlspecialchars($user['firstName'] . ' ' . $user['lastName'], ENT_QUOTES, 'UTF-8'),
                    'image' => $user['avatarPath'] ? htmlspecialchars($user['avatarPath'], ENT_QUOTES, 'UTF-8') : null,
                    'url' => HOME_URL . 'profil/' . htmlspecialchars($user['slug'], ENT_QUOTES, 'UTF-8')
                ];
            }

            // Search events
            $events = $this->homeRepository->searchEvents($query);
            foreach ($events as $event) {
                // Build event URL with city and category slugs: evenements/ville/category/event-slug
                $eventUrl = HOME_URL . 'evenements/';
                if ($event['ville_slug']) {
                    $eventUrl .= htmlspecialchars($event['ville_slug'], ENT_QUOTES, 'UTF-8') . '/';
                }
                if ($event['category_slug']) {
                    $eventUrl .= htmlspecialchars($event['category_slug'], ENT_QUOTES, 'UTF-8') . '/';
                }
                $eventUrl .= htmlspecialchars($event['slug'], ENT_QUOTES, 'UTF-8');

                $results[] = [
                    'type' => 'evenement',
                    'title' => htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'),
                    'image' => $event['bannerPath'] ? htmlspecialchars($event['bannerPath'], ENT_QUOTES, 'UTF-8') : null,
                    'url' => $eventUrl
                ];
            }

            // Search enterprises
            $entreprises = $this->homeRepository->searchEntreprises($query);
            foreach ($entreprises as $entreprise) {
                $results[] = [
                    'type' => 'entreprise',
                    'title' => htmlspecialchars($entreprise['name'], ENT_QUOTES, 'UTF-8'),
                    'image' => $entreprise['logoPath'] ? htmlspecialchars($entreprise['logoPath'], ENT_QUOTES, 'UTF-8') : null,
                    'url' => HOME_URL . 'entreprises/' . htmlspecialchars($entreprise['slug'], ENT_QUOTES, 'UTF-8')
                ];
            }

            // Search associations
            $associations = $this->homeRepository->searchAssociations($query);
            foreach ($associations as $association) {
                $results[] = [
                    'type' => 'association',
                    'title' => htmlspecialchars($association['name'], ENT_QUOTES, 'UTF-8'),
                    'image' => $association['logoPath'] ? htmlspecialchars($association['logoPath'], ENT_QUOTES, 'UTF-8') : null,
                    'url' => HOME_URL . 'associations/' . htmlspecialchars($association['slug'], ENT_QUOTES, 'UTF-8')
                ];
            }

            // Search villes
            $villes = $this->homeRepository->searchVilles($query);
            foreach ($villes as $ville) {
                $results[] = [
                    'type' => 'ville',
                    'title' => htmlspecialchars($ville['name'], ENT_QUOTES, 'UTF-8'),
                    'subtitle' => isset($ville['code_postal']) ? htmlspecialchars($ville['code_postal'], ENT_QUOTES, 'UTF-8') : '',
                    'image' => null,
                    'url' => HOME_URL . 'ville/' . htmlspecialchars($ville['slug'], ENT_QUOTES, 'UTF-8')
                ];
            }

            echo json_encode(['results' => $results]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Erreur lors de la recherche', 'results' => []]);
        }
    }

    public function displayVilleDetails(string $villeSlug): void
    {
        try {
            $ville = $this->homeRepository->getVilleBySlug($villeSlug);

            if (!$ville) {
                $this->page404();
                return;
            }

            // Get city-related data
            $events = $this->homeRepository->getEventsByVille($ville['idVille']);
            $entreprises = $this->homeRepository->getEntreprisesByVille($ville['idVille']);
            $associations = $this->homeRepository->getAssociationsByVille($ville['idVille']);
            $this->render('villes/ville_publique_detail', [
                'ville' => $ville,
                'events' => $events,
                'entreprises' => $entreprises,
                'associations' => $associations
            ]);
        } catch (Exception $e) {
            $this->page404();
        }
    }

    public function listVilles(): void
    {
        try {
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $itemsPerPage = 12; // Nombre de Villes par page
            $offset = ($currentPage - 1) * $itemsPerPage;
            $villes = $this->homeRepository->getAllVilles($itemsPerPage, $offset);
            $totalVilles = $this->homeRepository->getTotalVillesCount();
            $totalPages = (int)ceil($totalVilles / $itemsPerPage);
            if ($currentPage > $totalPages && $totalPages > 0) {
                throw new Exception("Page not found");
            }
            // var_dump($villes);die;
            $this->render(
                'villes/villes_list',
                [
                    'villes' => $villes,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage,
                    'itemsPerPage' => $itemsPerPage,
                    'title' => 'Toutes les villes',
                    'description' => 'Découvrez toutes les villes disponibles sur Le Média Voironnais'
                ]
            );
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(HOME_URL, ['error' => 'true']);
        }
    }
}
