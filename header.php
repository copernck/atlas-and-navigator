<?php
// includes/header.php - FINAL VERSION v9 (AdSense + User GSC Meta Tag)

// Ensure config is loaded
require_once __DIR__ . '/../config/config.php';

// Get requested path for active nav highlighting
$requestUriNav = $_SERVER['REQUEST_URI'];
$requestPathNav = strtok($requestUriNav, '?');
$requestPathNav = trim($requestPathNav, '/');
$currentPageKey = 'home'; // Default
// ... (rest of the $currentPageKey logic as before) ...
if ($requestPathNav === 'about') $currentPageKey = 'about';
elseif ($requestPathNav === 'contact') $currentPageKey = 'contact';
elseif ($requestPathNav === 'privacy') $currentPageKey = 'privacy';
elseif ($requestPathNav === 'donation') $currentPageKey = 'donation';
elseif ($requestPathNav === 'tools') $currentPageKey = 'tools';
elseif ($requestPathNav === 'blog' || strpos($requestPathNav, 'blog/') === 0) $currentPageKey = 'blog';
elseif ($requestPathNav === 'finance') $currentPageKey = 'finance';
elseif ($requestPathNav === 'travel') $currentPageKey = 'travel';

// Define navigation items
$navItems = [ /* ... (nav items as before) ... */
    'home' => ['path' => '/', 'name' => 'Home'], 'finance' => ['path' => '/finance', 'name' => 'Finance'],
    'travel' => ['path' => '/travel', 'name' => 'Travel'], 'tools' => ['path' => '/tools', 'name' => 'Tools'],
    'blog' => ['path' => '/blog', 'name' => 'Blog'], 'about' => ['path' => '/about', 'name' => 'About'],
    'contact' => ['path' => '/contact', 'name' => 'Contact']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; echo htmlspecialchars(SITE_NAME); ?></title>
    <?php // Base href included ?>
    <base href="/">

    <?php // Tailwind CSS ?>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>

    <?php // Prism.js Theme CSS ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css" integrity="sha512-mIs9kKbaw6JZFfSuo+MovjU+Ntggfoj8RwAmJbVXQ5mkAX5LlgETQEweFPI18humSPHymTb5iikEOKWF7I8ncQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php // Custom CSS ?>
    <link rel="stylesheet" href="/css/style.css">

    <?php // Favicon links ?>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?php // ** GOOGLE ADSENSE AUTO ADS CODE SNIPPET ** ?>
    <?php // Make sure your actual AdSense snippet is pasted here ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234"
         crossorigin="anonymous"></script>
    <?php // ** END GOOGLE ADSENSE CODE SNIPPET ** ?>

    <?php // ** YOUR GOOGLE SEARCH CONSOLE VERIFICATION META TAG ** ?>
    <meta name="google-site-verification" content="cjbdbjcjdcbjdbdchjdhchjdbgblahblah" />
    <?php // ** END GOOGLE SEARCH CONSOLE META TAG ** ?>

</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">

    <header class="bg-white text-gray-800 shadow-md sticky top-0 z-50">
       <?php // ... rest of header nav code as before ... ?>
       <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-blue-600 hover:text-blue-800 transition duration-200 flex-shrink-0 mr-auto"><?php echo htmlspecialchars(SITE_NAME); ?></a>
            <div class="hidden md:flex items-center">
                <ul class="flex items-center space-x-4 lg:space-x-6 mr-4"><?php foreach ($navItems as $pageKey => $item){ $isActive = ($currentPageKey === $pageKey); $linkClass = $isActive ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-600 transition duration-200 pb-1 border-b-2 border-transparent hover:border-gray-300'; echo "<li><a href=\"{$item['path']}\" class=\"{$linkClass}\">" . htmlspecialchars($item['name']) . "</a></li>"; } ?></ul>
                <a href="/donation" class="ml-2 flex-shrink-0 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5">Donate</a>
            </div>
            <div class="md:hidden flex items-center"><button id="mobile-menu-button" aria-label="Open Menu" aria-expanded="false" aria-controls="mobile-menu" class="text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded p-1 ml-2"><span class="sr-only">Open main menu</span><svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block h-6 w-6"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg><svg id="icon-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden h-6 w-6"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button></div>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-200 z-40"><ul class="flex flex-col items-center py-4 space-y-3"><?php foreach ($navItems as $pageKey => $item){ $isActive = ($currentPageKey === $pageKey); $linkClass = $isActive ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600 transition duration-200'; echo "<li><a href=\"{$item['path']}\" class=\"{$linkClass} block py-2 text-lg\">" . htmlspecialchars($item['name']) . "</a></li>"; } ?><li><a href="/donation" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium py-2 px-5 rounded-lg transition duration-200 shadow hover:shadow-md mt-2 inline-block text-lg">Donate</a></li></ul></div>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 flex-grow">
        <?php // Page content starts here ?>
