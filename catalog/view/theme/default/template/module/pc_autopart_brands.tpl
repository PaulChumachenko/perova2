<?php if ($model) : ?>
    <div class="brand-container">
        <div class="row">
            <div class="col-sm-12">
                <?php if ($model['logo']) : ?>
                    <div class="pc-logo">
                        <img src="<?= $model['thumb'] ?>" />
                    </div>
                <?php endif; ?>
                <?php if ($model['website']) : ?>
                    <div class="pc-external-link">
                        <i class="fa fa-external-link"></i>
                        <a href="<?= $model['website'] ?>">Официальный сайт</a>
                    </div>
                <?php endif; ?>
                <?php if ($model['description']) : ?>
                    <div class="pc-description">
                        <?= html_entity_decode($model['description']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>