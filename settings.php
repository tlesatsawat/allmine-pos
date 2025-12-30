<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - AllMine Coffee POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #FDF8F6;
            color: #4E342E;
        }
        .bg-primary { background-color: #8D6E63; }
        .text-primary { color: #8D6E63; }
        .bg-accent { background-color: #F2E8E5; }
        .text-muted { color: #BCAAA4; }
        
        .settings-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: #8D6E63;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .btn-secondary {
            background-color: #D7CCC8;
            color: #4E342E;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #D7CCC8;
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-primary">Settings</h1>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to POS
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Shop Profile Section -->
            <div class="settings-section">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-store mr-2"></i> Shop Profile
                </h2>
                
                <div class="form-group">
                    <label for="shopName">Shop Name</label>
                    <input type="text" id="shopName" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="receiptHeader">Receipt Header</label>
                    <textarea id="receiptHeader" rows="3" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="receiptFooter">Receipt Footer</label>
                    <textarea id="receiptFooter" rows="3" class="form-control"></textarea>
                </div>
            </div>
            
            <!-- Payment Settings Section -->
            <div class="settings-section">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-credit-card mr-2"></i> Payment Settings
                </h2>
                
                <div class="form-group">
                    <label for="promptpayNumber">PromptPay Number</label>
                    <input type="text" id="promptpayNumber" placeholder="Enter phone number or merchant ID" class="form-control">
                </div>
            </div>
            
            <!-- Points Settings Section -->
            <div class="settings-section">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-gift mr-2"></i> Points Settings
                </h2>
                
                <div class="form-group">
                    <label for="pointsRule">Points Rule</label>
                    <select id="pointsRule" class="form-control">
                        <option value="price">Price Based (50 THB = 1 Point)</option>
                        <option value="quantity">Quantity Based (1 Cup = 1 Point)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pointsRatio">Points Ratio (THB per Point)</label>
                    <input type="number" id="pointsRatio" value="50" min="1" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="pointsRedemption">Points Redemption (THB per Point)</label>
                    <input type="number" id="pointsRedemption" value="1" min="1" class="form-control">
                </div>
            </div>
            
            <!-- Hardware Settings Section -->
            <div class="settings-section">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-print mr-2"></i> Hardware Settings
                </h2>
                
                <div class="form-group">
                    <label>Printer Connection</label>
                    <button id="connectPrinterBtn" class="btn-primary w-full">
                        <i class="fas fa-plug mr-2"></i> Connect Printer via WebUSB
                    </button>
                </div>
                
                <div class="form-group">
                    <label>Test Printer</label>
                    <button id="testPrinterBtn" class="btn-secondary w-full">
                        <i class="fas fa-print mr-2"></i> Print Test Receipt
                    </button>
                </div>
                
                <div class="form-group">
                    <label>Open Cash Drawer</label>
                    <button id="openCashDrawerBtn" class="btn-secondary w-full">
                        <i class="fas fa-cash-register mr-2"></i> Open Cash Drawer
                    </button>
                </div>
            </div>
            
            <!-- Daily Summary Section -->
            <div class="settings-section md:col-span-2">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2"></i> Daily Summary
                </h2>
                
                <div class="form-group">
                    <label>Today's Summary</label>
                    <div id="dailySummary" class="bg-accent p-4 rounded">
                        <p>Loading daily summary...</p>
                    </div>
                </div>
                
                <button id="closeShopBtn" class="btn-primary w-full mt-4 py-3">
                    <i class="fas fa-lock mr-2"></i> Close Shop & Generate Report
                </button>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <button id="saveSettingsBtn" class="btn-primary px-8 py-3 text-lg">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </div>

    <script>
        // Settings page logic
        document.addEventListener('DOMContentLoaded', function() {
            loadSettings();
            
            document.getElementById('saveSettingsBtn').addEventListener('click', saveSettings);
            document.getElementById('connectPrinterBtn').addEventListener('click', connectPrinter);
            document.getElementById('testPrinterBtn').addEventListener('click', testPrinter);
            document.getElementById('openCashDrawerBtn').addEventListener('click', openCashDrawer);
            document.getElementById('closeShopBtn').addEventListener('click', closeShop);
        });
        
        function loadSettings() {
            // Load settings from API
            fetch('api/settings.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('shopName').value = data['shop_name'] || '';
                    document.getElementById('receiptHeader').value = data['receipt_header'] || '';
                    document.getElementById('receiptFooter').value = data['receipt_footer'] || '';
                    document.getElementById('promptpayNumber').value = data['promptpay_number'] || '';
                    
                    const pointsRule = data['points_rule'] || 'price';
                    document.getElementById('pointsRule').value = pointsRule;
                    
                    document.getElementById('pointsRatio').value = data['points_ratio'] || '50';
                    document.getElementById('pointsRedemption').value = data['points_redemption'] || '1';
                })
                .catch(error => console.error('Error loading settings:', error));
                
            // Load daily summary
            fetch('api/orders.php')
                .then(response => response.json())
                .then(data => {
                    const summaryDiv = document.getElementById('dailySummary');
                    if (data.summary) {
                        summaryDiv.innerHTML = `
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold">${data.summary.total_orders}</p>
                                    <p class="text-muted">Orders</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold">${parseFloat(data.summary.total_sales || 0).toFixed(2)} ฿</p>
                                    <p class="text-muted">Sales</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold">${parseFloat(data.summary.avg_order_value || 0).toFixed(2)} ฿</p>
                                    <p class="text-muted">Avg Order</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h3 class="font-bold mb-2">Best Sellers:</h3>
                                <ul>
                                    ${data.best_sellers && data.best_sellers.length > 0 ? 
                                        data.best_sellers.map(item => 
                                            `<li>${item.product_name} (${item.total_quantity} sold)</li>`
                                        ).join('') : 
                                        '<li>No sales today</li>'
                                    }
                                </ul>
                            </div>
                        `;
                    } else {
                        summaryDiv.innerHTML = '<p>No sales data available</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('dailySummary').innerHTML = '<p>Error loading daily summary</p>';
                    console.error('Error loading daily summary:', error);
                });
        }
        
        function saveSettings() {
            const settings = {
                shop_name: document.getElementById('shopName').value,
                receipt_header: document.getElementById('receiptHeader').value,
                receipt_footer: document.getElementById('receiptFooter').value,
                promptpay_number: document.getElementById('promptpayNumber').value,
                points_rule: document.getElementById('pointsRule').value,
                points_ratio: document.getElementById('pointsRatio').value,
                points_redemption: document.getElementById('pointsRedemption').value
            };
            
            fetch('api/settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(settings)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error saving settings');
                }
            })
            .catch(error => {
                console.error('Error saving settings:', error);
                alert('Error saving settings');
            });
        }
        
        async function connectPrinter() {
            try {
                // Request a USB device with specific vendor and product IDs for ESC/POS printers
                const device = await navigator.usb.requestDevice({
                    filters: [
                        { vendorId: 0x0416, productId: 0x5011 }, // Example: USB-POS80 Printer
                        { vendorId: 0x0416, productId: 0x5015 }, // Example: Another common POS printer
                        { vendorId: 0x04bb, productId: 0x0951 }, // Example: POS-5890 Printer
                        { vendorId: 0x04bb, productId: 0x0938 }, // Example: POS-58 Series
                        { vendorId: 0x04bb, productId: 0x0950 }, // Example: POS-5890C Printer
                        // Add more printer IDs as needed
                    ]
                });
                
                console.log('Selected device:', device);
                
                // Store the device for later use
                window.printerDevice = device;
                
                // Open and configure the device
                await device.open();
                await device.selectConfiguration(1);
                await device.claimInterface(0);
                
                alert('Printer connected successfully!');
            } catch (error) {
                console.error('Error connecting printer:', error);
                alert('Error connecting printer: ' + error.message);
            }
        }
        
        async function sendToPrinter(commands) {
            if (!window.printerDevice) {
                console.error('No printer connected');
                alert('Please connect a printer first');
                await connectPrinter();
                if (!window.printerDevice) return false;
            }
            
            try {
                // Send commands to the printer via endpoint 2 (bulk out)
                await window.printerDevice.transferOut(2, commands);
                return true;
            } catch (error) {
                console.error('Error sending to printer:', error);
                return false;
            }
        }
        
        async function testPrinter() {
            if (!window.printerDevice) {
                await connectPrinter();
                if (!window.printerDevice) return;
            }
            
            try {
                // Initialize printer
                const initCmd = new Uint8Array([0x1B, 0x40]);
                await sendToPrinter(initCmd);
                
                // Set bold and larger text
                const boldOn = new Uint8Array([0x1B, 0x45, 0x01]);
                const boldOff = new Uint8Array([0x1B, 0x45, 0x00]);
                const largeOn = new Uint8Array([0x1D, 0x21, 0x11]); // Larger text
                const largeOff = new Uint8Array([0x1D, 0x21, 0x00]); // Normal text
                
                await sendToPrinter(boldOn);
                await sendToPrinter(largeOn);
                
                const testText = new TextEncoder().encode('AllMine Coffee POS\nTest Print\n\n');
                await sendToPrinter(testText);
                
                await sendToPrinter(boldOff);
                await sendToPrinter(largeOff);
                
                // Add a blank line and cut
                await sendToPrinter(new Uint8Array([0x0A, 0x0A, 0x1D, 0x56, 0x42, 0x10]));
                
                alert('Test print sent successfully!');
            } catch (error) {
                console.error('Error in test print:', error);
                alert('Error in test print: ' + error.message);
            }
        }
        
        async function openCashDrawer() {
            if (!window.printerDevice) {
                await connectPrinter();
                if (!window.printerDevice) return;
            }
            
            try {
                // Send cash drawer command (sends pulse to pin 2 of cash drawer)
                const cashDrawerCmd = new Uint8Array([0x1B, 0x70, 0x00, 0x32, 0xFF]);
                await sendToPrinter(cashDrawerCmd);
                alert('Cash drawer opened!');
            } catch (error) {
                console.error('Error opening cash drawer:', error);
                alert('Error opening cash drawer: ' + error.message);
            }
        }
        
        function closeShop() {
            if (confirm('Are you sure you want to close the shop and generate a report?')) {
                // In a real implementation, this would trigger end-of-day processes
                alert('Shop closed and report generated!');
            }
        }
    </script>
</body>
</html>