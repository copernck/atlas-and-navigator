<?php $pageTitle = 'Contact Us'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo $pageTitle; ?></h1>
    
    <p class="text-gray-700 mb-4 leading-relaxed">
        We welcome your feedback, questions, and inquiries. Please feel free to reach out to us using the contact information below.
    </p>
    
    <div class="mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Email</h2>
        <p class="text-gray-700">
            For general inquiries, support, or feedback, please email us at:
            <a href="mailto:example@gmail.com" class="text-indigo-600 hover:text-indigo-800"><?php echo htmlspecialchars('dexterluxtar19@gmail.com'); ?></a>
        </p>
        <p class="text-sm text-gray-500 mt-1">We typically respond within 1-2 business days.</p>
    </div>

    <?php /* // Placeholder for a future contact form
    <div class="mt-8 border-t pt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Contact Form (Coming Soon)</h2>
        <p class="text-gray-600">A contact form will be available here in the future.</p> 
        // Building a working form requires PHP mail handling code, which we haven't done yet.
    </div> 
    */ ?>
    
</div>
