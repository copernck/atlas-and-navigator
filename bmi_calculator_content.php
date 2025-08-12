<?php
// pages/tools/bmi_calculator_content.php - FIXED form action and back link

// --- form input variables ---
$height_ft = ''; $height_in = ''; $height_cm = ''; $weight_lbs = ''; $weight_kg = '';
$units = 'imperial'; // default
// --- calculation result variables ---
$bmiResult = null; $bmiCategory = null; $errorMessage = null;
// --- BMI Categories ---
$bmiCategories = [
    ['max' => 18.5, 'label' => 'Underweight', 'color' => 'text-blue-600'],
    ['max' => 24.9, 'label' => 'Normal weight', 'color' => 'text-green-600'],
    ['max' => 29.9, 'label' => 'Overweight', 'color' => 'text-yellow-600'],
    ['max' => 34.9, 'label' => 'Obesity Class I', 'color' => 'text-orange-600'],
    ['max' => 39.9, 'label' => 'Obesity Class II', 'color' => 'text-red-600'],
    ['max' => null, 'label' => 'Obesity Class III', 'color' => 'text-red-700']
];

// --- handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $units = isset($_POST['units']) && $_POST['units'] === 'metric' ? 'metric' : 'imperial';
    $height_m = 0; $weight_kg_val = 0;

    if ($units === 'metric') {
        $height_cm = isset($_POST['height_cm']) ? filter_var(trim($_POST['height_cm']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
        $weight_kg = isset($_POST['weight_kg']) ? filter_var(trim($_POST['weight_kg']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
        if (!is_numeric($height_cm) || $height_cm <= 0) { $errorMessage = 'valid height in cm required.'; }
        elseif (!is_numeric($weight_kg) || $weight_kg <= 0) { $errorMessage = 'valid weight in kg required.'; }
        else { $height_m = (float)$height_cm / 100.0; $weight_kg_val = (float)$weight_kg; }
    } else { // imperial
        $height_ft = isset($_POST['height_ft']) ? filter_var(trim($_POST['height_ft']), FILTER_SANITIZE_NUMBER_INT) : '';
        $height_in = isset($_POST['height_in']) ? filter_var(trim($_POST['height_in']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
        $weight_lbs = isset($_POST['weight_lbs']) ? filter_var(trim($_POST['weight_lbs']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
        if (!is_numeric($height_ft) || $height_ft < 0 || !is_numeric($height_in) || $height_in < 0 || ($height_ft <= 0 && $height_in <= 0)) { $errorMessage = 'valid height (ft/in) required.'; }
        elseif (!is_numeric($weight_lbs) || $weight_lbs <= 0) { $errorMessage = 'valid weight (lbs) required.'; }
        else { $total_inches = ((float)$height_ft * 12) + (float)$height_in; $height_m = $total_inches * 0.0254; $weight_kg_val = (float)$weight_lbs * 0.453592; }
    }

    // --- Perform calculation if inputs are valid ---
    if (!$errorMessage && $height_m > 0 && $weight_kg_val > 0) {
         try {
             $bmiResult = round($weight_kg_val / ($height_m * $height_m), 1);
             foreach ($bmiCategories as $category) { if ($category['max'] === null || $bmiResult < $category['max']) { $bmiCategory = $category; break; } }
             if ($bmiCategory === null) { throw new Exception("Could not determine BMI category."); }
         } catch (Exception $e) { error_log("bmi calc error: " . $e->getMessage()); $errorMessage = "calculation error."; $bmiResult = null; $bmiCategory = null; }
    } elseif (!$errorMessage) { $errorMessage = "invalid height or weight values."; }

} // end POST handling
?>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($pageTitle ?? 'BMI Calculator'); ?></h1>
    <p class="text-sm text-gray-600 mb-6">calculate your body mass index (bmi).</p>

    <?php // ** FIXED form action to use clean URL + query string ** ?>
    <form action="/tools?tool=bmi_calculator" method="post" class="space-y-4 mb-8">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">units:</label>
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center"><input type="radio" name="units" value="imperial" class="form-radio text-indigo-600" <?php if ($units === 'imperial') echo 'checked'; ?> onchange="this.form.submit()"><span class="ml-2">imperial (ft/in, lbs)</span></label>
                <label class="inline-flex items-center"><input type="radio" name="units" value="metric" class="form-radio text-indigo-600" <?php if ($units === 'metric') echo 'checked'; ?> onchange="this.form.submit()"><span class="ml-2">metric (cm, kg)</span></label>
            </div>
             <p class="text-xs text-gray-500 mt-1">changing units will reload the form.</p>
        </div>

        <?php if ($units === 'metric'): ?>
            <div><label for="height_cm" class="block text-sm font-medium text-gray-700">height (cm):</label><input type="number" step="0.1" min="1" id="height_cm" name="height_cm" value="<?php echo htmlspecialchars($height_cm); ?>" required class="mt-1 block w-full form-input"></div>
            <div><label for="weight_kg" class="block text-sm font-medium text-gray-700">weight (kg):</label><input type="number" step="0.1" min="1" id="weight_kg" name="weight_kg" value="<?php echo htmlspecialchars($weight_kg); ?>" required class="mt-1 block w-full form-input"></div>
        <?php else: // imperial ?>
            <div class="flex space-x-4">
                <div class="flex-1"><label for="height_ft" class="block text-sm font-medium text-gray-700">height (ft):</label><input type="number" step="1" min="0" id="height_ft" name="height_ft" value="<?php echo htmlspecialchars($height_ft); ?>" required class="mt-1 block w-full form-input"></div>
                <div class="flex-1"><label for="height_in" class="block text-sm font-medium text-gray-700">height (in):</label><input type="number" step="0.1" min="0" max="11.9" id="height_in" name="height_in" value="<?php echo htmlspecialchars($height_in); ?>" required class="mt-1 block w-full form-input"></div>
            </div>
            <div><label for="weight_lbs" class="block text-sm font-medium text-gray-700">weight (lbs):</label><input type="number" step="0.1" min="1" id="weight_lbs" name="weight_lbs" value="<?php echo htmlspecialchars($weight_lbs); ?>" required class="mt-1 block w-full form-input"></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="text-red-500 text-sm"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <div><button type="submit" class="btn btn-primary">calculate bmi</button></div>
    </form>

    <?php if ($bmiResult !== null && $bmiCategory !== null): ?>
        <div class="mt-8 p-4 border-t border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">results:</h2>
            <div class="text-center p-6 border rounded-lg bg-gray-50">
                 <p class="text-lg text-gray-700">your estimated bmi is:</p>
                 <p class="text-4xl font-bold my-2 <?php echo $bmiCategory['color']; ?>"><?php echo number_format($bmiResult, 1); ?></p>
                 <p class="text-lg font-medium <?php echo $bmiCategory['color']; ?>">(<?php echo htmlspecialchars($bmiCategory['label']); ?>)</p>
            </div>
            <p class="text-xs text-gray-500 mt-4">based on who standard ranges. consult a healthcare professional for actual health assessment.</p>
        </div>
    <?php endif; ?>

    <div class="mt-8 p-4 border border-yellow-400 bg-yellow-50 rounded">
         <p class="text-sm text-yellow-700"><strong class="font-bold">disclaimer:</strong> this bmi calculator is for informational purposes only for adults and is not medical advice.</p>
    </div>

    <div class="mt-6">
         <?php // ** FIXED back link to use clean URL ** ?>
         <a href="/tools" class="text-sm text-blue-600 hover:text-blue-800">&larr; back to tools list</a>
    </div>

</div>
<?php // Add basic form/button styles if needed ?>
<style> .form-input, .form-radio { border-color: #d1d5db; border-radius: 0.375rem; } .form-input { border-width: 1px; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; width: 100%; } .form-input:focus, .form-radio:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgb(199 210 254 / 40%); outline: 2px solid transparent; outline-offset: 2px; } .form-radio { color: #4f46e5; } .btn { display: inline-flex; items-center; justify-content: center; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; transition: background-color 0.15s ease-in-out; cursor: pointer; } .btn-primary { background-color: #4f46e5; color: white; border: 1px solid transparent; } .btn-primary:hover { background-color: #4338ca; } </style>
