<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Connexion</h1>
            <p class="auth-subtitle">Accédez à votre compte</p>
        </div>
        

        <form method="POST" action="/login" class="auth-form">
            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($oldInput['email'] ?? '') ?>"
                    required
                    autocomplete="email"
                >
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                    required
                    autocomplete="current-password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Se connecter
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                Pas encore de compte ? 
                <a href="/register" class="auth-link">Créer un compte</a>
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