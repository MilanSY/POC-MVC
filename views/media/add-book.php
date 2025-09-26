<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Ajouter un livre</h1>
            <p class="auth-subtitle">Ajoutez un nouveau livre à la médiathèque</p>
        </div>
        
        <form method="POST" action="/add-book" class="auth-form">
            <div class="form-group">
                <label for="titre" class="form-label">Titre *</label>
                <input 
                    type="text" 
                    id="titre" 
                    name="titre" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($oldInput['titre'] ?? '') ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="auteur" class="form-label">Auteur *</label>
                <input 
                    type="text" 
                    id="auteur" 
                    name="auteur" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($oldInput['auteur'] ?? '') ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="page_number" class="form-label">Nombre de pages *</label>
                <input 
                    type="number" 
                    id="page_number" 
                    name="page_number" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($oldInput['page_number'] ?? '') ?>"
                    min="1"
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Ajouter le livre
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                <a href="/books" class="auth-link">← Retour aux livres</a>
            </p>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error alert-fixed">
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>