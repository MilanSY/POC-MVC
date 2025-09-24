<div class="section">
    <h2 class="section-title">Collection d'Albums</h2>
    
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
                    
                    <h3 class="media-title"><?= htmlspecialchars($album['titre']) ?></h3>
                    <p class="media-author">par <?= htmlspecialchars($album['auteur']) ?></p>
                    
                    <div class="media-details">
                        <div class="detail-item">
                            <span class="detail-label">Pistes</span>
                            <span class="detail-value"><?= $album['track_number'] ?? 'N/A' ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Label</span>
                            <span class="detail-value"><?= htmlspecialchars($album['editor'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📊 Statut</span>
                            <span class="detail-value">
                                <?php if ($album['disponible']): ?>
                                    ✅ Disponible
                                <?php else: ?>
                                    📤 Emprunté par <strong><?= htmlspecialchars($album['borrowed_by_username'] ?? 'Inconnu') ?></strong>
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
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <?php if ($album['disponible']): ?>
                                <button class="btn btn-primary" data-action="borrow" data-media-id="<?= $album['id'] ?>">
                                    Emprunter
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary" data-action="return" data-media-id="<?= $album['id'] ?>">
                                    Rendre
                                </button>
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