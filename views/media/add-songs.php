<div class="auth-container" style="max-width: 800px;">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Ajouter les chansons</h1>
            <p class="auth-subtitle">
                Album : <strong><?= htmlspecialchars($albumData['titre']) ?></strong> 
                par <?= htmlspecialchars($albumData['auteur']) ?>
            </p>
            <p class="auth-subtitle">
                Ajoutez les <?= $albumData['nb_tracks'] ?> piste<?= $albumData['nb_tracks'] > 1 ? 's' : '' ?>
            </p>
        </div>
        
        <form method="POST" action="/add-songs" class="auth-form">
            <?php for ($i = 1; $i <= $albumData['nb_tracks']; $i++): ?>
                <div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #f9f9f9;">
                    <h3 style="margin-top: 0; color: #333;">Piste <?= $i ?></h3>
                    
                    <div class="form-group">
                        <label for="song_title_<?= $i ?>" class="form-label">Titre *</label>
                        <input 
                            type="text" 
                            id="song_title_<?= $i ?>" 
                            name="song_title_<?= $i ?>" 
                            class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                            value="<?= htmlspecialchars($_POST["song_title_$i"] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="song_note_<?= $i ?>" class="form-label">Note (1-5) *</label>
                        <select 
                            id="song_note_<?= $i ?>" 
                            name="song_note_<?= $i ?>" 
                            class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                            required
                        >
                            <option value="">Choisir une note</option>
                            <option value="1" <?= ($_POST["song_note_$i"] ?? '') == '1' ? 'selected' : '' ?>>1 étoile</option>
                            <option value="2" <?= ($_POST["song_note_$i"] ?? '') == '2' ? 'selected' : '' ?>>2 étoiles</option>
                            <option value="3" <?= ($_POST["song_note_$i"] ?? '') == '3' ? 'selected' : '' ?>>3 étoiles</option>
                            <option value="4" <?= ($_POST["song_note_$i"] ?? '') == '4' ? 'selected' : '' ?>>4 étoiles</option>
                            <option value="5" <?= ($_POST["song_note_$i"] ?? '') == '5' ? 'selected' : '' ?>>5 étoiles</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Durée *</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input 
                                type="number" 
                                name="song_duration_minutes_<?= $i ?>" 
                                class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                                value="<?= htmlspecialchars($_POST["song_duration_minutes_$i"] ?? '') ?>"
                                min="0"
                                max="59"
                                placeholder="Minutes"
                                style="flex: 1;"
                            >
                            <span>:</span>
                            <input 
                                type="number" 
                                name="song_duration_seconds_<?= $i ?>" 
                                class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                                value="<?= htmlspecialchars($_POST["song_duration_seconds_$i"] ?? '') ?>"
                                min="0"
                                max="59"
                                placeholder="Secondes"
                                style="flex: 1;"
                            >
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
            
            <button type="submit" class="btn btn-primary btn-full">
                Ajouter l'album et toutes les chansons
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                <a href="/add-album" class="auth-link">← Retour aux informations de l'album</a>
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