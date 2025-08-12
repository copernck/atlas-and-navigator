<?php
// pages/tools.php - Clean URL Links for sub-tools

$requestedTool = isset($_GET['tool']) ? trim($_GET['tool']) : null;

// list of available tools (assuming this structure)
$availableTools = [
    'compound_interest' => ['file' => 'compound_interest_content.php', 'title' => 'Compound Interest Calculator', 'description' => 'Calculate how savings/investments grow with compounding.', 'icon' => '📈'],
    'savings_goal' => ['file' => 'savings_goal_content.php', 'title' => 'Savings Goal Calculator', 'description' => 'Estimate time to reach a savings target (simple).', 'icon' => '🎯'],
    'bmi_calculator' => ['file' => 'bmi_calculator_content.php', 'title' => 'BMI Calculator', 'description' => 'Calculate Body Mass Index using metric or imperial units.', 'icon' => '⚖️'],
    'subscription_audit' => ['file' => 'subscription_audit_content.php', 'title' => 'Subscription Audit Tool', 'description' => 'List recurring subscriptions and see total spending.', 'icon' => '🧾']
];

$toolFileToInclude = null;
// Use $pageTitle already set by index.php router, but allow override
// global $pageTitle; // No need to redeclare global here

// if a specific tool was requested...
if ($requestedTool && isset($availableTools[$requestedTool])) {
    $toolData = $availableTools[$requestedTool];
    $potentialFile = __DIR__ . '/tools/' . $toolData['file'];
    if (file_exists($potentialFile)) {
        $toolFileToInclude = $potentialFile;
        // Override page title for the specific tool
        $pageTitle = $toolData['title'];
        // We need to make $pageTitle available to header.php *before* it's included.
        // This is tricky as header.php is included by index.php *before* this file.
        // A better approach might be to set title *within* the included tool file,
        // or pass data differently. For now, this title override won't work perfectly
        // unless index.php's logic is changed to accommodate it AFTER routing.
        // Let's focus on fixing links first. The title will default to 'Tools'.

    } else {
        error_log("tool file not found: " . $potentialFile);
        // If file not found, show the tool list instead of 404
        $requestedTool = null; // Reset so list shows
        // Optionally set an error message here
    }
}

// --- display ---

if ($toolFileToInclude) {
    // if viewing a specific tool, include its content file
    // Make tool data available to the included file if needed
    $toolInfo = $toolData;
    include $toolFileToInclude;
} else {
    // otherwise, display the list of available tools
    // Ensure page title is 'Tools' for the list page
    // global $pageTitle; $pageTitle = 'Tools'; // This won't work reliably here. Title set in index.php.

?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php // Use the $pageTitle set by index.php ?>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 border-b border-gray-300 pb-4 mb-8"><?php echo htmlspecialchars($pageTitle ?? 'Tools'); ?></h1>

        <p class="text-lg text-gray-700 mb-10">
            a collection of helpful calculators and utilities for finance and travel planning.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            <?php foreach ($availableTools as $slug => $tool): ?>
                <?php // ** Use clean URL base path + query parameter for tool links ** ?>
                <a href="/tools?tool=<?php echo urlencode($slug); ?>"
                   class="block bg-white shadow-lg rounded-xl p-6 transition duration-300 ease-in-out hover:shadow-xl hover:scale-105">
                    <div class="flex items-start space-x-4">
                         <span class="text-3xl"><?php echo $tool['icon'] ?? '🔧'; ?></span>
                         <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($tool['title']); ?></h2>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($tool['description']); ?>
                            </p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>


            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex items-center justify-center text-gray-400 italic">
                <p>more tools coming soon...</p>
            </div>

        </div>
    </div>

<?php
} // end else (show tool list)
?>
