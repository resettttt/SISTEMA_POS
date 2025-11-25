<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cash-register me-2"></i>Point of Sale</h2>
    <a href="?controller=Sales&action=index" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Sales
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shopping-cart me-2"></i>Cart</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Cart items will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer (Optional)</label>
                            <select class="form-control" id="customer_id">
                                <option value="">Walk-in Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>">
                                        <?php echo htmlspecialchars($customer['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-control" id="payment_method">
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Mobile Payment">Mobile Payment</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Discount: $<span id="discount-amount">0.00</span></h5>
                        <div class="input-group" style="width: 200px;">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="discount" value="0" min="0">
                        </div>
                    </div>
                    <div class="text-end">
                        <h4>Total: $<span id="total-amount">0.00</span></h4>
                        <button class="btn btn-success btn-lg" id="complete-sale">
                            <i class="fas fa-check me-1"></i>Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-search me-2"></i>Products</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="search-product" placeholder="Search products...">
                </div>
                
                <div class="list-group" id="product-list">
                    <?php foreach ($products as $product): ?>
                        <div class="list-group-item product-item" 
                             data-id="<?php echo $product['id']; ?>" 
                             data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                             data-price="<?php echo $product['price']; ?>"
                             data-stock="<?php echo $product['stock']; ?>">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                    <small class="text-muted">Stock: <?php echo $product['stock']; ?></small>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">$<?php echo number_format($product['price'], 2); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    const cartTable = document.getElementById('cart-table').getElementsByTagName('tbody')[0];
    const totalAmountEl = document.getElementById('total-amount');
    const discountEl = document.getElementById('discount');
    const discountAmountEl = document.getElementById('discount-amount');
    const searchProduct = document.getElementById('search-product');
    const productList = document.getElementById('product-list');
    const completeSaleBtn = document.getElementById('complete-sale');
    
    // Add product to cart
    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            const stock = parseInt(this.getAttribute('data-stock'));
            
            // Check if product is in stock
            if (stock <= 0) {
                alert('Product is out of stock!');
                return;
            }
            
            // Check if product is already in cart
            const existingItem = cart.find(item => item.product_id === id);
            
            if (existingItem) {
                // Check if adding one more would exceed stock
                if (existingItem.quantity >= stock) {
                    alert('Not enough stock available!');
                    return;
                }
                existingItem.quantity += 1;
            } else {
                cart.push({
                    product_id: id,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }
            
            renderCart();
        });
    });
    
    // Update discount
    discountEl.addEventListener('input', function() {
        renderCart();
    });
    
    // Complete sale
    completeSaleBtn.addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        
        const customer_id = document.getElementById('customer_id').value;
        const payment_method = document.getElementById('payment_method').value;
        const discount = parseFloat(discountEl.value) || 0;
        const total = calculateTotal();
        
        // Send the sale data to the server
        const formData = new FormData();
        formData.append('items', JSON.stringify(cart));
        formData.append('customer_id', customer_id);
        formData.append('payment_method', payment_method);
        formData.append('discount', discount);
        formData.append('total_amount', total);
        
        // Submit the form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?controller=Sales&action=create';
        
        // Add form data
        const itemsInput = document.createElement('input');
        itemsInput.type = 'hidden';
        itemsInput.name = 'items';
        itemsInput.value = JSON.stringify(cart);
        form.appendChild(itemsInput);
        
        const customerIdInput = document.createElement('input');
        customerIdInput.type = 'hidden';
        customerIdInput.name = 'customer_id';
        customerIdInput.value = customer_id;
        form.appendChild(customerIdInput);
        
        const paymentMethodInput = document.createElement('input');
        paymentMethodInput.type = 'hidden';
        paymentMethodInput.name = 'payment_method';
        paymentMethodInput.value = payment_method;
        form.appendChild(paymentMethodInput);
        
        const discountInput = document.createElement('input');
        discountInput.type = 'hidden';
        discountInput.name = 'discount';
        discountInput.value = discount;
        form.appendChild(discountInput);
        
        const totalAmountInput = document.createElement('input');
        totalAmountInput.type = 'hidden';
        totalAmountInput.name = 'total_amount';
        totalAmountInput.value = total;
        form.appendChild(totalAmountInput);
        
        document.body.appendChild(form);
        form.submit();
    });
    
    function renderCart() {
        // Clear the cart table
        cartTable.innerHTML = '';
        
        // Add each item to the cart table
        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td>${item.name}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control mx-2" value="${item.quantity}" min="1" style="width: 60px;" onchange="updateQuantity(${index}, 0, this.value)">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                </td>
                <td>$${(item.price * item.quantity).toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            cartTable.appendChild(row);
        });
        
        // Update total
        const total = calculateTotal();
        totalAmountEl.textContent = total.toFixed(2);
        discountAmountEl.textContent = (parseFloat(discountEl.value) || 0).toFixed(2);
    }
    
    function calculateTotal() {
        let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = parseFloat(discountEl.value) || 0;
        return subtotal - discount;
    }
    
    // Update quantity function
    window.updateQuantity = function(index, change, newValue) {
        if (change === 0) {
            // Direct input
            const newQty = parseInt(newValue);
            if (newQty > 0) {
                cart[index].quantity = newQty;
            }
        } else {
            // Increment or decrement
            cart[index].quantity += change;
            
            // Ensure quantity doesn't go below 1
            if (cart[index].quantity < 1) {
                cart[index].quantity = 1;
            }
        }
        
        renderCart();
    };
    
    // Remove from cart function
    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        renderCart();
    };
    
    // Search products
    searchProduct.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            if (name.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Initialize cart display
    renderCart();
});
</script>