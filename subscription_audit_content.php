<?php
// pages/tools/subscription_audit_content.php - Subscription Audit Tool (HTML + JS)
// Note: This tool uses JavaScript for calculations and adding rows. Data is NOT saved.
?>

<div class="bg-white shadow rounded-lg p-6">
    
    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($pageTitle ?? 'Subscription Audit Tool'); ?></h1>
    <p class="text-sm text-gray-600 mb-6">list your recurring subscriptions to see your monthly and annual spending.</p>

    <div class="overflow-x-auto mb-6">
        <table class="min-w-full divide-y divide-gray-200" id="subscription-table">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">subscription name</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">cost</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">billing cycle</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">monthly cost</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="subscription-list">
                
                <tr class="subscription-row">
                    <td class="px-4 py-2 whitespace-nowrap"><input type="text" name="sub_name[]" class="sub-input sub-name px-2 py-1 border rounded w-full" placeholder="e.g., Netflix"></td>
                    <td class="px-4 py-2 whitespace-nowrap"><input type="number" step="0.01" min="0" name="sub_cost[]" class="sub-input sub-cost px-2 py-1 border rounded w-24" placeholder="e.g., 15.99"></td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <select name="sub_cycle[]" class="sub-input sub-cycle px-2 py-1 border rounded w-full bg-white">
                            <option value="monthly">monthly</option>
                            <option value="yearly">yearly</option>
                            <option value="quarterly">quarterly</option>
                            <option value="weekly">weekly</option>
                        </select>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 font-mono monthly-cost">$0.00</td>
                    <td class="px-4 py-2 whitespace-nowrap"><button type="button" class="text-red-500 hover:text-red-700 text-xs remove-row-btn">remove</button></td>
                </tr>
                
            </tbody>
        </table>
    </div>

    <button type="button" id="add-row-btn" class="mb-6 bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline">
        + add subscription
    </button>

    <div class="mt-4 p-4 border-t border-gray-200 bg-gray-50 rounded">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">totals:</h2>
        <div class="flex justify-around text-center">
            <div>
                <p class="text-sm text-gray-600 uppercase">monthly total</p>
                <p class="text-2xl font-bold text-blue-700" id="monthly-total">$0.00</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 uppercase">annual total</p>
                <p class="text-2xl font-bold text-blue-700" id="annual-total">$0.00</p>
            </div>
        </div>
    </div>

    <div class="mt-8 p-4 border border-yellow-400 bg-yellow-50 rounded">
         <p class="text-sm text-yellow-700"><strong class="font-bold">note:</strong> this tool uses javascript for calculations. data is not saved when you leave the page. calculations are estimates.</p>
    </div>

    <div class="mt-6">
         <a href="index.php?page=tools" class="text-sm text-blue-600 hover:text-blue-800">&larr; back to tools list</a>
    </div>

</div>

<script>
    // --- subscription audit tool javascript ---
    const tableBody = document.getElementById('subscription-list');
    const addRowBtn = document.getElementById('add-row-btn');
    const monthlyTotalEl = document.getElementById('monthly-total');
    const annualTotalEl = document.getElementById('annual-total');

    // Function to create a new row
    function createRow() {
        const newRow = document.createElement('tr');
        newRow.classList.add('subscription-row');
        newRow.innerHTML = `
            <td class="px-4 py-2 whitespace-nowrap"><input type="text" name="sub_name[]" class="sub-input sub-name px-2 py-1 border rounded w-full" placeholder="e.g., Spotify"></td>
            <td class="px-4 py-2 whitespace-nowrap"><input type="number" step="0.01" min="0" name="sub_cost[]" class="sub-input sub-cost px-2 py-1 border rounded w-24" placeholder="e.g., 10.99"></td>
            <td class="px-4 py-2 whitespace-nowrap">
                <select name="sub_cycle[]" class="sub-input sub-cycle px-2 py-1 border rounded w-full bg-white">
                    <option value="monthly">monthly</option>
                    <option value="yearly">yearly</option>
                    <option value="quarterly">quarterly</option>
                    <option value="weekly">weekly</option>
                </select>
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 font-mono monthly-cost">$0.00</td>
            <td class="px-4 py-2 whitespace-nowrap"><button type="button" class="text-red-500 hover:text-red-700 text-xs remove-row-btn">remove</button></td>
        `;
        tableBody.appendChild(newRow);
        attachRowListeners(newRow); // Attach listeners to the new row
    }

    // Function to calculate totals
    function calculateTotals() {
        let monthlySum = 0;
        const rows = tableBody.querySelectorAll('.subscription-row');

        rows.forEach(row => {
            const costInput = row.querySelector('.sub-cost');
            const cycleSelect = row.querySelector('.sub-cycle');
            const monthlyCostEl = row.querySelector('.monthly-cost');

            const cost = parseFloat(costInput.value) || 0;
            const cycle = cycleSelect.value;
            let monthlyCost = 0;

            if (cost > 0) {
                switch (cycle) {
                    case 'monthly':
                        monthlyCost = cost;
                        break;
                    case 'yearly':
                        monthlyCost = cost / 12;
                        break;
                    case 'quarterly':
                        monthlyCost = cost / 3;
                        break;
                    case 'weekly':
                        monthlyCost = cost * (52 / 12); // Approximate weeks per month
                        break;
                }
            }

            monthlyCostEl.textContent = `$${monthlyCost.toFixed(2)}`;
            monthlySum += monthlyCost;
        });

        monthlyTotalEl.textContent = `$${monthlySum.toFixed(2)}`;
        annualTotalEl.textContent = `$${(monthlySum * 12).toFixed(2)}`;
    }

    // Function to attach listeners to inputs and remove button in a row
    function attachRowListeners(row) {
        const inputs = row.querySelectorAll('.sub-input');
        inputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
            input.addEventListener('change', calculateTotals); // For select dropdown
        });

        const removeBtn = row.querySelector('.remove-row-btn');
        if(removeBtn) {
             removeBtn.addEventListener('click', () => {
                 // Prevent removing the last row if desired (optional)
                 // if (tableBody.querySelectorAll('.subscription-row').length > 1) {
                     row.remove();
                     calculateTotals(); // Recalculate after removing
                 // }
             });
        }
    }

    // Attach listeners to initial row(s)
    tableBody.querySelectorAll('.subscription-row').forEach(attachRowListeners);

    // Add row button listener
    addRowBtn.addEventListener('click', createRow);

    // Initial calculation
    calculateTotals();

</script>
