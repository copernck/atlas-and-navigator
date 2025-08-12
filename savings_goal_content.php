<?php
// pages/tools/savings_goal_content.php - FIXED form action and back link

// --- form input variables ---
$targetAmount = ''; $currentSavings = ''; $monthlyContribution = '';
// --- calculation result variables ---
$monthsNeeded = null; $years = null; $months = null; $errorMessage = null;

// --- handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetAmount = isset($_POST['target_amount']) ? filter_var(trim($_POST['target_amount']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $currentSavings = isset($_POST['current_savings']) ? filter_var(trim($_POST['current_savings']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $monthlyContribution = isset($_POST['monthly_contribution']) ? filter_var(trim($_POST['monthly_contribution']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';

    // validation
    if (!is_numeric($targetAmount) || $targetAmount <= 0) { $errorMessage = 'valid target amount required (>0).'; }
    elseif (!is_numeric($currentSavings) || $currentSavings < 0) { $errorMessage = 'valid current savings required (>=0).'; }
    elseif (!is_numeric($monthlyContribution) || $monthlyContribution <= 0) { $errorMessage = 'valid monthly contribution required (>0).'; }
    elseif ((float)$currentSavings >= (float)$targetAmount) {
         $errorMessage = 'current savings already meet or exceed target!'; $monthsNeeded = 0; $years = 0; $months = 0;
    } else {
        // --- perform calculation (simple version) ---
        try {
            $amountNeeded = (float)$targetAmount - (float)$currentSavings;
            $contrib = (float)$monthlyContribution;
            if ($contrib <= 0) { throw new Exception("Monthly contribution must be positive."); }
            $monthsNeeded = ceil($amountNeeded / $contrib);
            if ($monthsNeeded >= 0) { $years = floor($monthsNeeded / 12); $months = $monthsNeeded % 12; }
            else { throw new Exception("Calculation resulted in negative months."); }
        } catch (Exception $e) {
            error_log("savings goal calc error: " . $e->getMessage());
            $errorMessage = "calculation error."; $monthsNeeded = null;
        }
    }
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($pageTitle ?? 'Savings Goal Calculator'); ?></h1>
    <p class="text-sm text-gray-600 mb-6">Estimate how long it might take to reach your savings goal (simple version without interest).</p>

    <?php // ** FIXED form action to use clean URL + query string ** ?>
    <form action="/tools?tool=savings_goal" method="post" class="space-y-4 mb-8">
        <div>
            <label for="target_amount" class="block text-sm font-medium text-gray-700">Target Amount ($):</label>
            <input type="number" step="0.01" min="0.01" id="target_amount" name="target_amount" value="<?php echo htmlspecialchars($targetAmount); ?>" required class="mt-1 block w-full form-input">
        </div>
        <div>
            <label for="current_savings" class="block text-sm font-medium text-gray-700">Current Savings ($):</label>
            <input type="number" step="0.01" min="0" id="current_savings" name="current_savings" value="<?php echo htmlspecialchars($currentSavings); ?>" required class="mt-1 block w-full form-input">
        </div>
        <div>
            <label for="monthly_contribution" class="block text-sm font-medium text-gray-700">Monthly Contribution ($):</label>
            <input type="number" step="0.01" min="0.01" id="monthly_contribution" name="monthly_contribution" value="<?php echo htmlspecialchars($monthlyContribution); ?>" required class="mt-1 block w-full form-input">
        </div>

        <?php if ($errorMessage && $monthsNeeded === null): ?>
            <p class="text-red-500 text-sm"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <div>
            <button type="submit" class="btn btn-primary">Calculate Time</button>
        </div>
    </form>

    <?php // Display result ?>
    <?php if ($monthsNeeded !== null): ?>
        <div class="mt-8 p-4 border-t border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Results:</h2>
             <?php if ($errorMessage && $monthsNeeded === 0): ?>
                 <p class="text-lg text-green-700 font-medium"><?php echo htmlspecialchars($errorMessage); ?></p>
             <?php else: ?>
                 <p class="text-lg">It will take approximately <strong class="text-green-700">
                     <?php
                         if ($years > 0) echo $years . " year" . ($years > 1 ? "s" : "");
                         if ($years > 0 && $months > 0) echo " and ";
                         if ($months > 0 || ($years == 0 && $months == 0)) echo $months . " month" . ($months > 1 || $months == 0 ? "s" : "");
                     ?>
                 </strong> to reach your goal.</p>
                 <p class="text-sm text-gray-500 mt-2">(Total <?php echo $monthsNeeded; ?> months, assuming consistent contributions and no interest.)</p>
             <?php endif; ?>
        </div>
    <?php endif; ?>

     <div class="mt-8 p-4 border border-yellow-400 bg-yellow-50 rounded">
         <p class="text-sm text-yellow-700"><strong class="font-bold">Disclaimer:</strong> This calculator provides a basic estimate and does not account for interest earned, inflation, taxes, or changes in contributions.</p>
    </div>

    <div class="mt-6">
         <?php // ** FIXED back link to use clean URL ** ?>
         <a href="/tools" class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Tools List</a>
    </div>

</div>
<?php // Add basic form/button styles if needed ?>
<style> .form-input { border-width: 1px; border-color: #d1d5db; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; width: 100%; } .form-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgb(199 210 254 / 40%); outline: 2px solid transparent; outline-offset: 2px; } .btn { display: inline-flex; items-center; justify-content: center; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; transition: background-color 0.15s ease-in-out; cursor: pointer; } .btn-primary { background-color: #4f46e5; color: white; border: 1px solid transparent; } .btn-primary:hover { background-color: #4338ca; } </style>
