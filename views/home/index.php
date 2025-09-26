<div class="hero-section">
    <h2 class="hero-title">Bienvenue dans votre médiathèque</h2>
    <p class="hero-subtitle">
        Explorez notre collection riche et variée de livres, albums et films. 
        Découvrez de nouveaux trésors et gérez facilement vos emprunts.
    </p>
</div>


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
                    
                    <h3 class="media-title"><?= htmlspecialchars($media['titre']) ?></h3>
                    <p class="media-author">par <?= htmlspecialchars($media['auteur']) ?></p>
                    
                    <div class="media-details">
                        <?php if ($media['type_media'] === 'book'): ?>
                            <div class="detail-item">
                                <span class="detail-label">Pages</span>
                                <span class="detail-value"><?= $media['page_number'] ?? 'N/A' ?></span>
                            </div>
                        <?php elseif ($media['type_media'] === 'album'): ?>
                            <div class="detail-item">
                                <span class="detail-label">Pistes</span>
                                <span class="detail-value"><?= $media['track_number'] ?? 'N/A' ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Label</span>
                                <span class="detail-value"><?= htmlspecialchars($media['editor'] ?? 'N/A') ?></span>
                            </div>
                        <?php elseif ($media['type_media'] === 'movie'): ?>
                            <div class="detail-item">
                                <span class="detail-label">Durée</span>
                                <span class="detail-value"><?= $media['duration'] ?? 'N/A' ?> min</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Genre</span>
                                <span class="detail-value"><?= $media['genre'] ?? 'N/A' ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Statut d'emprunt -->
                        <div class="detail-item">
                            <span class="detail-label">Statut</span>
                            <span class="detail-value">
                                <?php if ($media['disponible']): ?>
                                    ✅ Disponible
                                <?php else: ?>
                                    📤 Emprunté par <strong><?= htmlspecialchars($media['borrowed_by_username'] ?? 'Inconnu') ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="media-actions">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <?php if ($media['disponible']): ?>
                                <form method="POST" action="/home" style="display: inline;">
                                    <input type="hidden" name="action" value="borrow">
                                    <input type="hidden" name="media_id" value="<?= $media['id'] ?>">
                                    <button type="submit" class="btn btn-primary">Emprunter</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/home" style="display: inline;">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="media_id" value="<?= $media['id'] ?>">
                                    <button type="submit" class="btn btn-secondary">Rendre</button>
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