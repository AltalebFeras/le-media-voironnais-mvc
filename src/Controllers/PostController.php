<?php

namespace src\Controllers;

use DateTime;
use Exception;
use src\Abstracts\AbstractController;
use src\Models\Post;
use src\Repositories\PostRepository;
use src\Repositories\AssociationRepository;
use src\Repositories\EntrepriseRepository;
use src\Services\Helper;

class PostController extends AbstractController
{
    private $repo;
    private $associationRepo;
    private $entrepriseRepo;

    public function __construct()
    {
        $this->repo = new PostRepository();
        $this->associationRepo = new AssociationRepository();
        $this->entrepriseRepo = new EntrepriseRepository();
    }

    private function getIdPostByUiid(): ?int
    {
        $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : (isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null);
        return $this->repo->getIdPostByUiid($uiid);
    }

    /**
     * Display all public posts
     */
    public function listPublicPosts(): void
    {
        try {
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $filter = isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : null;
            $postsPerPage = 12;

            $posts = $this->repo->getAllPublicPosts($currentPage, $postsPerPage, $filter);
            $totalPosts = $this->repo->countPublicPosts($filter);
            $totalPages = (int)ceil($totalPosts / $postsPerPage);

            $this->render('posts/actu_list', [
                'posts' => $posts,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'filter' => $filter,
                'title' => 'Actualités',
                'description' => 'Découvrez les dernières actualités de la communauté'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('404');
        }
    }

    /**
     * Display single post
     */
    public function displayPost(string $uiid): void
    {
        try {
            $post = $this->repo->getPostByUiid($uiid);

            if (!$post || !$post['isPublished']) {
                throw new Exception("L'article demandé n'existe pas");
            }

            $this->render('posts/actu_detail', [
                'post' => $post,
                'title' => $post['title']
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('actu');
        }
    }

    /**
     * Show form to create post
     */
    public function showAddPostForm(): void
    {
        try {
            $idUser = $_SESSION['idUser'];

            $associations = $this->associationRepo->getUserAssociations($idUser, 1, 100);
            $entreprises = $this->entrepriseRepo->getUserEntreprises($idUser, 1, 100);

            $this->render('posts/ajouter_post', [
                'associations' => $associations,
                'entreprises' => $entreprises,
                'title' => 'Créer une actualité'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('dashboard');
        }
    }

    /**
     * Create new post
     */
    public function createPost(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
            $content = isset($_POST['content']) ? htmlspecialchars(trim($_POST['content'])) : null;
            $authorType = isset($_POST['authorType']) ? htmlspecialchars($_POST['authorType']) : 'user';
            $association_uiid = isset($_POST['association_uiid']) && !empty($_POST['association_uiid']) ? htmlspecialchars(trim($_POST['association_uiid'])) : null;
            $entreprise_uiid = isset($_POST['entreprise_uiid']) && !empty($_POST['entreprise_uiid']) ? htmlspecialchars(trim($_POST['entreprise_uiid'])) : null;
            $isPublished = isset($_POST['isPublished']) ? 1 : 0;

            $_SESSION['form_data'] = $_POST;
            $errors = [];

            $idAssociation = null;
            $idEntreprise = null;

            if ($authorType === 'association' && $association_uiid) {
                $idAssociation = $this->associationRepo->getIdAssociationByUiid($association_uiid);
                if (!$idAssociation) {
                    $errors['association'] = "Veuillez sélectionner une association valide";
                }
            }

            if ($authorType === 'entreprise' && $entreprise_uiid) {
                $idEntreprise = $this->entrepriseRepo->getIdEntrepriseByUiid($entreprise_uiid);
                if (!$idEntreprise) {
                    $errors['entreprise'] = "Veuillez sélectionner une entreprise valide";
                }
            }

            if ($this->repo->isTitleExistsForUser($title, $idUser)) {
                $errors['title'] = "Vous avez déjà une actualité avec ce titre";
            }

            $helper = new Helper();
            $imagePath = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $helper->handleImageUpload('image', 'posts');
            }

            $post = new Post();
            $uiid = $helper->generateUiid();

            $post->setUiid($uiid)
                ->setTitle($title)
                ->setContent($content)
                ->setImagePath($imagePath)
                ->setIdUser($idUser)
                ->setIdAssociation($idAssociation)
                ->setIdEntreprise($idEntreprise)
                ->setAuthorType($authorType)
                ->setIsPublished($isPublished)
                ->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $modelErrors = $post->validate();
            $errors = array_merge($errors, $modelErrors);

            $this->returnAllErrors($errors, 'post/ajouter?error=true');

            $this->repo->createPost($post);

            $_SESSION['success'] = "L'actualité a été créée avec succès";
            $this->redirect('mes_posts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('post/ajouter?error=true');
        }
    }

    /**
     * Display user's posts
     */
    public function myPosts(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $postsPerPage = 12;

            $posts = $this->repo->getUserPosts($idUser, $currentPage, $postsPerPage);
            $totalPosts = $this->repo->countUserPosts($idUser);
            $totalPages = (int)ceil($totalPosts / $postsPerPage);

            $this->render('posts/mes_posts', [
                'posts' => $posts,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'totalPosts' => $totalPosts,
                'title' => 'Mes actualités'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('dashboard');
        }
    }

    /**
     * Show edit post form
     */
    public function showEditPostForm(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $idPost = $this->getIdPostByUiid();
            $uiid = isset($_GET['uiid']) ? htmlspecialchars(trim($_GET['uiid'])) : null;

            $post = $this->repo->getPostByUiid($uiid);

            if (!$post || $post['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette actualité");
            }

            $associations = $this->associationRepo->getUserAssociations($idUser, 1, 100);
            $entreprises = $this->entrepriseRepo->getUserEntreprises($idUser, 1, 100);

            $this->render('posts/modifier_post', [
                'post' => $post,
                'associations' => $associations,
                'entreprises' => $entreprises,
                'title' => 'Modifier l\'actualité'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_posts');
        }
    }

    /**
     * Update post
     */
    public function updatePost(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idPost = $this->getIdPostByUiid();

            $postData = $this->repo->getPostByUiid($uiid);

            if (!$postData || $postData['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de modifier cette actualité");
            }

            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null;
            $content = isset($_POST['content']) ? htmlspecialchars(trim($_POST['content'])) : null;
            $authorType = isset($_POST['authorType']) ? htmlspecialchars($_POST['authorType']) : 'user';
            $association_uiid = isset($_POST['association_uiid']) && !empty($_POST['association_uiid']) ? htmlspecialchars(trim($_POST['association_uiid'])) : null;
            $entreprise_uiid = isset($_POST['entreprise_uiid']) && !empty($_POST['entreprise_uiid']) ? htmlspecialchars(trim($_POST['entreprise_uiid'])) : null;
            $isPublished = isset($_POST['isPublished']) ? 1 : 0;

            $_SESSION['form_data'] = $_POST;
            $errors = [];

            $idAssociation = null;
            $idEntreprise = null;

            if ($authorType === 'association' && $association_uiid) {
                $idAssociation = $this->associationRepo->getIdAssociationByUiid($association_uiid);
            }

            if ($authorType === 'entreprise' && $entreprise_uiid) {
                $idEntreprise = $this->entrepriseRepo->getIdEntrepriseByUiid($entreprise_uiid);
            }

            if ($this->repo->isTitleExistsForUser($title, $idUser, $idPost)) {
                $errors['title'] = "Vous avez déjà une actualité avec ce titre";
            }

            $post = new Post();
            $post->setIdPost($idPost)
                ->setTitle($title)
                ->setContent($content)
                ->setImagePath($postData['imagePath'])
                ->setIdUser($idUser)
                ->setIdAssociation($idAssociation)
                ->setIdEntreprise($idEntreprise)
                ->setAuthorType($authorType)
                ->setIsPublished($isPublished)
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $modelErrors = $post->validate();
            $errors = array_merge($errors, $modelErrors);

            $this->returnAllErrors($errors, 'post/modifier?uiid=' . $uiid . '&error=true');

            $this->repo->updatePost($post);

            $_SESSION['success'] = "L'actualité a été mise à jour avec succès";
            $this->redirect('mes_posts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('post/modifier?uiid=' . ($uiid ?? '') . '&error=true');
        }
    }

    /**
     * Update post image
     */
    public function updatePostImage(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idPost = $this->getIdPostByUiid();

            $postData = $this->repo->getPostByUiid($uiid);

            if (!$postData || $postData['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation");
            }

            $helper = new Helper();
            $imagePath = $helper->handleImageUpload('image', 'posts');

            if ($postData['imagePath']) {
                $helper->handleDeleteImage($postData['imagePath']);
            }

            $post = new Post();
            $post->setIdPost($idPost)
                ->setTitle($postData['title'])
                ->setContent($postData['content'])
                ->setImagePath($imagePath)
                ->setIdUser($idUser)
                ->setIdAssociation($postData['idAssociation'])
                ->setIdEntreprise($postData['idEntreprise'])
                ->setAuthorType($postData['authorType'])
                ->setIsPublished($postData['isPublished'])
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updatePost($post);

            $_SESSION['success'] = "L'image a été mise à jour avec succès";
            $this->redirect('post/modifier?uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('post/modifier?uiid=' . ($uiid ?? '') . '&error=true');
        }
    }

    /**
     * Delete post image
     */
    public function deletePostImage(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idPost = $this->getIdPostByUiid();

            $postData = $this->repo->getPostByUiid($uiid);

            if (!$postData || $postData['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation");
            }

            if ($postData['imagePath']) {
                $helper = new Helper();
                $helper->handleDeleteImage($postData['imagePath']);
            }

            $post = new Post();
            $post->setIdPost($idPost)
                ->setTitle($postData['title'])
                ->setContent($postData['content'])
                ->setImagePath(null)
                ->setIdUser($idUser)
                ->setIdAssociation($postData['idAssociation'])
                ->setIdEntreprise($postData['idEntreprise'])
                ->setAuthorType($postData['authorType'])
                ->setIsPublished($postData['isPublished'])
                ->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

            $this->repo->updatePost($post);

            $_SESSION['success'] = "L'image a été supprimée avec succès";
            $this->redirect('post/modifier?uiid=' . $uiid);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('post/modifier?uiid=' . ($uiid ?? '') . '&error=true');
        }
    }

    /**
     * Delete post
     */
    public function deletePost(): void
    {
        try {
            $idUser = $_SESSION['idUser'];
            $uiid = isset($_POST['uiid']) ? htmlspecialchars(trim($_POST['uiid'])) : null;
            $idPost = $this->getIdPostByUiid();

            $postData = $this->repo->getPostByUiid($uiid);

            if (!$postData || $postData['idUser'] != $idUser) {
                throw new Exception("Vous n'avez pas l'autorisation de supprimer cette actualité");
            }

            if ($postData['imagePath']) {
                $helper = new Helper();
                $helper->handleDeleteImage($postData['imagePath']);
            }

            $this->repo->deletePost($idPost);

            $_SESSION['success'] = "L'actualité a été supprimée avec succès";
            $this->redirect('mes_posts');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('mes_posts');
        }
    }
}
