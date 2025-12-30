<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display - AllMine Coffee POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #FDF8F6;
            color: #4E342E;
            height: 100vh;
            overflow: hidden;
        }
        .bg-primary { background-color: #8D6E63; }
        .text-primary { color: #8D6E63; }
        .bg-accent { background-color: #F2E8E5; }
        .text-muted { color: #BCAAA4; }
        
        .welcome-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        
        .welcome-text {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #4E342E;
        }
        
        .queue-display {
            font-size: 8rem;
            font-weight: bold;
            color: #8D6E63;
            margin: 2rem 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .cart-section {
            padding: 2rem;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #D7CCC8;
            font-size: 1.5rem;
        }
        
        .cart-total {
            font-size: 2rem;
            font-weight: bold;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #8D6E63;
        }
        
        .promotional-slider {
            display: flex;
            overflow: hidden;
        }
        
        .promo-item {
            min-width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-position: center;
        }
        
        .promo-text {
            background: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 1rem;
            max-width: 80%;
            text-align: center;
        }
        
        .member-greeting {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #4E342E;
            animation: fadeIn 1s ease-in-out;
        }
        
        .member-points {
            font-size: 2rem;
            color: #8D6E63;
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <!-- Welcome Section (Default View) -->
    <div id="welcomeSection" class="welcome-section">
        <h1 class="welcome-text">Welcome to AllMine Coffee POS</h1>
        <p class="text-2xl text-muted">Please place your order at the counter</p>
    </div>

    <!-- Member Greeting Section -->
    <div id="memberSection" class="welcome-section hidden">
        <h2 id="memberGreeting" class="member-greeting">Welcome Customer</h2>
        <p id="memberPointsDisplay" class="member-points">Points: 0</p>
    </div>

    <!-- Queue Display Section -->
    <div id="queueSection" class="welcome-section hidden">
        <h2 class="welcome-text">Calling Queue</h2>
        <div id="queueNumber" class="queue-display">A001</div>
        <p class="text-2xl text-muted">Please prepare your order</p>
    </div>

    <!-- Active Order Section -->
    <div id="orderSection" class="hidden">
        <div class="cart-section">
            <h2 class="text-3xl font-bold text-center mb-6">Your Order</h2>
            <div id="orderItems">
                <!-- Order items will be populated here -->
            </div>
            <div id="orderTotal" class="cart-total text-center">
                Total: 0.00 ฿
            </div>
        </div>
    </div>

    <!-- Promotional Slider Section -->
    <div id="promoSection" class="promotional-slider hidden">
        <!-- Promotional slides will be added here -->
    </div>

    <script>
        // Customer display logic
        document.addEventListener('DOMContentLoaded', function() {
            // In a real implementation, this would connect to the POS system via WebSocket or polling
            // For this example, we'll use periodic polling to get the latest order information
            
            // Show welcome message by default
            showWelcome();
            
            // Start polling for updates
            setInterval(checkForUpdates, 2000);
            
            // Also check for updates when the page becomes visible again
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    checkForUpdates();
                }
            });
        });
        
        let lastOrderId = null;
        
        async function checkForUpdates() {
            try {
                // In a real implementation, this would be a WebSocket connection or Server-Sent Events
                // For this example, we'll use a simple polling approach
                
                // Get the latest order
                const response = await fetch('api/orders.php');
                const data = await response.json();
                
                if (data.best_sellers && data.best_sellers.length > 0) {
                    // Show the most recent order
                    const recentOrder = data.best_sellers[0];
                    
                    // For demo purposes, we'll simulate different states
                    const states = ['welcome', 'member', 'queue', 'order'];
                    const randomState = states[Math.floor(Math.random() * states.length)];
                    
                    switch(randomState) {
                        case 'member':
                            showMember('John Doe', 25);
                            break;
                        case 'queue':
                            showQueue('A' + String(Math.floor(Math.random() * 999) + 1).padStart(3, '0'));
                            break;
                        case 'order':
                            showOrder([
                                {name: 'Cappuccino', size: 'M', quantity: 1, price: 85.00},
                                {name: 'Croissant', quantity: 1, price: 45.00}
                            ], 130.00);
                            break;
                        default:
                            showWelcome();
                    }
                }
            } catch (error) {
                console.error('Error checking for updates:', error);
            }
        }
        
        function showWelcome() {
            document.getElementById('welcomeSection').classList.remove('hidden');
            document.getElementById('memberSection').classList.add('hidden');
            document.getElementById('queueSection').classList.add('hidden');
            document.getElementById('orderSection').classList.add('hidden');
            document.getElementById('promoSection').classList.add('hidden');
        }
        
        function showMember(name, points) {
            document.getElementById('welcomeSection').classList.add('hidden');
            document.getElementById('memberSection').classList.remove('hidden');
            document.getElementById('queueSection').classList.add('hidden');
            document.getElementById('orderSection').classList.add('hidden');
            document.getElementById('promoSection').classList.add('hidden');
            
            document.getElementById('memberGreeting').textContent = `Welcome ${name}`;
            document.getElementById('memberPointsDisplay').textContent = `Points: ${points}`;
        }
        
        function showQueue(queueNumber) {
            document.getElementById('welcomeSection').classList.add('hidden');
            document.getElementById('memberSection').classList.add('hidden');
            document.getElementById('queueSection').classList.remove('hidden');
            document.getElementById('orderSection').classList.add('hidden');
            document.getElementById('promoSection').classList.add('hidden');
            
            document.getElementById('queueNumber').textContent = queueNumber;
        }
        
        function showOrder(items, total) {
            document.getElementById('welcomeSection').classList.add('hidden');
            document.getElementById('memberSection').classList.add('hidden');
            document.getElementById('queueSection').classList.add('hidden');
            document.getElementById('orderSection').classList.remove('hidden');
            document.getElementById('promoSection').classList.add('hidden');
            
            const orderItemsContainer = document.getElementById('orderItems');
            orderItemsContainer.innerHTML = '';
            
            items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item slide-in';
                itemElement.innerHTML = `
                    <span>${item.quantity}x ${item.name} ${item.size ? item.size : ''}</span>
                    <span>${item.price.toFixed(2)} ฿</span>
                `;
                orderItemsContainer.appendChild(itemElement);
            });
            
            document.getElementById('orderTotal').textContent = `Total: ${total.toFixed(2)} ฿`;
        }
        
        // In a real implementation, you would connect to the POS system to receive updates
        // This could be done via WebSocket, Server-Sent Events, or periodic polling
    </script>
</body>
</html>