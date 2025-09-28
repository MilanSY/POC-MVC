<div class="section">
    <div class="section-header">
        <h2 class="section-title">Collection d'Albums</h2>
        <a href="/add-album" class="btn btn-primary">Ajouter un album</a>
    </div>
    
    <?php if (!empty($albums)): ?>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($albums) ?></div>
                <div class="stat-label">Albums Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($albums, fn($album) => $album['disponible'] == 1)) ?></div>
                <div class="stat-label">Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($albums, fn($album) => $album['disponible'] == 0)) ?></div>
                <div class="stat-label">Empruntés</div>
            </div>
        </div>

        <div class="media-grid">
            <?php foreach ($albums as $index => $album): ?>
                <div class="media-card album">
                    <div class="media-header">
                        <span class="media-type album">Album</span>
                        <span class="availability <?= $album['disponible'] ? 'available' : 'unavailable' ?>">
                            <?= $album['disponible'] ? 'Disponible' : 'Emprunté' ?>
                        </span>
                    </div>
                    
                    <h3 class="media-title">
                        <a href="/album-details/<?= $album['id'] ?>" class="media-title-link">
                            <?= htmlspecialchars($album['titre']) ?>
                        </a>
                    </h3>
                    <p class="media-author">par <?= htmlspecialchars($album['auteur']) ?></p>
                    
                    <div class="media-details">
                        <div class="detail-item">
                            <span class="detail-label">Statut</span>
                            <span class="detail-value">
                                <?php if ($album['disponible']): ?>
                                    Disponible
                                <?php else: ?>
                                    Emprunté par <strong><?= htmlspecialchars($album['borrowed_by_username'] ?? 'Inconnu') ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <?php 
                    $songs = $repository->getAlbumSongs($album['id']);
                    if (!empty($songs)): ?>
                        <div class="album-songs">
                            <h4>🎶 Liste des pistes</h4>
                            <div class="song-list">
                                <?php foreach ($songs as $i => $song): ?>
                                    <div class="song-item">
                                        <span><?= ($i + 1) . '. ' . htmlspecialchars($song['titre']) ?></span>
                                        <small><?= $song['duration'] ?>s</small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="media-actions">
                        <a href="/album-details/<?= $album['id'] ?>" class="btn btn-outline btn-small">Détails</a>
                        
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <?php if ($album['disponible']): ?>
                                <form method="POST" action="/albums" style="display: inline;">
                                    <input type="hidden" name="action" value="borrow">
                                    <input type="hidden" name="media_id" value="<?= $album['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-small">Emprunter</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/albums" style="display: inline;">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="media_id" value="<?= $album['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-small">Rendre</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="auth-required"><a href="/login">Connectez-vous</a> pour emprunter</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon"></div>
            <h3>Aucun album disponible</h3>
            <p>La collection d'albums est actuellement vide.</p>
            <a href="index.php?page=home" class="btn btn-primary">← Retour à l'accueil</a>
        </div>
    <?php endif; ?>
</div>