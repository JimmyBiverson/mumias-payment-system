<?php
require_once('./config.php');
header('Content-Type: application/json; charset=utf-8');
$logo = validate_image($_settings->info('logo'));
// Prefer dedicated app icons if available, else fall back to site logo
$icons = [];
if(is_file(base_app.'assets/img/app-icon-192.png')){
    $icons[] = ["src" => base_url.'assets/img/app-icon-192.png', "sizes" => "192x192", "type" => "image/png"];
} elseif(is_file(base_app.'assets/img/app-icon-192.svg')){
    $icons[] = ["src" => base_url.'assets/img/app-icon-192.svg', "sizes" => "192x192", "type" => "image/svg+xml"];
} else {
    $icons[] = ["src" => $logo, "sizes" => "192x192", "type" => "image/png"];
}
if(is_file(base_app.'assets/img/app-icon-512.png')){
    $icons[] = ["src" => base_url.'assets/img/app-icon-512.png', "sizes" => "512x512", "type" => "image/png"];
} elseif(is_file(base_app.'assets/img/app-icon-512.svg')){
    $icons[] = ["src" => base_url.'assets/img/app-icon-512.svg', "sizes" => "512x512", "type" => "image/svg+xml"];
} else {
    $icons[] = ["src" => $logo, "sizes" => "512x512", "type" => "image/png"];
}

$manifest = [
    "name" => $_settings->info('name'),
    "short_name" => $_settings->info('short_name') ? $_settings->info('short_name') : substr($_settings->info('name'),0,12),
    "start_url" => '/',
    "scope" => '/',
    "display" => 'standalone',
    "background_color" => '#ffffff',
    "theme_color" => '#007bff',
    "icons" => $icons
];
echo json_encode($manifest, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
exit;