<div class="media-details-container">
    <div class="media-details-header">
        <a href="/albums" class="back-button">← Retour aux albums</a>
        <div class="media-type-badge album">
            🎵 Album
        </div>
    </div>

    <div class="media-details-content">
        <div class="media-main-info">
            <h1 class="media-title"><?= htmlspecialchars($album['titre']) ?></h1>
            <p class="media-author">par <?= htmlspecialchars($album['auteur']) ?></p>
            
            <div class="availability-status <?= $album['disponible'] ? 'available' : 'unavailable' ?>">
                <?php if ($album['disponible']): ?>
                    <span class="status-icon">✅</span>
                    <span class="status-text">Disponible</span>
                <?php else: ?>
                    <span class="status-icon">📤</span>
                    <span class="status-text">Emprunté par <strong><?= htmlspecialchars($album['borrowed_by_username'] ?? 'Inconnu') ?></strong></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="media-specific-details">
            <div class="detail-card">
                <h3 class="detail-card-title">Informations de l'album</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nombre de pistes</span>
                        <span class="detail-value"><?= $album['track_number'] ?? 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Label / Éditeur</span>
                        <span class="detail-value"><?= htmlspecialchars($album['editor'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value">Album</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($album['songs'])): ?>
                <div class="songs-section">
                    <h3 class="detail-card-title">Liste des pistes</h3>
                    <div class="songs-list">
                        <?php foreach ($album['songs'] as $index => $song): ?>
                            <div class="song-item" data-song-id="<?= $song['id'] ?>">
                                <div class="song-info">
                                    <span class="song-number"><?= $index + 1 ?></span>
                                    <div class="song-details">
                                        <span class="song-title" data-field="title"><?= htmlspecialchars($song['title']) ?></span>
                                        <div class="song-meta">
                                            <span class="song-duration" data-field="duration"><?= htmlspecialchars($song['duration']) ?></span>
                                            <div class="song-rating" data-field="note">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="star <?= $i <= $song['note'] ? 'filled' : '' ?>">★</span>
                                                <?php endfor; ?>
                                                <span class="rating-text">(<?= $song['note'] ?>/5)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions de l'album -->
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <div class="media-actions-section">
                <div class="primary-actions">
                    <?php if ($album['disponible']): ?>
                        <form method="POST" action="/album-details/<?= $album['id'] ?>" style="display: inline;">
                            <input type="hidden" name="action" value="borrow">
                            <input type="hidden" name="media_id" value="<?= $album['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-large">📥 Emprunter</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/album-details/<?= $album['id'] ?>" style="display: inline;">
                            <input type="hidden" name="action" value="return">
                            <input type="hidden" name="media_id" value="<?= $album['id'] ?>">
                            <button type="submit" class="btn btn-secondary btn-large">📤 Rendre</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="secondary-actions">
                    <a href="/edit-album/<?= $album['id'] ?>" class="btn btn-outline">✏️ Modifier</a>
                    <form method="POST" action="/album-details/<?= $album['id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet album ? Cette action est irréversible.')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="media_id" value="<?= $album['id'] ?>">
                        <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="auth-required-message">
                <p><a href="/login" class="btn btn-primary">Connectez-vous</a> pour emprunter, modifier ou supprimer cet album</p>
            </div>
        <?php endif; ?>
    </div>
</div>