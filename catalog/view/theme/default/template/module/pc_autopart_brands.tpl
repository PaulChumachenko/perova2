<?php if ($model) : ?>
    <div class="brand-container">
        <?= $model['brand'] ?>
        <div class="row">
            <div class="col-sm-2">
                <?php if ($model['website']) : ?>
                    <div><a href="<?= $model['website'] ?>">Официальный сайт</a></div>
                <?php endif; ?>
                <?php if ($model['logo']) : ?>
                    <div><img src="<?= $model['thumb'] ?>" /></div>
                <?php endif; ?>
            </div>
            <div class="col-sm-10">
                <?php if ($model['description']) : ?>
                    <?= html_entity_decode($model['description']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>