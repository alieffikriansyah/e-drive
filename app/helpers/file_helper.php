<?php

/**
 * File Helper — E-Drive
 * Utility functions for file operations
 */

if (!function_exists('get_file_icon')) {
    /**
     * Return Font Awesome icon class based on file extension or mime type
     */
    function get_file_icon($ext_or_mime) {
        $ext = strtolower($ext_or_mime);
        
        // If mime type, extract from it
        if (strpos($ext, '/') !== false) {
            $parts = explode('/', $ext);
            $type = $parts[0];
            $sub = $parts[1] ?? '';
            
            if ($type === 'image') return 'fa-solid fa-file-image text-purple-500';
            if ($type === 'video') return 'fa-solid fa-file-video text-pink-500';
            if ($type === 'audio') return 'fa-solid fa-file-audio text-indigo-500';
            if (strpos($sub, 'pdf') !== false) return 'fa-solid fa-file-pdf text-red-500';
            if (strpos($sub, 'word') !== false || strpos($sub, 'document') !== false) return 'fa-solid fa-file-word text-blue-600';
            if (strpos($sub, 'excel') !== false || strpos($sub, 'sheet') !== false) return 'fa-solid fa-file-excel text-green-600';
            if (strpos($sub, 'powerpoint') !== false || strpos($sub, 'presentation') !== false) return 'fa-solid fa-file-powerpoint text-orange-500';
            if (strpos($sub, 'zip') !== false || strpos($sub, 'rar') !== false || strpos($sub, 'compressed') !== false) return 'fa-solid fa-file-zipper text-yellow-600';
            if ($type === 'text') return 'fa-solid fa-file-lines text-gray-500';
            return 'fa-solid fa-file text-gray-400';
        }
        
        // By extension
        $icons = [
            'pdf'  => 'fa-solid fa-file-pdf text-red-500',
            'doc'  => 'fa-solid fa-file-word text-blue-600',
            'docx' => 'fa-solid fa-file-word text-blue-600',
            'xls'  => 'fa-solid fa-file-excel text-green-600',
            'xlsx' => 'fa-solid fa-file-excel text-green-600',
            'csv'  => 'fa-solid fa-file-csv text-green-600',
            'ppt'  => 'fa-solid fa-file-powerpoint text-orange-500',
            'pptx' => 'fa-solid fa-file-powerpoint text-orange-500',
            'jpg'  => 'fa-solid fa-file-image text-purple-500',
            'jpeg' => 'fa-solid fa-file-image text-purple-500',
            'png'  => 'fa-solid fa-file-image text-purple-500',
            'gif'  => 'fa-solid fa-file-image text-purple-500',
            'svg'  => 'fa-solid fa-file-image text-purple-500',
            'webp' => 'fa-solid fa-file-image text-purple-500',
            'bmp'  => 'fa-solid fa-file-image text-purple-500',
            'mp4'  => 'fa-solid fa-file-video text-pink-500',
            'avi'  => 'fa-solid fa-file-video text-pink-500',
            'mov'  => 'fa-solid fa-file-video text-pink-500',
            'wmv'  => 'fa-solid fa-file-video text-pink-500',
            'mp3'  => 'fa-solid fa-file-audio text-indigo-500',
            'wav'  => 'fa-solid fa-file-audio text-indigo-500',
            'flac' => 'fa-solid fa-file-audio text-indigo-500',
            'zip'  => 'fa-solid fa-file-zipper text-yellow-600',
            'rar'  => 'fa-solid fa-file-zipper text-yellow-600',
            '7z'   => 'fa-solid fa-file-zipper text-yellow-600',
            'tar'  => 'fa-solid fa-file-zipper text-yellow-600',
            'gz'   => 'fa-solid fa-file-zipper text-yellow-600',
            'txt'  => 'fa-solid fa-file-lines text-gray-500',
            'rtf'  => 'fa-solid fa-file-lines text-gray-500',
            'md'   => 'fa-solid fa-file-lines text-gray-500',
            'html' => 'fa-solid fa-file-code text-orange-400',
            'css'  => 'fa-solid fa-file-code text-blue-400',
            'js'   => 'fa-solid fa-file-code text-yellow-400',
            'php'  => 'fa-solid fa-file-code text-indigo-400',
            'json' => 'fa-solid fa-file-code text-gray-500',
            'xml'  => 'fa-solid fa-file-code text-green-400',
            'sql'  => 'fa-solid fa-database text-blue-500',
            'psd'  => 'fa-solid fa-file-image text-blue-700',
            'ai'   => 'fa-solid fa-file-image text-orange-600',
            'dwg'  => 'fa-solid fa-compass-drafting text-teal-500',
            'dxf'  => 'fa-solid fa-compass-drafting text-teal-500',
        ];
        
        return $icons[$ext] ?? 'fa-solid fa-file text-gray-400';
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format bytes to human readable size
     */
    function format_file_size($bytes) {
        if ($bytes == 0) return '0 B';
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}

if (!function_exists('get_file_extension')) {
    function get_file_extension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

if (!function_exists('is_previewable')) {
    /**
     * Check if a file type can be previewed in browser
     */
    function is_previewable($mime_type) {
        $previewable = [
            'application/pdf',
            'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp', 'image/bmp',
            'text/plain', 'text/html', 'text/css', 'text/csv',
            'application/json', 'application/xml',
            'video/mp4', 'video/webm',
            'audio/mpeg', 'audio/wav', 'audio/ogg',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
        return in_array($mime_type, $previewable);
    }
}

if (!function_exists('generate_unique_filename')) {
    /**
     * Generate unique filename preserving extension
     */
    function generate_unique_filename($original_name) {
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $hash = substr(md5(uniqid(mt_rand(), true)), 0, 12);
        $timestamp = date('Ymd_His');
        return $timestamp . '_' . $hash . ($ext ? '.' . strtolower($ext) : '');
    }
}

if (!function_exists('get_breadcrumb_path')) {
    /**
     * Build breadcrumb array for a folder (recursive to root)
     */
    function get_breadcrumb_path($folder_id) {
        $CI =& Controller::get_instance();
        $breadcrumbs = [];
        $current_id = $folder_id;
        $max_depth = 20; // prevent infinite loop
        
        while ($current_id && $max_depth > 0) {
            $folder = $CI->db->table('folders')->where('id', $current_id)->row();
            if (!$folder) break;
            
            array_unshift($breadcrumbs, [
                'name' => $folder->name,
                'url'  => site_url('drive/view/' . $folder->drive_id . '?folder=' . $folder->id),
            ]);
            
            $current_id = $folder->parent_id;
            $max_depth--;
        }
        
        // Add drive as first breadcrumb
        if (!empty($breadcrumbs)) {
            $first_folder = $CI->db->table('folders')->where('id', $folder_id)->row();
            if ($first_folder) {
                $drive = $CI->db->table('drives')->where('id', $first_folder->drive_id)->row();
                if ($drive) {
                    array_unshift($breadcrumbs, [
                        'name' => $drive->name,
                        'url'  => site_url('drive/view/' . $drive->id),
                    ]);
                }
            }
        }
        
        return $breadcrumbs;
    }
}

if (!function_exists('get_mime_type')) {
    /**
     * Get MIME type from file path
     */
    function get_mime_type($file_path) {
        if (function_exists('mime_content_type')) {
            return mime_content_type($file_path);
        }
        
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $mimes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            'webp' => 'image/webp',
            'mp4'  => 'video/mp4',
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
            'json' => 'application/json',
            'xml'  => 'application/xml',
            'html' => 'text/html',
            'css'  => 'text/css',
            'js'   => 'application/javascript',
        ];
        return $mimes[$ext] ?? 'application/octet-stream';
    }
}
