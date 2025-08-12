<?php
// pages/privacy.php
$pageTitle = 'Privacy Policy';
?>

<div class="bg-white rounded-lg shadow-lg overflow-hidden p-6 sm:p-8 lg:p-10">
    <header class="text-center border-b border-gray-200 pb-6 mb-8">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 mb-2"><?php echo $pageTitle; ?></h1>
        <p class="text-lg text-gray-600">Last Updated: <?php echo date('F j, Y'); ?></p> <?php // Update date manually or dynamically ?>
    </header>

    <?php // Apply Tailwind Typography styles ?>
    <div class="prose prose-lg lg:prose-xl max-w-none prose-indigo
                prose-a:text-blue-600 hover:prose-a:text-blue-800
                prose-headings:text-gray-800">

        <?php // --- PASTE YOUR ACTUAL PRIVACY POLICY CONTENT HERE --- ?>

        <h2>Introduction</h2>
        <p>Welcome to <?php echo SITE_NAME; ?>. We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website atlasandnavigator.site, including any other media form, media channel, mobile website, or mobile application related or connected thereto (collectively, the “Site”). Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>

        <h2>Collection of Your Information</h2>
        <p>We may collect information about you in a variety of ways. The information we may collect on the Site includes:</p>
        <ul>
            <li><strong>Personal Data:</strong> Personally identifiable information, such as your name, email address, and demographic information, that you voluntarily give to us when you register with the Site or when you choose to participate in various activities related to the Site, such as online chat and message boards.</li>
            <li><strong>Derivative Data:</strong> Information our servers automatically collect when you access the Site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the Site.</li>
            <li><strong>Financial Data:</strong> Financial information, such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services from the Site. [We store only very limited, if any, financial information that we collect. Otherwise, all financial information is stored by our payment processor, [Payment Processor Name], and you are encouraged to review their privacy policy and contact them directly for responses to your questions.]</li>
        </ul>

        <h2>Use of Your Information</h2>
        <p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the Site to:</p>
        <ul>
            <li>Create and manage your account.</li>
            <li>Email you regarding your account or order.</li>
            <li>Enable user-to-user communications.</li>
            <li>Generate a personal profile about you to make future visits to the Site more personalized.</li>
            
        </ul>

        <h2>Disclosure of Your Information</h2>
        <p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p>
        
        <h3>By Law or to Protect Rights</h3>
        <p>If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation...</p>

        
        <h2>Security of Your Information</h2>
        <p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.</p>

        <h2>Contact Us</h2>
        <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
        <p>
            <?php echo SITE_NAME; ?><br>
            atlasandnavigator.site<br>
            <a href="dexterluxtar19@gmail.com">dexterluxtar19@gmail.com</a> <?php // Replace with your email ?>
        </p>

        <?php // --- END OF PRIVACY POLICY CONTENT --- ?>

    </div> <?php // End prose container ?>
</div>
