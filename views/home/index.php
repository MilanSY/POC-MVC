
<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
<section class="section">
    <div class="section-header">
        <h3 class="section-title">Ajouter un media</h3>
    </div>
    <div class="admin-actions">
        <a href="/add-book" class="btn btn-secondary">Ajouter un livre</a>
        <a href="/add-movie" class="btn btn-secondary">Ajouter un film</a>
        <a href="/add-album" class="btn btn-secondary">Ajouter un album</a>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?= $totalCount ?></div>
            <div class="stat-label">Médias Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $albumsCount ?></div>
            <div class="stat-label">Albums</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $booksCount ?></div>
            <div class="stat-label">Livres</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $albumsCount + $moviesCount ?></div>
            <div class="stat-label">Films</div>
        </div>
    </div>
</section>

<?php if (!empty($allMedias)): ?>
    <section class="featured-section">
        <div class="featured-title">
            <h2 class="section-title">Tous nos médias</h2>
        </div>
        
        <div class="media-grid">
            <?php foreach ($allMedias as $media): ?>
                <div class="media-card <?= $media['type_media'] ?>">
                    <div class="media-header">
                        <span class="media-type <?= $media['type_media'] ?>">
                            <?php
                            switch($media['type_media']) {
                                case 'book': echo 'Livre'; break;
                                case 'album': echo 'Album'; break;
                                case 'movie': echo 'Film'; break;
                                default: break;
                            }
                            ?>
                        </span>
                        <span class="availability <?= $media['disponible'] ? 'available' : 'unavailable' ?>">
                            <?= $media['disponible'] ? 'Disponible' : 'Emprunté' ?>
                        </span>
                    </div>
                    
                    <h3 class="media-title">
                        <a href="/<?= $media['type_media'] ?>-details/<?= $media['id'] ?>" class="media-title-link">
                            <?= htmlspecialchars($media['titre']) ?>
                        </a>
                    </h3>
                    <p class="media-author">par <?= htmlspecialchars($media['auteur']) ?></p>
                                        
                    <div class="detail-item">
                        <span class="detail-label">Statut</span>
                        <span class="detail-value">
                            <?php if ($media['disponible']): ?>
                                Disponible
                            <?php else: ?>
                                Emprunté par <strong><?= htmlspecialchars($media['borrowed_by_username'] ?? 'Inconnu') ?></strong>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="media-actions">
                        <a href="/<?= $media['type_media'] ?>-details/<?= $media['id'] ?>" class="btn btn-outline btn-small">Détails</a>
                        
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <?php if ($media['disponible']): ?>
                                <form method="POST" action="/home" style="display: inline;">
                                    <input type="hidden" name="action" value="borrow">
                                    <input type="hidden" name="media_id" value="<?= $media['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-small">Emprunter</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/home" style="display: inline;">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="media_id" value="<?= $media['id'] ?>">
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
    </section>
<?php endif; ?>


<?php if (empty($allMedias)): ?>
    <div class="empty-state">
        <div class="empty-state-icon"></div>
        <h3>Aucun média disponible</h3>
        <p>La médiathèque est actuellement vide. Ajoutez des médias pour commencer !</p>
    </div>
<?php endif; ?>