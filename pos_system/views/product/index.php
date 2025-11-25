<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-box-open me-2"></i>Products</h2>
    <a href="?controller=Product&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Product
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="">
                    <input type="hidden" name="controller" value="Product">
                    <input type="hidden" name="action" value="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" placeholder="Search products..." 
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
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $product['stock'] > 10 ? 'bg-success' : ($product['stock'] > 0 ? 'bg-warning' : 'bg-danger'); ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $category = null;
                                    foreach ($categories as $cat) {
                                        if ($cat['id'] == $product['category_id']) {
                                            $category = $cat;
                                            break;
                                        }
                                    }
                                    echo $category ? htmlspecialchars($category['name']) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <a href="?controller=Product&action=edit&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?controller=Product&action=delete&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>