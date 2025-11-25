<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Customers</h2>
    <a href="?controller=Customer&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Customer
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="">
                    <input type="hidden" name="controller" value="Customer">
                    <input type="hidden" name="action" value="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" placeholder="Search customers..." 
                               value="<?php echo $_GET['keyword'] ?? ''; ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo $customer['id']; ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td><?php echo htmlspecialchars($customer['address']); ?></td>
                                <td>
                                    <a href="?controller=Customer&action=edit&id=<?php echo $customer['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?controller=Customer&action=delete&id=<?php echo $customer['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this customer?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No customers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>