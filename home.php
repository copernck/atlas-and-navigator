<?php
// pages/home.php - FINAL VERSION v3 (Mobile layout adjustments & Clean URL Links)
$pageTitle = 'Welcome';
?>

<?php // Adjusted vertical spacing ?>
<div class="space-y-8 sm:space-y-12 lg:space-y-16">

    <?php // Hero Section ?>
    <section class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg shadow-xl overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 py-12 sm:py-16 lg:py-20 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-4 leading-tight">
                Explore Finance & Travel Smarter
            </h1>
            <p class="text-lg sm:text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                Your guide to navigating the worlds of personal finance and global travel with confidence.
            </p>
            <div class="flex flex-wrap justify-center items-center gap-4">
                <?php // Use clean URL for blog link ?>
                <a href="/blog" class="inline-block bg-white text-indigo-600 font-semibold py-3 px-6 rounded-lg shadow hover:bg-gray-100 transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Read the Blog
                </a>
                <?php // Use clean URL for about link ?>
                <a href="/about" class="inline-block bg-transparent border-2 border-white text-white font-semibold py-3 px-6 rounded-lg hover:bg-white hover:text-indigo-600 transition duration-300 ease-in-out">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <?php // "What We Offer" Section ?>
    <section>
        <div class="text-center mb-8 sm:mb-12 px-4">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">What We Offer</h2>
            <p class="text-lg text-gray-600 max-w-xl mx-auto">Insights and tools for your financial and travel journeys.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 px-4">
            <?php // Card 1: Finance ?>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 flex flex-col">
                 <div class="text-blue-500 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                 </div>
                <h3 class="text-xl font-semibold mb-2">Finance Insights</h3>
                <p class="text-gray-600 text-sm mb-4 flex-grow">Actionable tips on budgeting, saving, and investing.</p>
                 <?php // Use clean URL for finance link ?>
                 <a href="/finance" class="text-blue-600 hover:text-blue-800 font-medium mt-auto inline-block self-start">Explore Finance &rarr;</a>
            </div>
            <?php // Card 2: Travel ?>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 flex flex-col">
                 <div class="text-green-500 mb-3">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                 </div>
                <h3 class="text-xl font-semibold mb-2">Travel Guides</h3>
                <p class="text-gray-600 text-sm mb-4 flex-grow">Destination ideas, travel hacks, and planning advice.</p>
                 <?php // Use clean URL for travel link ?>
                 <a href="/travel" class="text-green-600 hover:text-green-800 font-medium mt-auto inline-block self-start">Explore Travel &rarr;</a>
            </div>
            <?php // Card 3: Tools ?>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 flex flex-col">
                  <div class="text-purple-500 mb-3">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                 </div>
                <h3 class="text-xl font-semibold mb-2">Useful Tools</h3>
                <p class="text-gray-600 text-sm mb-4 flex-grow">Calculators and resources to help you plan.</p>
                 <?php // Use clean URL for tools link ?>
                 <a href="/tools" class="text-purple-600 hover:text-purple-800 font-medium mt-auto inline-block self-start">Explore Tools &rarr;</a>
            </div>
        </div>
    </section>

    <?php // "Ready to Dive In?" Section ?>
    <section class="bg-gray-100 py-12 rounded-lg">
        <div class="container mx-auto px-4 sm:px-6 text-center">
             <h2 class="text-3xl font-bold text-gray-800 mb-4">Ready to Dive In?</h2>
             <p class="text-lg text-gray-600 mb-6 max-w-lg mx-auto">Check out our latest articles and start your journey today.</p>
             <?php // Use clean URL for blog link ?>
             <a href="/blog" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow hover:shadow-md transition duration-300">
                Visit the Blog
            </a>
        </div>
    </section>

</div>
