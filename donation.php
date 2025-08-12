<?php
// now with qr codes and full visible addresses (｀-´)>
?>

<div class="donation-container">
<div class="donation-header">
<h1>Support the Project</h1>
<p>Scan QR code or copy the full address. All donations are non-refundable.</p>
</div>

<div class="crypto-grid">
<div class="crypto-card">
<div class="crypto-info">
<div class="crypto-icon">₿</div>
<h3>Bitcoin (BTC)</h3>
</div>
<div class="qr-container">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=bc1qcmej8yfuu48pxc38qz5z4u46mmfrulww8j49ut" alt="BTC QR">
</div>
<div class="address-box">
<input type="text" readonly value="bc1qcmej8yfuu48pxc38qz5z4u46mmfrulww8j49ut">
<button class="copy-btn" data-coin="btc">Copy</button>
</div>
</div>

<!-- repeat this pattern for each coin, just changing the values -->
<div class="crypto-card">
<div class="crypto-info">
<div class="crypto-icon">Ξ</div>
<h3>ETH / BNB</h3>
</div>
<div class="qr-container">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=0xC0026a1e4D0343A263E86cB4AF6aA0Fa33806Cc3" alt="ETH QR">
</div>
<div class="address-box">
<input type="text" readonly value="0xC0026a1e4D0343A263E86cB4AF6aA0Fa33806Cc3">
<button class="copy-btn" data-coin="eth">Copy</button>
</div>
</div>


<!-- TRX -->
<div class="crypto-card">
<div class="crypto-info">
<div class="crypto-icon">TRX</div>
<h3>Tron (TRX)</h3>
</div>
<div class="qr-container">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TDkVubHsui98kzAMaVymFyGPerjdyLa1nf" alt="TRX QR">
</div>
<div class="address-box">
<input type="text" readonly value="TDkVubHsui98kzAMaVymFyGPerjdyLa1nf">
<button class="copy-btn">Copy</button>
</div>
</div>

<!-- SUI -->
<div class="crypto-card">
<div class="crypto-info">
<div class="crypto-icon">SUI</div>
<h3>Sui</h3>
</div>
<div class="qr-container">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=0xeb8be95f60e9db161ebcc12b33c12200bd605b277352d51725db9322b45e32e9" alt="SUI QR">
</div>
<div class="address-box">
<input type="text" readonly value="0xeb8be95f60e9db161ebcc12b33c12200bd605b277352d51725db9322b45e32e9">
<button class="copy-btn">Copy</button>
</div>
</div>

<!-- LTC -->
<div class="crypto-card">
<div class="crypto-info">
<div class="crypto-icon">Ł</div>
<h3>Litecoin (LTC)</h3>
</div>
<div class="qr-container">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ltc1qjefzk5na3e40zrhx0f3vuzjzcwt9mdcuvlhq0l" alt="LTC QR">
</div>
<div class="address-box">
<input type="text" readonly value="ltc1qjefzk5na3e40zrhx0f3vuzjzcwt9mdcuvlhq0l">
<button class="copy-btn">Copy</button>
</div>
</div>

<!-- XRP -->
<div class="crypto-card">
<div class="crypto-info">
<div class="crypto-icon">✕</div>
<h3>XRP Ledger</h3>
</div>
<div class="qr-container">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=rwQxdkFzrVtkqx97ev6y8R2DR6Ew4T19Sd" alt="XRP QR">
</div>
<div class="address-box">
<input type="text" readonly value="rwQxdkFzrVtkqx97ev6y8R2DR6Ew4T19Sd">
<button class="copy-btn">Copy</button>
</div>
</div>

<!-- add all other coins similarly -->
</div>
</div>

<style>
.donation-container {
max-width: 1400px;
margin: 20px auto;
padding: 20px;
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.crypto-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
gap: 25px;
}

.crypto-card {
background: #f8f9fa;
border-radius: 15px;
padding: 25px;
box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.crypto-info {
display: flex;
align-items: center;
gap: 15px;
margin-bottom: 15px;
}

.crypto-icon {
font-size: 28px;
color: #2d3748;
}

.qr-container {
display: flex;
justify-content: center;
margin: 15px 0;
padding: 10px;
background: white;
border-radius: 10px;
}

.qr-container img {
max-width: 150px;
height: auto;
}

.address-box {
position: relative;
margin-top: 15px;
}

.address-box input {
width: 100%;
padding: 12px;
padding-right: 70px;
border: 1px solid #e2e8f0;
border-radius: 8px;
font-size: 14px;
font-family: monospace;
background: #fff;
color: #2d3748;
overflow: visible;
text-overflow: ellipsis;
}

.copy-btn {
position: absolute;
right: 5px;
top: 50%;
transform: translateY(-50%);
padding: 6px 12px;
background: #4a5568;
color: white;
border: none;
border-radius: 6px;
cursor: pointer;
font-size: 14px;
transition: background 0.2s ease;
}

.copy-btn:hover {
background: #2d3748;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
.crypto-grid {
grid-template-columns: 1fr;
}

.crypto-card {
padding: 15px;
}

.address-box input {
font-size: 12px;
}
}
</style>

<script>
document.querySelectorAll('.copy-btn').forEach(button => {
button.addEventListener('click', function() {
const input = this.parentElement.querySelector('input');
input.select();
document.execCommand('copy');

// visual feedback
const originalText = this.textContent;
this.textContent = 'Copied!';
this.style.background = '#48bb78';

setTimeout(() => {
this.textContent = originalText;
this.style.background = '#4a5568';
}, 1500);
});
});
</script>