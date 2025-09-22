<?php

namespace src\Services;

use Exception;

class Helper
{
    /**
     * Generate a unique identifier (UUID v4)
     */
    public function generateUiid(): string
    {
        $uiid = substr(bin2hex(random_bytes(16)), 1, 16);
        // $uiid = uniqid('', true);
        return $uiid;
    }

    /**
     * Generate a URL-friendly slug from a string, including ville and category if provided
     */
    public function generateSlug(string $ville, string $string, $category = null): string
    {
        // Use $ville and $category in the slug
        $villeSlug = '';
        $categorySlug = '';

        if (!empty($ville)) {
            $villeSlug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $ville);
            $villeSlug = strtolower($villeSlug);
            $villeSlug = preg_replace('/[^a-z0-9\s-]/', '', $villeSlug);
            $villeSlug = preg_replace('/[\s-]+/', '-', $villeSlug);
            $villeSlug = trim($villeSlug, '-');
        }

        if (!empty($category)) {
            $categorySlug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $category);
            $categorySlug = strtolower($categorySlug);
            $categorySlug = preg_replace('/[^a-z0-9\s-]/', '', $categorySlug);
            $categorySlug = preg_replace('/[\s-]+/', '-', $categorySlug);
            $categorySlug = trim($categorySlug, '-');
        }

        // Remove accents and special characters from $string
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Combine all parts
        $parts = array_filter([$villeSlug, $categorySlug, $slug]);
        return implode('-', $parts);
    }
    /**
     * Handle image upload
     */
    public function handleImageUpload($fileInputName, $directory)
    {
        if (!isset($_FILES[$fileInputName]) || empty($_FILES[$fileInputName]['name'])) {
            throw new Exception("Aucun fichier n'a été sélectionné.");
        }

        // Handle upload errors
        if ($_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = 'Erreur lors du téléchargement de l\'image.';
            switch ($_FILES[$fileInputName]['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = "Le fichier est trop volumineux. Limite serveur : " . ini_get('upload_max_filesize') . ".";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = "Le fichier n'a été que partiellement téléchargé.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = "Aucun fichier n'a été téléchargé.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMsg = "Dossier temporaire manquant.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMsg = "Échec de l'écriture du fichier sur le disque.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMsg = "Une extension PHP a arrêté le téléchargement du fichier.";
                    break;
            }
            throw new Exception($errorMsg);
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $uploadDir = __DIR__ . "/../../public/assets/images/uploads/{$directory}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileNameOriginal = $_FILES[$fileInputName]['name'];
        $fileSize = $_FILES[$fileInputName]['size'];
        $fileType = mime_content_type($fileTmpPath);
        $fileExtension = strtolower(pathinfo($fileNameOriginal, PATHINFO_EXTENSION));

        // Check if file is actually uploaded
        if (!is_uploaded_file($fileTmpPath)) {
            throw new Exception('Le fichier n\'a pas été téléchargé correctement.');
        }

        // Validate file type and size
        if (!in_array($fileType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Format de fichier non autorisé. Veuillez télécharger une image (jpg, jpeg, png, gif, webp).');
        }

        if ($fileSize > $maxFileSize) {
            throw new Exception('La taille de l\'image ne doit pas dépasser 5 Mo.');
        }

        $fileName = uniqid() . '_' . basename($fileNameOriginal);
        $uploadFile = "{$uploadDir}{$fileName}";

        // Handle EXIF orientation for JPEG images
        if ($fileExtension === 'jpg' || $fileExtension === 'jpeg') {
            $image = @imagecreatefromjpeg($fileTmpPath);
            if ($image && function_exists('exif_read_data')) {
                $exif = @exif_read_data($fileTmpPath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                }
            }
            if ($image) {
                imagejpeg($image, $uploadFile, 90);
                imagedestroy($image);
            } else {
                throw new Exception('Impossible de traiter l\'image JPEG.');
            }
        } else {
            // Move uploaded file for non-JPEG images
            if (!move_uploaded_file($fileTmpPath, $uploadFile)) {
                throw new Exception('Erreur lors du déplacement du fichier téléchargé.');
            }
        }

        return "assets/images/uploads/{$directory}/" . $fileName;
    }
    public function handleDeleteImage($imagePath): bool
    {
        try {

            $fullPath = __DIR__ . "/../../public/" . $imagePath;

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
