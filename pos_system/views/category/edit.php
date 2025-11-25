<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit me-2"></i>Edit Category</h2>
    <a href="?controller=Category&action=index" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="?controller=Category&action=edit&id=<?php echo $category['id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($category['name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php 
                    echo htmlspecialchars($category['description'] ?? ''); 
                ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update Category
            </button>
        </form>
    </div>
</div>