<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Inscription</h1>
            <p class="auth-subtitle">Créez votre compte</p>
        </div>
             
        <form method="POST" action="/register" class="auth-form">
            <div class="form-group">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-input"
                    value="<?= htmlspecialchars($oldInput['username'] ?? '') ?>"
                    required
                    autocomplete="username"
                    pattern="[a-zA-Z0-9_-]+"
                    title="Lettres, chiffres, tirets et underscores uniquement"
                >
                <small class="form-help">Minimum 3 caractères, lettres, chiffres, - et _ uniquement</small>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input"
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
                    class="form-input"
                    required
                    autocomplete="new-password"
                    minlength="8"
                >
                <div class="password-strength" id="password-strength"></div>
            </div>
            
            <div class="password-rules">
                <p>Le mot de passe doit contenir au minimum 8 caractères, au moins une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="form-input"
                    required
                    autocomplete="new-password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Créer mon compte
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                Déjà un compte ? 
                <a href="/login" class="auth-link">Se connecter</a>
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

