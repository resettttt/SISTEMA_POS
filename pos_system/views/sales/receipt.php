<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-receipt me-2"></i>Sale Receipt</h2>
    <div>
        <button class="btn btn-secondary me-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Print
        </button>
        <a href="?controller=Sales&action=pos" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>New Sale
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Business Information</h5>
                <p>
                    <strong>POS System</strong><br>
                    123 Business Street<br>
                    City, State 12345<br>
                    Phone: (123) 456-7890
                </p>
            </div>
            <div class="col-md-6 text-end">
                <h5>Receipt #<?php echo $sale['id']; ?></h5>
                <p>
                    Date: <?php echo date('Y-m-d H:i:s', strtotime($sale['created_at'])); ?><br>
                    Cashier: Admin
                </p>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <p>
                    <?php if ($sale['customer_name']): ?>
                        <strong><?php echo htmlspecialchars($sale['customer_name']); ?></strong><br>
                    <?php else: ?>
                        <strong>Walk-in Customer</strong><br>
                    <?php endif; ?>
                    <!-- Add customer details if needed -->
                </p>
            </div>
        </div>
        
        <div class="table-responsive mt-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <table class="table">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-end">$<?php echo number_format($sale['total_amount'] + $sale['discount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Discount:</strong></td>
                        <td class="text-end">-$<?php echo number_format($sale['discount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td class="text-end">$<?php echo number_format($sale['total_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Method:</strong></td>
                        <td class="text-end"><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p>Thank you for your business!</p>
            <p>For inquiries, contact us at (123) 456-7890</p>
        </div>
    </div>
</div>

<style>
@media print {
    .btn {
        display: none;
    }
    .card {
        border: none;
        box-shadow: none;
    }
}
</style>