<?php

namespace src\Services;

use Exception;

class Helper
{
    // Image optimization settings
    private const MAX_WIDTH = 1920;
    private const MAX_HEIGHT = 1080;
    private const JPEG_QUALITY = 85;
    private const WEBP_QUALITY = 80;
    private const PNG_COMPRESSION = 6;
    
    // Memory management constants
    private const MAX_PIXELS = 50000000; // 50 megapixels limit
    private const MEMORY_LIMIT_INCREASE = '512M';

    /**
     * Generate a unique identifier (UUID v4)
     */
    public function generateUiid(): string
    {
        $uiid = substr(bin2hex(random_bytes(16)), 1, 16);
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
     * Handle image upload with automatic optimization
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
        $maxFileSize = 5 * 1024 * 1024; // 5MB initial upload limit

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

        // Check image dimensions before processing to prevent memory issues
        $imageInfo = getimagesize($fileTmpPath);
        if ($imageInfo === false) {
            throw new Exception('Impossible de lire les informations de l\'image.');
        }

        $totalPixels = $imageInfo[0] * $imageInfo[1];
        if ($totalPixels > self::MAX_PIXELS) {
            throw new Exception('L\'image est trop grande en résolution. Maximum autorisé : 50 mégapixels.');
        }

        // Calculate approximate memory needed (width * height * 4 bytes * 3 for safety)
        $memoryNeeded = $totalPixels * 4 * 3;
        $currentMemoryLimit = $this->return_bytes(ini_get('memory_limit'));
        
        if ($memoryNeeded > $currentMemoryLimit * 0.8) {
            // Temporarily increase memory limit
            $oldMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', self::MEMORY_LIMIT_INCREASE);
        }

        // Generate unique filename
        $fileName = uniqid() . '_' . time();
        $optimizedImagePath = $uploadDir . $fileName . '.webp';

        try {
            // Process and optimize the image
            $this->processAndOptimizeImage($fileTmpPath, $optimizedImagePath, $fileType);
        } finally {
            // Restore original memory limit if it was changed
            if (isset($oldMemoryLimit)) {
                ini_set('memory_limit', $oldMemoryLimit);
            }
        }

        return "assets/images/uploads/{$directory}/" . $fileName . '.webp';
    }

    /**
     * Convert human readable file size to bytes
     */
    private function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Process and optimize image with resizing and compression
     */
    private function processAndOptimizeImage($sourcePath, $targetPath, $mimeType)
    {
        if (!file_exists($sourcePath)) {
            throw new Exception('Le fichier source n\'existe pas.');
        }

        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new Exception('Impossible de lire les informations de l\'image.');
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];

        // Handle EXIF orientation
        $orientation = 1;
        if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
            }
        }

        // Adjust dimensions for orientation
        if (in_array($orientation, [6, 8])) {
            $width = $originalHeight;
            $height = $originalWidth;
        } else {
            $width = $originalWidth;
            $height = $originalHeight;
        }

        // Create source image resource with error handling
        try {
            $sourceImage = $this->createImageResource($sourcePath, $mimeType);
            if (!$sourceImage) {
                throw new Exception('Impossible de créer la ressource image.');
            }
        } catch (Exception $e) {
            throw new Exception('Erreur lors du chargement de l\'image: ' . $e->getMessage());
        }

        // Apply orientation correction
        if ($orientation !== 1) {
            $rotatedImage = $this->fixImageOrientation($sourceImage, $orientation);
            if ($rotatedImage !== $sourceImage) {
                imagedestroy($sourceImage);
                $sourceImage = $rotatedImage;
            }
        }

        // Calculate new dimensions if resizing is needed
        $newDimensions = $this->calculateOptimalDimensions($width, $height, self::MAX_WIDTH, self::MAX_HEIGHT);
        $needsResize = ($newDimensions['width'] !== $width || $newDimensions['height'] !== $height);

        if ($needsResize) {
            // Resize image
            $optimizedImage = $this->resizeImage($sourceImage, $width, $height, $newDimensions['width'], $newDimensions['height']);
        } else {
            // Use original dimensions
            $optimizedImage = imagecreatetruecolor($width, $height);
            
            if (!$optimizedImage) {
                imagedestroy($sourceImage);
                throw new Exception('Impossible de créer l\'image optimisée.');
            }
            
            // Preserve transparency for PNG
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($optimizedImage, false);
                imagesavealpha($optimizedImage, true);
                $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                imagefill($optimizedImage, 0, 0, $transparent);
            }
            
            imagecopy($optimizedImage, $sourceImage, 0, 0, 0, 0, $width, $height);
        }

        // Save as WebP with compression
        if (!imagewebp($optimizedImage, $targetPath, self::WEBP_QUALITY)) {
            // Clean up resources before throwing exception
            imagedestroy($sourceImage);
            imagedestroy($optimizedImage);
            throw new Exception('Erreur lors de la sauvegarde de l\'image optimisée.');
        }

        // Clean up resources
        imagedestroy($sourceImage);
        imagedestroy($optimizedImage);

        // Verify the saved file
        if (!file_exists($targetPath) || filesize($targetPath) === 0) {
            throw new Exception('L\'image optimisée n\'a pas pu être sauvegardée correctement.');
        }
    }

    /**
     * Create image resource from file based on MIME type
     */
    private function createImageResource($filePath, $mimeType)
    {
        // Set error reporting to catch GD errors
        $oldErrorReporting = error_reporting(E_ALL);
        
        try {
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = @imagecreatefromjpeg($filePath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($filePath);
                    break;
                case 'image/gif':
                    $image = @imagecreatefromgif($filePath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($filePath);
                    break;
                default:
                    return false;
            }
            
            if ($image === false) {
                throw new Exception('Impossible de charger l\'image. Le fichier pourrait être corrompu ou trop volumineux.');
            }
            
            return $image;
        } finally {
            error_reporting($oldErrorReporting);
        }
    }

    /**
     * Fix image orientation based on EXIF data
     */
    private function fixImageOrientation($image, $orientation)
    {
        switch ($orientation) {
            case 3:
                return imagerotate($image, 180, 0);
            case 6:
                return imagerotate($image, -90, 0);
            case 8:
                return imagerotate($image, 90, 0);
            default:
                return $image;
        }
    }

    /**
     * Calculate optimal dimensions while maintaining aspect ratio
     */
    private function calculateOptimalDimensions($currentWidth, $currentHeight, $maxWidth, $maxHeight)
    {
        // If image is already smaller than max dimensions, keep original size
        if ($currentWidth <= $maxWidth && $currentHeight <= $maxHeight) {
            return ['width' => $currentWidth, 'height' => $currentHeight];
        }

        // Calculate aspect ratio
        $aspectRatio = $currentWidth / $currentHeight;

        // Determine which dimension to constrain
        if ($currentWidth / $maxWidth > $currentHeight / $maxHeight) {
            // Width is the limiting factor
            $newWidth = $maxWidth;
            $newHeight = round($maxWidth / $aspectRatio);
        } else {
            // Height is the limiting factor
            $newHeight = $maxHeight;
            $newWidth = round($maxHeight * $aspectRatio);
        }

        return ['width' => (int)$newWidth, 'height' => (int)$newHeight];
    }

    /**
     * Resize image with high quality resampling
     */
    private function resizeImage($sourceImage, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight)
    {
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Enable alpha blending for transparent images
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        // Create transparent background
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefill($resizedImage, 0, 0, $transparent);

        // Resample with high quality
        if (!imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $sourceWidth, $sourceHeight
        )) {
            imagedestroy($resizedImage);
            throw new Exception('Erreur lors du redimensionnement de l\'image.');
        }

        return $resizedImage;
    }

    /**
     * Create thumbnail version of an image
     */
    public function createThumbnail($sourcePath, $targetPath, $thumbWidth = 300, $thumbHeight = 300)
    {
        try {
            if (!file_exists($sourcePath)) {
                throw new Exception('Le fichier source n\'existe pas.');
            }

            $imageInfo = getimagesize($sourcePath);
            if ($imageInfo === false) {
                throw new Exception('Impossible de lire les informations de l\'image.');
            }

            $mimeType = $imageInfo['mime'];
            $sourceImage = $this->createImageResource($sourcePath, $mimeType);

            if (!$sourceImage) {
                throw new Exception('Impossible de créer la ressource image.');
            }

            $sourceWidth = $imageInfo[0];
            $sourceHeight = $imageInfo[1];

            // Create square thumbnail with cropping
            $size = min($sourceWidth, $sourceHeight);
            $x = ($sourceWidth - $size) / 2;
            $y = ($sourceHeight - $size) / 2;

            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);

            if (!imagecopyresampled(
                $thumbnail,
                $sourceImage,
                0, 0, $x, $y,
                $thumbWidth, $thumbHeight,
                $size, $size
            )) {
                imagedestroy($thumbnail);
                imagedestroy($sourceImage);
                throw new Exception('Erreur lors de la création de la miniature.');
            }

            // Save as WebP
            imagewebp($thumbnail, $targetPath, self::WEBP_QUALITY);

            imagedestroy($thumbnail);
            imagedestroy($sourceImage);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get optimized image dimensions without actually processing the image
     */
    public function getOptimizedDimensions($imagePath)
    {
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            return null;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        return $this->calculateOptimalDimensions($width, $height, self::MAX_WIDTH, self::MAX_HEIGHT);
    }

    /**
     * Delete image file from server
     */
    public function handleDeleteImage($imagePath): bool
    {
        try {
            $fullPath = __DIR__ . "/../../public/" . $imagePath;

            if (file_exists($fullPath)) {
                unlink($fullPath);
                
                // Also try to delete potential thumbnail
                $pathInfo = pathinfo($fullPath);
                $thumbPath = $pathInfo['dirname'] . '/thumbs/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate image file before processing
     */
    public function validateImageFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('Le fichier n\'existe pas.');
        }

        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            throw new Exception('Le fichier n\'est pas une image valide.');
        }

        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
        if (!in_array($imageInfo[2], $allowedTypes)) {
            throw new Exception('Format d\'image non supporté.');
        }

        return true;
    }

    /**
     * Get image file size in human readable format
     */
    public function getImageFileSize($imagePath)
    {
        $fullPath = __DIR__ . "/../../public/" . $imagePath;
        
        if (!file_exists($fullPath)) {
            return null;
        }

        $bytes = filesize($fullPath);
        
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}
