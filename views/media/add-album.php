<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Ajouter un album</h1>
            <p class="auth-subtitle">Ajoutez un nouvel album à la médiathèque</p>
        </div>
        
        <form method="POST" action="/add-album" class="auth-form">
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
                <label for="auteur" class="form-label">Artiste *</label>
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
                <label for="editeur" class="form-label">Éditeur *</label>
                <input 
                    type="text" 
                    id="editeur" 
                    name="editeur" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($oldInput['editeur'] ?? '') ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="nb_tracks" class="form-label">Nombre de pistes *</label>
                <input 
                    type="number" 
                    id="nb_tracks" 
                    name="nb_tracks" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($oldInput['nb_tracks'] ?? '') ?>"
                    min="1"
                    max="50"
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Continuer vers les pistes
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                <a href="/albums" class="auth-link">← Retour aux albums</a>
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