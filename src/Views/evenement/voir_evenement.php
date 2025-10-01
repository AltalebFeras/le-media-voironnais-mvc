<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/banners_logos.css' ?>">
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/carousel.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main style="padding:0;">
    <?php if (!$evenement): ?>
        <div class="custom-alert custom-alert-danger" style="margin: 20px;">
            <p>L'événement demandé n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <!-- Banner Section -->
        <div class="evenement-banner-section">
            <div class="evenement-banner-wrapper">
                <?php if ($evenement['bannerPath']): ?>
                    <img id="currentBanner" src="<?= $evenement['bannerPath'] ?>" alt="Bannière de <?= $evenement['title'] ?>" class="evenement-banner-img">
                <?php else: ?>
                    <div id="currentBanner" class="evenement-banner-placeholder">Aucune bannière</div>
                <?php endif; ?>
                <img id="bannerPreview" style="display:none;">
            </div>

            <?php if ($isOwner): ?>
                <div class="evenement-banner-actions">
                    <form method="post" action="<?= HOME_URL . 'evenement/modifier' ?>" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="modifier_banner">
                        <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                        <label for="bannerInput" class="btn">
                            Changer bannière
                            <input type="file" id="bannerInput" name="banner" accept="image/*" required>
                        </label>
                        <button type="submit" class="btn" id="bannerSubmitBtn" disabled>Valider</button>
                        <button type="button" id="cancelBannerBtn" class="btn" style="display:none;">Annuler</button>
                    </form>
                    <?php if ($evenement['bannerPath']): ?>
                        <form method="post" action="<?= HOME_URL . 'evenement/modifier' ?>">
                            <input type="hidden" name="action" value="supprimer_banner">
                            <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                            <button type="submit" class="btn bg-danger">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php include_once __DIR__ . '/../includes/messages.php'; ?>

        <!-- Main Content -->
        <div class="evenement-main-content">
            <!-- Header with back button and title -->
            <div class="flex-row justify-content-between align-items-center mb-4">
                <div>
                    <h1 style="margin: 0;"><?= $evenement['title'] ?></h1>
                    <div class="flex-row mt-2">
                        <span class="badge <?= $evenement['isPublic'] ? 'badge-success' : 'badge-secondary' ?> mr-2">
                            <?= $evenement['isPublic'] ? 'Public' : 'Privé' ?>
                        </span>
                    </div>
                </div>
                <div>
                    <a href="<?= HOME_URL . 'mes_evenements' ?>" class="">
                        <span class="material-icons btn" style="color:white;">arrow_back</span>
                    </a>
                </div>
            </div>

            <div class="flex-row" style="gap: 20px;">
                <!-- Main Details Section -->
                <div class="max-width-66">
                    <div class="card mb-4">
                        <div class="p-3">
                            <h4>Description</h4>
                            <div class="card p-3 bg-light">
                                <p><?= nl2br($evenement['description'] ?? 'Aucune description disponible') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Event Images Carousel -->
                    <div class="card mb-4">
                        <div class="p-3">
                            <div class="flex-row justify-content-between align-items-center mb-3">
                                <h4>Galerie d'images</h4>
                                <?php if ($isOwner): ?>
                                    <button type="button" class="btn btn-sm" onclick="document.getElementById('addImageModal').style.display='flex'">
                                        Ajouter une image
                                    </button>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($eventImages)): ?>
                                <div class="image-carousel">
                                    <div class="carousel-container">
                                        <div class="carousel-track" id="carouselTrack">
                                            <?php foreach ($eventImages as $index => $image): ?>
                                                <div class="carousel-slide <?= $index === 0 ? 'active' : '' ?>">
                                                    <img src="<?= $image['imagePath'] ?>" alt="<?= $image['altText'] ?>">
                                                    <?php if ($isOwner): ?>
                                                        <div class="image-actions">
                                                            <form method="post" action="<?= HOME_URL . 'evenement/modifier' ?>" style="display:inline;">
                                                                <input type="hidden" name="action" value="supprimer_image">
                                                                <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                                                                <input type="hidden" name="imageId" value="<?= $image['idEventImage'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?')">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (count($eventImages) > 1): ?>
                                            <button class="carousel-btn prev" onclick="previousSlide()">‹</button>
                                            <button class="carousel-btn next" onclick="nextSlide()">›</button>
                                            <div class="carousel-indicators">
                                                <?php foreach ($eventImages as $index => $image): ?>
                                                    <span class="indicator <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index + 1 ?>)"></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Aucune image ajoutée pour cet événement.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="card mb-4">
                        <div class="p-3">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Informations pratiques</h4>
                                    <dl>
                                        <dt>Date de début</dt>
                                        <dd><?= date('d/m/Y H:i', strtotime($evenement['startDate'])) ?></dd>

                                        <dt>Date de fin</dt>
                                        <dd><?= $evenement['endDate'] ? date('d/m/Y H:i', strtotime($evenement['endDate'])) : 'Non spécifiée' ?></dd>

                                        <dt>Lieu</dt>
                                        <dd><?= $evenement['address'] ?></dd>

                                        <dt>Ville</dt>
                                        <dd><?= $ville ? $ville['ville_nom_reel'] . ' (' . $ville['ville_code_postal'] . ')' : 'Non spécifiée' ?></dd>
                                    </dl>
                                </div>

                                <div class="col-md-6">
                                    <h4>Participation</h4>
                                    <dl>
                                        <dt>Participants max</dt>
                                        <dd><?= $evenement['maxParticipants'] ?></dd>

                                        <dt>Participants actuels</dt>
                                        <dd><?= $evenement['currentParticipants'] ?></dd>

                                        <dt>Prix</dt>
                                        <dd><?= $evenement['price'] ? number_format($evenement['price'], 2) . ' ' . $evenement['currency'] : 'Gratuit' ?></dd>

                                        <dt>Date limite d'inscription</dt>
                                        <dd><?= $evenement['registrationDeadline'] ? date('d/m/Y H:i', strtotime($evenement['registrationDeadline'])) : 'Non spécifiée' ?></dd>
                                    </dl>
                                </div>
                            </div>

                            <?php if ($isOwner): ?>
                                <div class="flex-row justify-content-between mt-4">
                                    <a href="<?= HOME_URL . 'evenement/modifier?uiid=' . $evenement['uiid'] ?>" class="btn linkNotDecorated">
                                        Modifier l'événement
                                    </a>
                                    <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('popup').style.display='flex'">
                                        Supprimer l'événement
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Info Sidebar -->
                <div class="max-width-33">
                    <div class="card">
                        <div class="p-3">
                            <h4>Statistiques</h4>
                            <dl>
                                <dt>Créé le</dt>
                                <dd><?= date('d/m/Y', strtotime($evenement['createdAt'])) ?></dd>

                                <dt>Dernière modification</dt>
                                <dd><?= $evenement['updatedAt'] ? date('d/m/Y', strtotime($evenement['updatedAt'])) : 'Jamais' ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <!-- Waiting List    -->
                <div class="max-width-33">
                    <?php if ($evenement['requiresApproval'] == true): ?>
                        <div class="card mt-4">
                            <div class="p-3">
                                <h4>Liste d'attente</h4>
                                <?php if (!empty($waitingList)): ?>
                                    <ul class="list-group">
                                        <?php foreach ($waitingList as $participant): ?>
                                            <hr>
                                            <li class="list-group-item d-flex justify-content-between align-items-center m">
                                                <div>
                                                    <strong><?= htmlspecialchars($participant['firstName'] . ' ' . $participant['lastName']) ?></strong><br>
                                                    <small class="text-muted">Inscrit le <?= date('d/m/Y', strtotime($participant['joinedAt'])) ?></small>
                                                </div>
                                                <?php if ($isOwner): ?>
                                                    <div class="d-flex ml">
                                                        <form method="post" action="<?= HOME_URL . 'mes_evenement/participants' ?>" class="mr">
                                                            <input type="hidden" name="action" value="accepter">
                                                            <input type="hidden" name="idEventParticipant" value="<?= $participant['idEventParticipant'] ?>">
                                                            <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-success">Accepter</button>
                                                        </form>
                                                        <form method="post" action="<?= HOME_URL . 'mes_evenement/participants' ?>" class="mr">
                                                            <input type="hidden" name="action" value="refuser">
                                                            <input type="hidden" name="idEventParticipant" value="<?= $participant['idEventParticipant'] ?>">
                                                            <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">Refuser</button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </li>
                                            <hr>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>Aucun participant dans la liste d'attente.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Participants List -->
                <div class="max-width-33">
                    <div class="card mt-4">
                        <div class="p-3">
                            <h4>Participants</h4>
                            <?php if (!empty($participants)): ?>
                                <ul class="list-group">
                                    <?php foreach ($participants as $participant): ?>
                                        <li class="list-group-item d-flex align-items-center">
                                            <div>
                                                <strong><?= htmlspecialchars($participant['firstName'] . ' ' . $participant['lastName']) ?></strong><br>
                                                <small class="text-muted">Inscrit le <?= date('d/m/Y', strtotime($participant['joinedAt'])) ?></small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Aucun participant dans la liste d'attente.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Add Image Modal -->
                <?php if ($isOwner): ?>
                    <div id="addImageModal" class="d-none popup">
                        <div class="card" style="max-width:500px;">
                            <h3>Ajouter une image</h3>
                            <button type="button" onclick="document.getElementById('addImageModal').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                            <form method="post" action="<?= HOME_URL . 'evenement/modifier' ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="ajouter_image">
                                <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                                <div class="mt mb">
                                    <label for="eventImage">Sélectionner une image :</label>
                                    <input type="file" id="eventImage" name="eventImage" accept="image/*" required>

                                    <label for="altText" class="mt">Texte alternatif (optionnel) :</label>
                                    <input type="text" id="altText" name="altText" placeholder="Description de l'image">
                                </div>
                                <div class="flex-row justify-content-between">
                                    <button type="button" class="btn" onclick="document.getElementById('addImageModal').style.display='none'">Annuler</button>
                                    <button type="submit" class="btn">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Social sharing include -->
                    <div class=" bg-info social-sharing d-flex justify-content-center align-items-center text-center pb">
                        <?php include __DIR__ . '/../includes/social_share_btns.php'; ?>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div id="popup" class="d-none popup">
                        <div class="card">
                            <h3>Confirmer la suppression</h3>
                            <button type="button" onclick="document.getElementById('popup').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                            <div class="mt mb">
                                <p>Êtes-vous sûr de vouloir supprimer l'événement "<?= $evenement['title'] ?>" ?</p>
                                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                            </div>
                            <div class="flex-row justify-content-between">
                                <button type="button" class="btn" onclick="document.getElementById('popup').style.display='none'">Annuler</button>
                                <form action="<?= HOME_URL . 'evenement/supprimer' ?>" method="post">
                                    <input type="hidden" name="uiid" value="<?= $evenement['uiid'] ?>">
                                    <button type="submit" class="btn deconnexion">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
</main>

<script src="<?= HOME_URL . 'assets/javascript/banner-logo-management.js' ?>"></script>
<script src="<?= HOME_URL . 'assets/javascript/carousel.js' ?>"></script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>