<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice-dollar me-2"></i>Sales</h2>
    <a href="?controller=Sales&action=pos" class="btn btn-primary">
        <i class="fas fa-cash-register me-1"></i>New Sale
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo $sale['id']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($sale['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?></td>
                                <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                                <td>
                                    <a href="?controller=Sales&action=receipt&id=<?php echo $sale['id']; ?>" 
                                       class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No sales found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>