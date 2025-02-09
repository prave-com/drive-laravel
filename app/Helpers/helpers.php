<?php

if (! function_exists('getFileType')) {
    /**
     * Get the file type based on the file extension.
     *
     * @param  mixed  $file
     * @return string
     */
    function getFileType($file)
    {
        $extension = strtolower(is_object($file) ? $file->extension : ($file['extension'] ?? ''));
        $types = [
            'doc' => 'Docs',
            'docx' => 'Docs',
            'pdf' => 'PDF',
            'jpg' => 'Image',
            'jpeg' => 'Image',
            'png' => 'Image',
            'gif' => 'Image',
            'mp4' => 'Video',
            'mp3' => 'Audio',
            'zip' => 'Archive',
            'rar' => 'Archive',
            'xlsx' => 'Sheet',
            'xls' => 'Sheet',
            'ppt' => 'Presentation',
            'pptx' => 'Presentation',
        ];

        return $types[$extension] ?? 'Other';
    }
}

if (! function_exists('getFileIconClass')) {
    /**
     * Get the icon class based on the file extension.
     *
     * @param  mixed  $file
     * @return string
     */
    function getFileIconClass($file)
    {
        $extension = strtolower(is_object($file) ? $file->extension : ($file['extension'] ?? ''));
        $icons = [
            'doc' => 'fas fa-file-alt file-icon text-primary',
            'docx' => 'fas fa-file-alt file-icon text-primary',
            'pdf' => 'fas fa-file-pdf file-icon text-danger',
            'jpg' => 'fa-solid fa-image file-icon text-danger',
            'jpeg' => 'fa-solid fa-image file-icon text-danger',
            'png' => 'fa-solid fa-image file-icon text-danger',
            'gif' => 'fa-solid fa-image file-icon text-danger',
            'mp4' => 'fa-solid fa-film file-icon text-danger',
            'mp3' => 'fa-solid fa-file-audio file-icon text-danger',
            'zip' => 'fa-solid fa-file-zipper file-icon',
            'rar' => 'fa-solid fa-file-zipper file-icon',
            'xlsx' => 'fa-solid fa-file-excel file-icon text-success',
            'xls' => 'fa-solid fa-file-excel file-icon text-success',
            'ppt' => 'fas fa-file-powerpoint file-icon text-warning',
            'pptx' => 'fas fa-file-powerpoint file-icon text-warning',
        ];

        return $icons[$extension] ?? 'fa fa-file';
    }
}

if (! function_exists('formatFileSize')) {
    /**
     * Format the file size into human-readable format.
     *
     * @param  mixed  $size
     * @return string
     */
    function formatFileSize($size)
    {
        // If the size is part of an object, extract it
        if (is_object($size)) {
            $size = $size->size ?? 0;
        }

        // Format the size
        if ($size < 1024) {
            return $size.' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 2).' KB';
        } elseif ($size < 1073741824) {
            return round($size / 1048576, 2).' MB';
        } else {
            return round($size / 1073741824, 2).' GB';
        }
    }
}
