<?php
// pages/tools/compound_interest_content.php - FIXED form action and back link

// --- form input variables ---
$principal = ''; $rate = ''; $years = '';
$compoundingFrequency = 1; // default annually
// --- calculation result variables ---
$futureValue = null; $totalInterest = null; $errorMessage = null;
// --- compounding options ---
$compoundingOptions = [ 1 => 'Annually', 2 => 'Semi-Annually', 4 => 'Quarterly', 12 => 'Monthly', 365 => 'Daily' ];

// --- handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $principal = isset($_POST['principal']) ? filter_var(trim($_POST['principal']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $rate = isset($_POST['rate']) ? filter_var(trim($_POST['rate']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $years = isset($_POST['years']) ? filter_var(trim($_POST['years']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $compoundingFrequency = isset($_POST['compounding']) ? (int)$_POST['compounding'] : 1;

    // validation
    if (!is_numeric($principal) || $principal < 0) { $errorMessage = 'valid principal required (>=0).'; }
    elseif (!is_numeric($rate) || $rate < 0) { $errorMessage = 'valid rate required (>=0).'; }
    elseif (!is_numeric($years) || $years <= 0) { $errorMessage = 'valid years required (>0).'; }
    elseif (!isset($compoundingOptions[$compoundingFrequency])) { $errorMessage = 'invalid compounding frequency.'; }
    else {
        // --- perform calculation ---
        try {
            $rateDecimal = (float)$rate / 100.0;
            $n = (float)$compoundingFrequency;
            $p = (float)$principal;
            $t = (float)$years;
            // Formula: A = P(1 + r/n)^(nt)
            $futureValue = $p * pow((1 + ($rateDecimal / $n)), ($n * $t));
            $totalInterest = $futureValue - $p;
        } catch (Exception $e) {
            error_log("compound interest calc error: " . $e->getMessage());
            $errorMessage = "calculation error.";
        }
    }
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <?php // Use $pageTitle set by tools.php (which gets it from $availableTools array) ?>
    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($pageTitle ?? 'Compound Interest Calculator'); ?></h1>
    <p class="text-sm text-gray-600 mb-6">See how your money can grow over time with the power of compounding.</p>

    <?php // ** FIXED form action to use clean URL + query string ** ?>
    <form action="/tools?tool=compound_interest" method="post" class="space-y-4 mb-8">
        <div>
            <label for="principal" class="block text-sm font-medium text-gray-700">Principal Amount ($):</label>
            <input type="number" step="0.01" min="0" id="principal" name="principal" value="<?php echo htmlspecialchars($principal); ?>" required class="mt-1 block w-full form-input">
        </div>
        <div>
            <label for="rate" class="block text-sm font-medium text-gray-700">Annual Interest Rate (%):</label>
            <input type="number" step="0.01" min="0" id="rate" name="rate" value="<?php echo htmlspecialchars($rate); ?>" required class="mt-1 block w-full form-input">
        </div>
        <div>
            <label for="years" class="block text-sm font-medium text-gray-700">Years to Grow:</label>
            <input type="number" step="0.1" min="0.1" id="years" name="years" value="<?php echo htmlspecialchars($years); ?>" required class="mt-1 block w-full form-input">
        </div>
        <div>
            <label for="compounding" class="block text-sm font-medium text-gray-700">Compounding Frequency:</label>
            <select id="compounding" name="compounding" class="mt-1 block w-full form-select">
                <?php foreach ($compoundingOptions as $value => $text): ?>
                    <option value="<?php echo $value; ?>" <?php if ($compoundingFrequency == $value) echo 'selected'; ?>>
                        <?php echo $text; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($errorMessage): ?>
            <p class="text-red-500 text-sm"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <div>
            <button type="submit" class="btn btn-primary"> <?php // Using basic btn classes ?>
                Calculate
            </button>
        </div>
    </form>

    <?php // Display results if calculation was successful ?>
    <?php if ($futureValue !== null && $totalInterest !== null): ?>
        <div class="mt-8 p-4 border-t border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Results:</h2>
            <div class="space-y-2 text-lg bg-gray-50 p-4 rounded">
                <p>Future Value: <strong class="text-green-700">$<?php echo number_format($futureValue, 2); ?></strong></p>
                <p>Total Interest Earned: <strong class="text-green-600">$<?php echo number_format($totalInterest, 2); ?></strong></p>
            </div>
             <p class="text-xs text-gray-500 mt-4">Note: This is an estimate. Actual returns may vary.</p>
        </div>
    <?php endif; ?>

    <div class="mt-8 p-4 border border-yellow-400 bg-yellow-50 rounded">
         <p class="text-sm text-yellow-700"><strong class="font-bold">Disclaimer:</strong> This calculator is for informational purposes only and does not constitute financial advice.</p>
    </div>

    <div class="mt-6">
         <?php // ** FIXED back link to use clean URL ** ?>
         <a href="/tools" class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Tools List</a>
    </div>

</div>
<?php // Add basic form/button styles if needed, assuming Tailwind handles most ?>
<style> .form-input, .form-select { border-width: 1px; border-color: #d1d5db; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; width: 100%; } .form-input:focus, .form-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgb(199 210 254 / 40%); outline: 2px solid transparent; outline-offset: 2px; } .btn { display: inline-flex; items-center; justify-content: center; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; transition: background-color 0.15s ease-in-out; cursor: pointer; } .btn-primary { background-color: #4f46e5; color: white; border: 1px solid transparent; } .btn-primary:hover { background-color: #4338ca; } </style>
