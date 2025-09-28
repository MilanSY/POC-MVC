<div class="section">
    <div class="section-header">
        <h2 class="section-title">Collection de Films</h2>
        <a href="/add-movie" class="btn btn-primary">➕ Ajouter un film</a>
    </div>
    
    <?php if (!empty($movies)): ?>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($movies) ?></div>
                <div class="stat-label">Films Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($movies, fn($movie) => $movie['disponible'] == 1)) ?></div>
                <div class="stat-label">Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($movies, fn($movie) => $movie['disponible'] == 0)) ?></div>
                <div class="stat-label">Empruntés</div>
            </div>
        </div>

        <div class="media-grid">
            <?php foreach ($movies as $index => $movie): ?>
                <div class="media-card movie">
                    <div class="media-header">
                        <span class="media-type movie">Film</span>
                        <span class="availability <?= $movie['disponible'] ? 'available' : 'unavailable' ?>">
                            <?= $movie['disponible'] ? 'Disponible' : 'Emprunté' ?>
                        </span>
                    </div>
                    
                    <h3 class="media-title">
                        <a href="/movie-details/<?= $movie['id'] ?>" class="media-title-link">
                            <?= htmlspecialchars($movie['titre']) ?>
                        </a>
                    </h3>
                    <p class="media-author">réalisé par <?= htmlspecialchars($movie['auteur']) ?></p>
                    
                    <div class="media-details">
                        <div class="detail-item">
                            <span class="detail-label">Durée</span>
                            <span class="detail-value"><?= $movie['duration'] ?? 'N/A' ?> min</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Genre</span>
                            <span class="detail-value"><?= $movie['genre'] ?? 'N/A' ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Statut</span>
                            <span class="detail-value">
                                <?php if ($movie['disponible']): ?>
                                    Disponible
                                <?php else: ?>
                                    Emprunté par <strong><?= htmlspecialchars($movie['borrowed_by_username'] ?? 'Inconnu') ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="media-actions">
                        <a href="/movie-details/<?= $movie['id'] ?>" class="btn btn-outline btn-small">👁️ Détails</a>
                        
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <?php if ($movie['disponible']): ?>
                                <form method="POST" action="/movies" style="display: inline;">
                                    <input type="hidden" name="action" value="borrow">
                                    <input type="hidden" name="media_id" value="<?= $movie['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-small">Emprunter</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/movies" style="display: inline;">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="media_id" value="<?= $movie['id'] ?>">
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
            <h3>Aucun film disponible</h3>
            <p>La collection de films est actuellement vide.</p>
            <a href="index.php?page=home" class="btn btn-primary">← Retour à l'accueil</a>
        </div>
    <?php endif; ?>
</div>