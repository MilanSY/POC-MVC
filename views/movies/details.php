<div class="media-details-container">
    <div class="media-details-header">
        <a href="/movies" class="back-button">← Retour aux films</a>
        <div class="media-type-badge movie">
            🎬 Film
        </div>
    </div>

    <div class="media-details-content">
        <div class="media-main-info">
            <h1 class="media-title"><?= htmlspecialchars($movie['titre']) ?></h1>
            <p class="media-author">par <?= htmlspecialchars($movie['auteur']) ?></p>
            
            <div class="availability-status <?= $movie['disponible'] ? 'available' : 'unavailable' ?>">
                <?php if ($movie['disponible']): ?>
                    <span class="status-icon">✅</span>
                    <span class="status-text">Disponible</span>
                <?php else: ?>
                    <span class="status-icon">📤</span>
                    <span class="status-text">Emprunté par <strong><?= htmlspecialchars($movie['borrowed_by_username'] ?? 'Inconnu') ?></strong></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="media-specific-details">
            <div class="detail-card">
                <h3 class="detail-card-title">Informations du film</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Durée</span>
                        <span class="detail-value"><?= htmlspecialchars($movie['movie_duration'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Genre</span>
                        <span class="detail-value"><?= htmlspecialchars($movie['genre'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value">Film</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions du film -->
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <div class="media-actions-section">
                <div class="primary-actions">
                    <?php if ($movie['disponible']): ?>
                        <form method="POST" action="/movie-details/<?= $movie['id'] ?>" style="display: inline;">
                            <input type="hidden" name="action" value="borrow">
                            <input type="hidden" name="media_id" value="<?= $movie['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-large">📥 Emprunter</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/movie-details/<?= $movie['id'] ?>" style="display: inline;">
                            <input type="hidden" name="action" value="return">
                            <input type="hidden" name="media_id" value="<?= $movie['id'] ?>">
                            <button type="submit" class="btn btn-secondary btn-large">📤 Rendre</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="secondary-actions">
                    <a href="/edit-movie/<?= $movie['id'] ?>" class="btn btn-outline">✏️ Modifier</a>
                    <form method="POST" action="/movie-details/<?= $movie['id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce film ? Cette action est irréversible.')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="media_id" value="<?= $movie['id'] ?>">
                        <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="auth-required-message">
                <p><a href="/login" class="btn btn-primary">Connectez-vous</a> pour emprunter, modifier ou supprimer ce film</p>
            </div>
        <?php endif; ?>
    </div>
</div>