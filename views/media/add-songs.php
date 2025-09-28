<div class="auth-container songs-form">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title"><?= isset($songsData) ? 'Modifier les chansons' : 'Ajouter les chansons' ?></h1>
            <p class="auth-subtitle">
                Album : <strong><?= htmlspecialchars($albumData['titre']) ?></strong> 
                par <?= htmlspecialchars($albumData['auteur']) ?>
            </p>
            <p class="auth-subtitle">
                <?= isset($songsData) ? 'Modifiez' : 'Ajoutez' ?> les <?= $albumData['nb_tracks'] ?> piste<?= $albumData['nb_tracks'] > 1 ? 's' : '' ?>
            </p>
        </div>
        
        <form method="POST" action="<?= isset($songsData) ? $_SERVER['REQUEST_URI'] : '/add-songs' ?>" class="auth-form">
            <?php for ($i = 1; $i <= $albumData['nb_tracks']; $i++): 
                $songData = isset($songsData) && isset($songsData[$i-1]) ? $songsData[$i-1] : null;
            ?>
                <div class="song-form-item">
                    <h3 class="song-form-title">Piste <?= $i ?></h3>
                    
                    <div class="form-group">
                        <label for="song_title_<?= $i ?>" class="form-label">Titre *</label>
                        <input 
                            type="text" 
                            id="song_title_<?= $i ?>" 
                            name="song_title_<?= $i ?>" 
                            class="form-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                            value="<?= htmlspecialchars($songData ? $songData['title'] : ($_POST["song_title_$i"] ?? '')) ?>"
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
                            <option value="1" <?= ($songData ? $songData['note'] : ($_POST["song_note_$i"] ?? '')) == '1' ? 'selected' : '' ?>>1 étoile</option>
                            <option value="2" <?= ($songData ? $songData['note'] : ($_POST["song_note_$i"] ?? '')) == '2' ? 'selected' : '' ?>>2 étoiles</option>
                            <option value="3" <?= ($songData ? $songData['note'] : ($_POST["song_note_$i"] ?? '')) == '3' ? 'selected' : '' ?>>3 étoiles</option>
                            <option value="4" <?= ($songData ? $songData['note'] : ($_POST["song_note_$i"] ?? '')) == '4' ? 'selected' : '' ?>>4 étoiles</option>
                            <option value="5" <?= ($songData ? $songData['note'] : ($_POST["song_note_$i"] ?? '')) == '5' ? 'selected' : '' ?>>5 étoiles</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Durée *</label>
                        <div class="duration-input-group">
                            <input 
                                type="number" 
                                name="song_duration_minutes_<?= $i ?>" 
                                class="form-input duration-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                                value="<?= htmlspecialchars($songData ? $songData['duration_minutes'] : ($_POST["song_duration_minutes_$i"] ?? '')) ?>"
                                min="0"
                                max="59"
                                placeholder="Minutes"
                            >
                            <span class="duration-separator">:</span>
                            <input 
                                type="number" 
                                name="song_duration_seconds_<?= $i ?>" 
                                class="form-input duration-input <?= isset($errors) && !empty($errors) ? 'error' : '' ?>"
                                value="<?= htmlspecialchars($songData ? $songData['duration_seconds'] : ($_POST["song_duration_seconds_$i"] ?? '')) ?>"
                                min="0"
                                max="59"
                                placeholder="Secondes"
                            >
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
            
            <button type="submit" class="btn btn-primary btn-full">
                <?= isset($songsData) ? 'Modifier les chansons' : 'Ajouter l\'album et toutes les chansons' ?>
            </button>
        </form>
        
        <div class="auth-footer">
            <p class="auth-link-text">
                <?php if (isset($songsData)): ?>
                    <a href="<?= '/album-details/' . explode('/', $_SERVER['REQUEST_URI'])[2] ?>" class="auth-link">← Retour aux détails de l'album</a>
                <?php else: ?>
                    <a href="/add-album" class="auth-link">← Retour aux informations de l'album</a>
                <?php endif; ?>
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