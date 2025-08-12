        <?php // End of the main page content included by index.php ?>
    </main>

    <?php // Site Footer ?>
    <footer class="bg-gray-800 text-gray-300 py-8 mt-12 sm:mt-16 w-full">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <?php // Footer Links - Use Clean URLs ?>
            <div class="mb-4">
                 <a href="/privacy" class="text-gray-400 hover:text-white transition duration-200 text-sm mx-2">Privacy Policy</a>
                 <span class="text-gray-500 text-sm mx-1">|</span>
                 <a href="/contact" class="text-gray-400 hover:text-white transition duration-200 text-sm mx-2">Contact Us</a>
            </div>
            <?php // Copyright Notice ?>
            <p class="text-sm text-gray-400 mb-2">
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?>. All Rights Reserved.
            </p>
            <?php // Disclaimer Text ?>
            <p class="text-xs text-gray-500 max-w-prose mx-auto">
                Disclaimer: Financial and travel content provided on this site is for informational and educational purposes only. It does not constitute professional financial or travel advice. Always consult with qualified professionals before making decisions.
            </p>
        </div>
    </footer>

    <?php // JavaScript for Mobile Menu Toggle (using SVGs) ?>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Mobile Menu Toggle Logic (as before)
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');
            if (menuButton && mobileMenu && iconMenu && iconClose) {
                menuButton.addEventListener('click', () => {
                    const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
                    menuButton.setAttribute('aria-expanded', !isExpanded);
                    mobileMenu.classList.toggle('hidden');
                    if (!isExpanded) {
                        iconMenu.classList.add('hidden'); iconClose.classList.remove('hidden'); menuButton.setAttribute('aria-label', 'Close Menu');
                    } else {
                        iconMenu.classList.remove('hidden'); iconClose.classList.add('hidden'); menuButton.setAttribute('aria-label', 'Open Menu');
                    }
                });
            } else { console.error("Mobile menu elements not found."); }
        });
    </script>

    <?php // ** NEW: JavaScript for Table of Contents Smooth Scrolling ** ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find the table of contents container (adjust selector if needed)
            const toc = document.querySelector('.toc'); // Assuming your ToC is wrapped in <div class="toc">

            if (toc) {
                // Get all links within the table of contents
                const tocLinks = toc.querySelectorAll('a[href^="#"]'); // Select only links starting with #

                tocLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Prevent the default anchor link behavior (which might be broken by <base href>)
                        e.preventDefault();

                        // Get the target element's ID from the link's href
                        const targetId = this.getAttribute('href').substring(1); // Remove the '#'
                        const targetElement = document.getElementById(targetId);

                        if (targetElement) {
                            // Calculate position to scroll to (accounting for sticky header height if necessary)
                            const headerOffset = 80; // Adjust this value based on your sticky header's height
                            const elementPosition = targetElement.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                            // Smooth scroll to the target element
                            window.scrollTo({
                                 top: offsetPosition,
                                 behavior: "smooth"
                            });

                            // Optional: Update URL hash without jumping (for better history/bookmarking)
                            // history.pushState(null, null, `#${targetId}`);
                        } else {
                            console.warn('toc link target not found:', targetId);
                        }
                    });
                });
            }
        });
    </script>

    <?php // Prism JS (Currently Disabled - Uncomment to test again) ?>
    <?php /* --- Keep these commented out unless you resolve mobile freezing issue ---
    <script>
        // Configure autoloader path
        window.Prism = window.Prism || {}; window.Prism.plugins = window.Prism.plugins || {};
        window.Prism.plugins.autoloader = window.Prism.plugins.autoloader || {};
        window.Prism.plugins.autoloader.languages_path = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js" integrity="sha512-9khQRAUBYEJDCDVP2ywQ0ckmtDcCRYZWnRrcsyt4sbQCWYfZ6GwsF3uNmLXSDtehIG/itUsU9GBltdJjGsI//g==" crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" integrity="sha512-SkmBfuA2hqjzEVpmnMt/LINrj CodyMCET/0Dd5chDvPoQWZXsGnoUR_s2rsVgLTGssj9KNX3XW+BKRp75rtQgwA==" crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
    */ ?>

</body>
</html>
