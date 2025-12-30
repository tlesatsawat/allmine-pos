// AllMine Coffee POS JavaScript Logic

class CoffeePOS {
    constructor() {
        this.cart = [];
        this.currentProduct = null;
        this.selectedSize = null;
        this.selectedSweetness = '100%';
        this.selectedToppings = [];
        this.quantity = 1;
        this.currentMember = null;
        this.products = [];
        this.categories = [];
        
        this.init();
    }
    
    init() {
        this.loadMenu();
        this.bindEvents();
        this.loadSettings();
    }
    
    bindEvents() {
        // Category selection
        document.getElementById('categoryList').addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON') {
                this.loadCategoryProducts(e.target.dataset.id);
                document.getElementById('currentCategory').textContent = e.target.textContent;
            }
        });
        
        // Member search
        document.getElementById('searchMemberBtn').addEventListener('click', () => {
            this.searchMember();
        });
        
        document.getElementById('memberPhone').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.searchMember();
            }
        });
        
        // Product modal events
        document.getElementById('decreaseQty').addEventListener('click', () => {
            if (this.quantity > 1) {
                this.quantity--;
                this.updateModalPrice();
            }
        });
        
        document.getElementById('increaseQty').addEventListener('click', () => {
            this.quantity++;
            this.updateModalPrice();
        });
        
        document.getElementById('sizeOptions').addEventListener('click', (e) => {
            if (e.target.classList.contains('size-option')) {
                document.querySelectorAll('#sizeOptions .size-option').forEach(el => {
                    el.classList.remove('selected');
                });
                e.target.classList.add('selected');
                this.selectedSize = {
                    id: e.target.dataset.id,
                    name: e.target.dataset.name,
                    price: parseFloat(e.target.dataset.price)
                };
                this.updateModalPrice();
            }
        });
        
        document.getElementById('sweetnessOptions').addEventListener('click', (e) => {
            if (e.target.classList.contains('sweetness-option')) {
                document.querySelectorAll('#sweetnessOptions .sweetness-option').forEach(el => {
                    el.classList.remove('selected');
                });
                e.target.classList.add('selected');
                this.selectedSweetness = e.target.dataset.value;
            }
        });
        
        document.getElementById('toppingOptions').addEventListener('click', (e) => {
            if (e.target.classList.contains('topping-option')) {
                e.target.classList.toggle('selected');
                const toppingId = e.target.dataset.id;
                const toppingIndex = this.selectedToppings.findIndex(t => t.id == toppingId);
                
                if (toppingIndex > -1) {
                    this.selectedToppings.splice(toppingIndex, 1);
                } else {
                    const topping = {
                        id: toppingId,
                        name: e.target.dataset.name,
                        price: parseFloat(e.target.dataset.price)
                    };
                    this.selectedToppings.push(topping);
                }
                this.updateModalPrice();
            }
        });
        
        // Modal buttons
        document.getElementById('addToCartBtn').addEventListener('click', () => {
            this.addToCart();
        });
        
        document.getElementById('cancelModalBtn').addEventListener('click', () => {
            this.closeProductModal();
        });
        
        // Checkout events
        document.getElementById('checkoutBtn').addEventListener('click', () => {
            this.showCheckoutModal();
        });
        
        document.getElementById('cashPaymentBtn').addEventListener('click', () => {
            this.selectPaymentMethod('cash');
        });
        
        document.getElementById('promptpayPaymentBtn').addEventListener('click', () => {
            this.selectPaymentMethod('promptpay');
        });
        
        document.getElementById('amountReceived').addEventListener('input', () => {
            this.calculateChange();
        });
        
        document.getElementById('completeCashPaymentBtn').addEventListener('click', () => {
            this.completeCashPayment();
        });
        
        document.getElementById('completePromptPayBtn').addEventListener('click', () => {
            this.completePromptPayPayment();
        });
        
        document.getElementById('cancelCheckoutBtn').addEventListener('click', () => {
            this.closeCheckoutModal();
        });
        
        // Settings and customer display
        document.getElementById('settingsBtn').addEventListener('click', () => {
            this.showSettingsModal();
        });
        
        document.getElementById('customerDisplayBtn').addEventListener('click', () => {
            this.openCustomerDisplay();
        });
        
        document.getElementById('closeSettingsBtn').addEventListener('click', () => {
            this.closeSettingsModal();
        });
        
        document.getElementById('closeCustomerDisplayBtn').addEventListener('click', () => {
            this.closeCustomerDisplayModal();
        });
        
        // New order button
        document.getElementById('newOrderBtn').addEventListener('click', () => {
            this.newOrder();
        });
        
        // Close shop
        document.getElementById('closeShopBtn').addEventListener('click', () => {
            this.closeShop();
        });
    }
    
    async loadMenu() {
        try {
            const response = await fetch('api/menu.php');
            const data = await response.json();
            
            this.categories = data;
            this.renderCategories();
            
            // Load all products by default
            this.loadAllProducts();
        } catch (error) {
            console.error('Error loading menu:', error);
        }
    }
    
    renderCategories() {
        const categoryList = document.getElementById('categoryList');
        categoryList.innerHTML = '';
        
        // Add "All" category
        const allBtn = document.createElement('button');
        allBtn.className = 'w-full text-left p-2 rounded hover:bg-accent';
        allBtn.textContent = 'All Products';
        allBtn.dataset.id = 'all';
        categoryList.appendChild(allBtn);
        
        // Add other categories
        this.categories.forEach(category => {
            const btn = document.createElement('button');
            btn.className = 'w-full text-left p-2 rounded hover:bg-accent';
            btn.textContent = category.name;
            btn.dataset.id = category.id;
            categoryList.appendChild(btn);
        });
    }
    
    loadAllProducts() {
        document.getElementById('currentCategory').textContent = 'All Products';
        this.products = [];
        
        this.categories.forEach(category => {
            this.products = this.products.concat(category.products);
        });
        
        this.renderProducts(this.products);
    }
    
    loadCategoryProducts(categoryId) {
        if (categoryId === 'all') {
            this.loadAllProducts();
            return;
        }
        
        const category = this.categories.find(c => c.id == categoryId);
        if (category) {
            document.getElementById('currentCategory').textContent = category.name;
            this.renderProducts(category.products);
        }
    }
    
    renderProducts(products) {
        const productGrid = document.getElementById('productGrid');
        productGrid.innerHTML = '';
        
        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.dataset.id = product.id;
            productCard.innerHTML = `
                <div class="product-image">
                    <i class="fas fa-coffee text-3xl text-gray-400"></i>
                </div>
                <div class="p-2">
                    <h3 class="font-bold">${product.name}</h3>
                    <p class="text-primary font-bold">${product.price.toFixed(2)} ฿</p>
                </div>
            `;
            
            productCard.addEventListener('click', () => {
                this.openProductModal(product);
            });
            
            productGrid.appendChild(productCard);
        });
    }
    
    openProductModal(product) {
        this.currentProduct = product;
        this.selectedSize = null;
        this.selectedSweetness = '100%';
        this.selectedToppings = [];
        this.quantity = 1;
        
        document.getElementById('modalProductName').textContent = product.name;
        
        // Render size options
        const sizeOptions = document.getElementById('sizeOptions');
        sizeOptions.innerHTML = '';
        
        if (product.sizes && product.sizes.length > 0) {
            product.sizes.forEach(size => {
                const sizeBtn = document.createElement('span');
                sizeBtn.className = 'size-option';
                sizeBtn.dataset.id = size.id;
                sizeBtn.dataset.name = size.name;
                sizeBtn.dataset.price = size.price;
                sizeBtn.textContent = `${size.name} - ${size.price.toFixed(2)} ฿`;
                sizeOptions.appendChild(sizeBtn);
            });
        } else {
            // If no specific sizes, use the base product price
            const sizeBtn = document.createElement('span');
            sizeBtn.className = 'size-option selected';
            sizeBtn.dataset.id = 'base';
            sizeBtn.dataset.name = 'Regular';
            sizeBtn.dataset.price = product.price;
            sizeBtn.textContent = `Regular - ${product.price.toFixed(2)} ฿`;
            sizeOptions.appendChild(sizeBtn);
            this.selectedSize = {
                id: 'base',
                name: 'Regular',
                price: product.price
            };
        }
        
        // Reset sweetness selection
        document.querySelectorAll('#sweetnessOptions .sweetness-option').forEach(el => {
            el.classList.remove('selected');
        });
        document.querySelector('#sweetnessOptions .sweetness-option[data-value="100%"]').classList.add('selected');
        
        // Render topping options
        this.loadToppings();
        
        // Reset special instructions
        document.getElementById('specialInstructions').value = '';
        
        // Update price
        this.updateModalPrice();
        
        // Show modal
        document.getElementById('productModal').style.display = 'flex';
    }
    
    async loadToppings() {
        try {
            const response = await fetch('api/toppings.php');
            const toppings = await response.json();
            
            const toppingOptions = document.getElementById('toppingOptions');
            toppingOptions.innerHTML = '';
            
            toppings.forEach(topping => {
                const toppingBtn = document.createElement('span');
                toppingBtn.className = 'topping-option';
                toppingBtn.dataset.id = topping.id;
                toppingBtn.dataset.name = topping.name;
                toppingBtn.dataset.price = topping.price;
                toppingBtn.textContent = `${topping.name} +${topping.price.toFixed(2)} ฿`;
                toppingOptions.appendChild(toppingBtn);
            });
        } catch (error) {
            console.error('Error loading toppings:', error);
            
            // Default toppings if API fails
            const defaultToppings = [
                { id: 1, name: 'Whipped Cream', price: 10.00 },
                { id: 2, name: 'Extra Shot', price: 15.00 },
                { id: 3, name: 'Caramel Syrup', price: 10.00 },
                { id: 4, name: 'Vanilla Syrup', price: 10.00 }
            ];
            
            const toppingOptions = document.getElementById('toppingOptions');
            toppingOptions.innerHTML = '';
            
            defaultToppings.forEach(topping => {
                const toppingBtn = document.createElement('span');
                toppingBtn.className = 'topping-option';
                toppingBtn.dataset.id = topping.id;
                toppingBtn.dataset.name = topping.name;
                toppingBtn.dataset.price = topping.price;
                toppingBtn.textContent = `${topping.name} +${topping.price.toFixed(2)} ฿`;
                toppingOptions.appendChild(toppingBtn);
            });
        }
    }
    
    updateModalPrice() {
        let price = this.selectedSize ? this.selectedSize.price : this.currentProduct.price;
        
        // Add topping prices
        this.selectedToppings.forEach(topping => {
            price += topping.price;
        });
        
        const totalPrice = price * this.quantity;
        document.getElementById('modalPrice').textContent = `${totalPrice.toFixed(2)} ฿`;
    }
    
    closeProductModal() {
        document.getElementById('productModal').style.display = 'none';
    }
    
    addToCart() {
        if (!this.selectedSize) {
            alert('Please select a size');
            return;
        }
        
        const cartItem = {
            id: this.currentProduct.id,
            name: this.currentProduct.name,
            size: this.selectedSize.name,
            sweetness: this.selectedSweetness,
            toppings: [...this.selectedToppings],
            price: this.selectedSize.price,
            basePrice: this.selectedSize.price,
            quantity: this.quantity,
            specialInstructions: document.getElementById('specialInstructions').value
        };
        
        // Add topping prices to the item price
        cartItem.toppings.forEach(topping => {
            cartItem.price += topping.price;
        });
        
        // Add to cart
        this.cart.push(cartItem);
        this.renderCart();
        
        // Close modal and reset
        this.closeProductModal();
    }
    
    renderCart() {
        const cartItems = document.getElementById('cartItems');
        cartItems.innerHTML = '';
        
        this.cart.forEach((item, index) => {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <div>
                    <div class="font-bold">${item.quantity}x ${item.name} ${item.size}</div>
                    <div class="text-sm text-muted">${item.sweetness} sweetness</div>
                    ${item.toppings.length > 0 ? 
                        `<div class="text-sm text-muted">${item.toppings.map(t => t.name).join(', ')}</div>` 
                        : ''}
                    ${item.specialInstructions ? 
                        `<div class="text-sm text-muted">Note: ${item.specialInstructions}</div>` 
                        : ''}
                </div>
                <div class="text-right">
                    <div>${(item.price * item.quantity).toFixed(2)} ฿</div>
                    <button class="text-red-500 text-sm" onclick="pos.removeFromCart(${index})">Remove</button>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });
        
        // Update total
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        document.getElementById('cartTotal').textContent = `${total.toFixed(2)} ฿`;
    }
    
    removeFromCart(index) {
        this.cart.splice(index, 1);
        this.renderCart();
    }
    
    async searchMember() {
        const phone = document.getElementById('memberPhone').value.trim();
        
        if (!phone) {
            alert('Please enter a phone number');
            return;
        }
        
        try {
            const response = await fetch(`api/members.php?phone=${encodeURIComponent(phone)}`);
            const data = await response.json();
            
            if (data.error) {
                if (confirm('Member not found. Would you like to create a new member?')) {
                    const name = prompt('Enter customer name:');
                    if (name) {
                        await this.createMember(phone, name);
                    }
                }
            } else {
                this.currentMember = data;
                this.showMemberInfo();
            }
        } catch (error) {
            console.error('Error searching member:', error);
            alert('Error searching member');
        }
    }
    
    async createMember(phone, name) {
        try {
            const response = await fetch('api/members.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ phone, name })
            });
            
            const data = await response.json();
            
            if (data.error) {
                alert('Error creating member: ' + data.error);
            } else {
                this.currentMember = data;
                this.showMemberInfo();
            }
        } catch (error) {
            console.error('Error creating member:', error);
            alert('Error creating member');
        }
    }
    
    showMemberInfo() {
        if (this.currentMember) {
            document.getElementById('memberName').textContent = this.currentMember.name;
            document.getElementById('memberPoints').textContent = this.currentMember.points;
            document.getElementById('memberInfo').classList.remove('hidden');
        }
    }
    
    showCheckoutModal() {
        if (this.cart.length === 0) {
            alert('Cart is empty');
            return;
        }
        
        // Set checkout total
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        document.getElementById('checkoutTotal').textContent = `${total.toFixed(2)} ฿`;
        
        document.getElementById('checkoutModal').style.display = 'flex';
    }
    
    selectPaymentMethod(method) {
        if (method === 'cash') {
            document.getElementById('cashPaymentSection').classList.remove('hidden');
            document.getElementById('promptpayPaymentSection').classList.add('hidden');
        } else if (method === 'promptpay') {
            document.getElementById('cashPaymentSection').classList.add('hidden');
            document.getElementById('promptpayPaymentSection').classList.remove('hidden');
            
            // Generate QR code for PromptPay
            this.generatePromptPayQR();
        }
    }
    
    generatePromptPayQR() {
        // In a real implementation, this would generate an actual PromptPay QR code
        // For now, we'll just show a placeholder
        document.getElementById('promptpayQr').innerHTML = '<div class="bg-gray-200 border-2 border-dashed rounded-xl w-48 h-48 flex items-center justify-center text-gray-500">QR Code</div>';
    }
    
    calculateChange() {
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const received = parseFloat(document.getElementById('amountReceived').value) || 0;
        const change = received - total;
        
        document.getElementById('changeAmount').textContent = `${change >= 0 ? change.toFixed(2) : '0.00'} ฿`;
    }
    
    async completeCashPayment() {
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const received = parseFloat(document.getElementById('amountReceived').value);
        
        if (!received || received < total) {
            alert('Please enter sufficient amount');
            return;
        }
        
        await this.saveOrder('cash');
        
        // Trigger cash drawer
        await this.triggerCashDrawer();
        
        this.resetOrder();
    }
    
    async completePromptPayPayment() {
        await this.saveOrder('promptpay');
        this.resetOrder();
    }
    
    async saveOrder(paymentMethod) {
        try {
            const orderData = {
                queue_number: this.generateQueueNumber(),
                total_price: this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
                payment_method: paymentMethod,
                member_id: this.currentMember ? this.currentMember.id : null,
                items: this.cart.map(item => ({
                    name: item.name,
                    size: item.size,
                    sweetness: item.sweetness,
                    toppings: item.toppings,
                    price: item.price * item.quantity,
                    quantity: item.quantity
                }))
            };
            
            const response = await fetch('api/orders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`Order placed successfully! Queue: ${result.queue_number}`);
                
                // Print receipt
                await this.printReceipt(orderData);
                
                // Update customer display with order information
                this.updateCustomerDisplay(orderData);
            } else {
                alert('Error saving order: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error saving order:', error);
            alert('Error saving order');
        }
    }
    
    generateQueueNumber() {
        // In a real implementation, this would generate a unique queue number
        // For now, we'll generate a simple one based on time
        const now = new Date();
        const queueNum = Math.floor(Math.random() * 1000) + 1;
        return `A${String(queueNum).padStart(3, '0')}`;
    }
    
    closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }
    
    showSettingsModal() {
        document.getElementById('settingsModal').style.display = 'flex';
    }
    
    closeSettingsModal() {
        document.getElementById('settingsModal').style.display = 'none';
    }
    
    openCustomerDisplay() {
        // Open customer display in a new window/tab
        window.open('customer.php', 'customer_display', 'width=1024,height=768');
    }
    
    closeCustomerDisplayModal() {
        document.getElementById('customerDisplayModal').style.display = 'none';
    }
    
    async loadSettings() {
        try {
            const response = await fetch('api/settings.php');
            const settings = await response.json();
            
            // Store settings for later use
            this.settings = settings;
        } catch (error) {
            console.error('Error loading settings:', error);
        }
    }
    
    resetOrder() {
        this.cart = [];
        this.currentMember = null;
        document.getElementById('memberInfo').classList.add('hidden');
        document.getElementById('memberPhone').value = '';
        this.renderCart();
        this.closeCheckoutModal();
    }
    
    closeShop() {
        if (confirm('Are you sure you want to close the shop? This will generate a daily report.')) {
            // In a real implementation, this would trigger end-of-day processes
            fetch('api/orders.php')
                .then(response => response.json())
                .then(data => {
                    let report = `Daily Report:\n`;
                    report += `Total Orders: ${data.summary.total_orders}\n`;
                    report += `Total Sales: ${parseFloat(data.summary.total_sales || 0).toFixed(2)} ฿\n`;
                    report += `Avg Order Value: ${parseFloat(data.summary.avg_order_value || 0).toFixed(2)} ฿\n`;
                    
                    if (data.best_sellers && data.best_sellers.length > 0) {
                        report += `\nBest Sellers:\n`;
                        data.best_sellers.forEach(item => {
                            report += `${item.product_name}: ${item.total_quantity} sold\n`;
                        });
                    }
                    
                    // Mock AI analysis
                    if (data.best_sellers && data.best_sellers.length > 0) {
                        const bestSeller = data.best_sellers[0].product_name;
                        report += `\nAI Analysis: Tomorrow you should restock ${bestSeller} ingredients.`;
                    }
                    
                    alert(report);
                })
                .catch(error => {
                    console.error('Error generating report:', error);
                    alert('Error generating report');
                });
        }
    }
    
    async connectPrinter() {
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
            this.printerDevice = device;
            
            // Open and configure the device
            await device.open();
            await device.selectConfiguration(1);
            await device.claimInterface(0);
            
            alert('Printer connected successfully!');
            return true;
        } catch (error) {
            console.error('Error connecting printer:', error);
            alert('Error connecting printer: ' + error.message);
            return false;
        }
    }
    
    async sendToPrinter(commands) {
        if (!this.printerDevice) {
            console.error('No printer connected');
            return false;
        }
        
        try {
            // Send commands to the printer via endpoint 2 (bulk out)
            await this.printerDevice.transferOut(2, commands);
            return true;
        } catch (error) {
            console.error('Error sending to printer:', error);
            return false;
        }
    }
    
    async printReceipt(orderData) {
        if (!this.printerDevice) {
            if (!await this.connectPrinter()) {
                return false;
            }
        }
        
        try {
            // Initialize printer
            const initCmd = new Uint8Array([0x1B, 0x40]);
            await this.sendToPrinter(initCmd);
            
            // Set bold and larger text for shop name
            const shopName = new TextEncoder().encode('AllMine Coffee POS\n');
            const boldOn = new Uint8Array([0x1B, 0x45, 0x01]);
            const boldOff = new Uint8Array([0x1B, 0x45, 0x00]);
            const largeOn = new Uint8Array([0x1D, 0x21, 0x11]); // Larger text
            const largeOff = new Uint8Array([0x1D, 0x21, 0x00]); // Normal text
            
            await this.sendToPrinter(boldOn);
            await this.sendToPrinter(largeOn);
            await this.sendToPrinter(shopName);
            await this.sendToPrinter(boldOff);
            await this.sendToPrinter(largeOff);
            
            // Add a blank line
            await this.sendToPrinter(new Uint8Array([0x0A]));
            
            // Print queue number
            const queueText = new TextEncoder().encode('Queue: ' + orderData.queue_number + '\n\n');
            await this.sendToPrinter(queueText);
            
            // Print items
            for (const item of orderData.items) {
                let itemText = `${item.quantity}x ${item.name}`;
                if (item.size) itemText += ` ${item.size}`;
                if (item.sweetness && item.sweetness !== '100%') itemText += ` ${item.sweetness}`;
                itemText += `\n${item.price.toFixed(2)} ฿\n`;
                
                const encodedItem = new TextEncoder().encode(itemText);
                await this.sendToPrinter(encodedItem);
                
                // Print toppings if any
                if (item.toppings && item.toppings.length > 0) {
                    const toppingsText = '  + ' + item.toppings.map(t => t.name).join(', ') + '\n';
                    const encodedToppings = new TextEncoder().encode(toppingsText);
                    await this.sendToPrinter(encodedToppings);
                }
            }
            
            // Add a separator
            const separator = new TextEncoder().encode('------------------------\n');
            await this.sendToPrinter(separator);
            
            // Print total
            const totalText = new TextEncoder().encode(`TOTAL: ${orderData.total_price.toFixed(2)} ฿\n\n`);
            await this.sendToPrinter(totalText);
            
            // Print thank you message
            const thankYou = new TextEncoder().encode('Thank you for visiting!\nAllMine Coffee POS\n');
            await this.sendToPrinter(thankYou);
            
            // Add blank lines and cut paper
            await this.sendToPrinter(new Uint8Array([0x0A, 0x0A, 0x0A, 0x1D, 0x56, 0x42, 0x10])); // Feed and cut
            
            return true;
        } catch (error) {
            console.error('Error printing receipt:', error);
            return false;
        }
    }
    
    async triggerCashDrawer() {
        if (!this.printerDevice) {
            if (!await this.connectPrinter()) {
                return false;
            }
        }
        
        try {
            // Send cash drawer command (sends pulse to pin 2 of cash drawer)
            const cashDrawerCmd = new Uint8Array([0x1B, 0x70, 0x00, 0x32, 0xFF]);
            await this.sendToPrinter(cashDrawerCmd);
            console.log('Cash drawer triggered');
            return true;
        } catch (error) {
            console.error('Error triggering cash drawer:', error);
            return false;
        }
    }
    
    newOrder() {
        if (this.cart.length > 0) {
            if (!confirm('Current cart is not empty. Start a new order?')) {
                return;
            }
        }
        
        this.cart = [];
        this.currentMember = null;
        document.getElementById('memberInfo').classList.add('hidden');
        document.getElementById('memberPhone').value = '';
        this.renderCart();
    }
    
    updateCustomerDisplay(orderData) {
        // In a real implementation, this would update the customer display via WebSocket or other means
        // For now, we'll just log the update
        console.log('Updating customer display with:', orderData);
    }
}

// Initialize POS when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pos = new CoffeePOS();
});

// Modal close when clicking outside
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});