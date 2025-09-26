<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Ajouter un film</h1>
            <p class="auth-subtitle">Ajoutez un nouveau film à la médiathèque</p>
        </div>
        
        <form method="POST" action="/add-movie" class="auth-form">
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
                <label for="auteur" class="form-label">Réalisateur *</label>
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
                <label class="form-label">Durée *</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input 
                        type="number" 
                        name="duration_hours" 
                        class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                        value="<?= htmlspecialchars($oldInput['duration_hours'] ?? '') ?>"
                        min="0"
                        max="23"
                        placeholder="Heures"
                        style="flex: 1;"
                    >
                    <span>h</span>
                    <input 
                        type="number" 
                        name="duration_minutes" 
                        class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                        value="<?= htmlspecialchars($oldInput['duration_minutes'] ?? '') ?>"
                        min="0"
                        max="59"
                        placeholder="Minutes"
                        style="flex: 1;"
                    >
                    <span>min</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="genre" class="form-label">Genre *</label>
                <select 
                    id="genre" 
                    name="genre" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    required
                >
                    <option value="">Sélectionner un genre</option>
                    <?php foreach ($genres as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= ($oldInput['genre'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Ajouter le film
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                <a href="/movies" class="auth-link">← Retour aux films</a>
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