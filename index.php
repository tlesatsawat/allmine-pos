<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AllMine Coffee POS</title>
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
        .border-primary { border-color: #8D6E63; }
        .bg-accent { background-color: #F2E8E5; }
        .text-muted { color: #BCAAA4; }
        .sidebar {
            width: 250px;
            background: #F2E8E5;
            border-right: 1px solid #D7CCC8;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
        }
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .product-image {
            height: 100px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.5rem;
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
        .btn-danger {
            background-color: #EF5350;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
        }
        .size-option, .sweetness-option, .topping-option {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.5rem 1rem;
            border: 1px solid #D7CCC8;
            border-radius: 20px;
            cursor: pointer;
        }
        .size-option.selected, .sweetness-option.selected, .topping-option.selected {
            background-color: #8D6E63;
            color: white;
            border-color: #8D6E63;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-primary text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">AllMine Coffee POS</h1>
            <div class="flex items-center space-x-4">
                <button id="settingsBtn" class="bg-white text-primary px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-cog mr-2"></i>Settings
                </button>
                <button id="customerDisplayBtn" class="bg-white text-primary px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-tv mr-2"></i>Customer Display
                </button>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="sidebar p-4 overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Categories</h2>
            <ul id="categoryList" class="space-y-2">
                <!-- Categories will be loaded here -->
            </ul>
            
            <div class="mt-8">
                <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                <button id="newOrderBtn" class="w-full btn-primary py-3 mb-2">
                    <i class="fas fa-plus mr-2"></i>New Order
                </button>
                <button id="closeShopBtn" class="w-full btn-danger py-3">
                    <i class="fas fa-lock mr-2"></i>Close Shop
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 overflow-y-auto">
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="currentCategory" class="text-2xl font-bold">All Products</h2>
                    <div class="flex items-center">
                        <span class="mr-2">Member:</span>
                        <input type="text" id="memberPhone" placeholder="Phone number" class="border p-2 rounded mr-2 w-40">
                        <button id="searchMemberBtn" class="btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div id="memberInfo" class="bg-accent p-3 rounded-lg mb-4 hidden">
                    <div class="flex justify-between items-center">
                        <div>
                            <span id="memberName" class="font-bold"></span> - Points: <span id="memberPoints" class="font-bold"></span>
                        </div>
                        <button id="redeemPointsBtn" class="btn-secondary text-sm">Redeem Points</button>
                    </div>
                </div>
            </div>
            
            <div id="productGrid" class="product-grid">
                <!-- Products will be loaded here -->
            </div>
        </main>

        <!-- Cart -->
        <aside class="w-96 bg-white border-l p-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-4">Cart</h2>
            
            <div id="cartItems" class="flex-1 overflow-y-auto mb-4">
                <!-- Cart items will appear here -->
            </div>
            
            <div class="mt-auto border-t pt-4">
                <div class="flex justify-between text-lg font-bold mb-4">
                    <span>Total:</span>
                    <span id="cartTotal">0.00 ฿</span>
                </div>
                
                <button id="checkoutBtn" class="w-full btn-primary py-3 text-lg">
                    <i class="fas fa-check mr-2"></i>Checkout
                </button>
            </div>
        </aside>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <h3 id="modalProductName" class="text-xl font-bold mb-4"></h3>
            
            <div class="mb-4">
                <label class="block mb-2">Size:</label>
                <div id="sizeOptions" class="flex flex-wrap">
                    <!-- Size options will be loaded here -->
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Sweetness:</label>
                <div id="sweetnessOptions" class="flex flex-wrap">
                    <span class="sweetness-option" data-value="0%">0%</span>
                    <span class="sweetness-option" data-value="25%">25%</span>
                    <span class="sweetness-option" data-value="50%">50%</span>
                    <span class="sweetness-option" data-value="100%">100%</span>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Toppings:</label>
                <div id="toppingOptions" class="flex flex-wrap">
                    <!-- Toppings will be loaded here -->
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Special Instructions:</label>
                <textarea id="specialInstructions" class="w-full border p-2 rounded" rows="2"></textarea>
            </div>
            
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-bold">Price: <span id="modalPrice">0.00 ฿</span></span>
                <div class="flex items-center">
                    <button id="decreaseQty" class="btn-secondary w-8 h-8">-</button>
                    <span id="quantity" class="mx-2">1</span>
                    <button id="increaseQty" class="btn-secondary w-8 h-8">+</button>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button id="addToCartBtn" class="flex-1 btn-primary">Add to Cart</button>
                <button id="cancelModalBtn" class="flex-1 btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <h3 class="text-xl font-bold mb-4">Checkout</h3>
            
            <div class="mb-4">
                <div class="flex space-x-4">
                    <button id="cashPaymentBtn" class="flex-1 btn-primary py-3">
                        <i class="fas fa-money-bill-wave mr-2"></i>Cash
                    </button>
                    <button id="promptpayPaymentBtn" class="flex-1 btn-primary py-3">
                        <i class="fas fa-qrcode mr-2"></i>PromptPay
                    </button>
                </div>
            </div>
            
            <!-- Cash Payment Section -->
            <div id="cashPaymentSection" class="hidden">
                <div class="mb-4">
                    <label class="block mb-2">Amount Received:</label>
                    <input type="number" id="amountReceived" class="w-full border p-2 rounded" placeholder="Enter amount">
                </div>
                <div class="mb-4">
                    <div class="flex justify-between">
                        <span>Total:</span>
                        <span id="checkoutTotal">0.00 ฿</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Change:</span>
                        <span id="changeAmount">0.00 ฿</span>
                    </div>
                </div>
                <button id="completeCashPaymentBtn" class="w-full btn-primary py-3">Complete Payment</button>
            </div>
            
            <!-- PromptPay Section -->
            <div id="promptpayPaymentSection" class="hidden">
                <div class="text-center mb-4">
                    <div id="promptpayQr" class="flex justify-center mb-4">
                        <!-- QR code will be generated here -->
                    </div>
                    <p>Scan QR code to pay</p>
                </div>
                <button id="completePromptPayBtn" class="w-full btn-primary py-3">Complete Payment</button>
            </div>
            
            <button id="cancelCheckoutBtn" class="w-full btn-secondary py-2 mt-2">Cancel</button>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal">
        <div class="modal-content">
            <h3 class="text-xl font-bold mb-4">Settings</h3>
            <p>Settings page will be implemented here.</p>
            <button id="closeSettingsBtn" class="btn-secondary mt-4">Close</button>
        </div>
    </div>

    <!-- Customer Display Modal -->
    <div id="customerDisplayModal" class="modal">
        <div class="modal-content">
            <h3 class="text-xl font-bold mb-4">Customer Display</h3>
            <p>Customer display page will be implemented here.</p>
            <button id="closeCustomerDisplayBtn" class="btn-secondary mt-4">Close</button>
        </div>
    </div>

    <script src="/assets/js/pos.js"></script>
</body>
</html>
