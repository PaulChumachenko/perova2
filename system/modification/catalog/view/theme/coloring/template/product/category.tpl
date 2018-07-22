<?= $header ?>

<div class="container">
    <ul class="breadcrumb">
        <?php $breadcount = count($breadcrumbs) - 1; ?>
        <?php foreach ($breadcrumbs as $key => $breadcrumb) : ?>
            <?php if ($key != $breadcount) : ?>
                <li><a href="<?= $breadcrumb['href'] ?>"><?= $breadcrumb['text'] ?></a></li>
            <?php else : ?>
                <li class="active"><?= $breadcrumb['text'] ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <h1><?= $heading_title ?></h1>

    <div class="row">
        <?php if ($subcategory_left) : ?>
            <?php if ($column_left || $categories) : ?>
                <div class="col-sm-4 col-md-3" id="category-column-left">
                    <?php if ($categories) : ?>
                        <div class="panel panel-default">
                            <div class="panel-heading"><i class="fa fa-level-down"></i>&nbsp;&nbsp;<?= $text_refine ?></div>
                            <div class="list-group">
                                <?php foreach ($categories as $category) : ?>
                                    <a href="<?= $category['href'] ?>" class="list-group-item"><?= $category['name'] ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?= $column_left ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <?= $column_left ?>
        <?php endif; ?>

        <?php if (($column_left && $column_right) || ($column_right && ($categories && $subcategory_left))) { ?>
            <?php $twocols = true; ?>
            <?php $class = 'col-sm-4 col-md-6'; ?>
        <?php } elseif ($column_left || $column_right || ($categories && $subcategory_left)) { ?>
            <?php $twocols = false; ?>
            <?php $class = 'col-sm-8 col-md-9'; ?>
        <?php } else { ?>
            <?php $twocols = false; ?>
            <?php $class = 'col-sm-12'; ?>
        <?php } ?>

        <div id="content" class="<?= $class ?>">
			<?= $content_top ?>

            <?php if ($description_position) : ?>
			    <?php if ($thumb || $description) : ?>
                    <div class="well red-links">
                        <?php if ($thumb) : ?>
                            <div class="pull-left"><img src="<?= $thumb ?>" alt="<?= $heading_title ?>" title="<?= $heading_title ?>" class="img-thumbnail" style="margin: 0 10px 5px 0" /></div>
                        <?php endif; ?>
                        <?php if ($description) : ?>
                            <?= $description ?>
                        <?php endif; ?>
                        <div class="clearfix"></div>
                    </div>
                <?php endif; ?>
			<?php endif; ?>

			<?php if (!$subcategory_left) : ?>
                <?php if ($categories) : ?>
                    <div class="well well-sm"><i class="fa fa-level-down"></i>&nbsp;&nbsp;<?= $text_refine ?></div>
                    <div class="row">
                        <?php foreach ($categories as $category) : ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div  class="thumbnail subcategory" title="<?= $category['name'] ?>">
                                    <a href="<?= $category['href'] ?>">
                                        <div class="pull-left">
                                            <?php if ($category['image']) : ?>
                                                <img src="<?= $category['image'] ?>" alt="<?= $category['name'] ?>" />
                                            <?php else : ?>
                                                <i class="fa fa-image no-image"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="name-wrapper">
                                            <div class="display-table">
                                                <div class="display-table-cell">
                                                    <h5><?= $category['name'] ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
			<?php endif; ?>

			<?php if ($products) : ?>
                <div class="well well-sm">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-4">
                            <div class="btn-group btn-group-justified"></div>
                            <div class="input-group">
                                <span class="input-group-addon" >
                                    <i class="fa fa-sort"></i>
                                    <span class="hidden-xs hidden-sm hidden-md <?= $twocols ? 'hidden-lg' : '' ?>"><?= $text_sort ?></span>
                                </span>
                                <select id="input-sort" class="form-control" onchange="location = this.value;">
                                    <?php foreach ($sorts as $sorts) : ?>
                                        <?php if ($sorts['value'] == $sort . '-' . $order) : ?>
                                            <option value="<?= $sorts['href'] ?>" selected="selected"><?= $sorts['text'] ?></option>
                                        <?php else : ?>
                                            <option value="<?= $sorts['href'] ?>"><?= $sorts['text'] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <br class="visible-xs" />
                        <div class="col-lg-4 col-md-3 col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-eye"></i>
                                    <span class="hidden-xs hidden-sm hidden-md <?= $twocols ? 'hidden-lg' : '' ?>"><?= $text_limit ?></span>
                                </span>
                                <select id="input-limit" class="form-control" onchange="location = this.value;">
                                    <?php foreach ($limits as $limits) : ?>
                                        <?php if ($limits['value'] == $limit) : ?>
                                            <option value="<?= $limits['href'] ?>" selected="selected"><?= $limits['text'] ?></option>
                                        <?php else : ?>
                                            <option value="<?= $limits['href'] ?>"><?= $limits['text'] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
			    </div>
                <div class="row">
                    <?php foreach ($products as $product) : ?>
                        <div class="product-layout product-list col-xs-12">
                            <div class="product-thumb thumbnail ">
                                <div>
                                    <div class="caption">
                                        <div><a href="<?= $product['href'] ?>"><?= $product['name'] ?></a></div>
                                        <div>Артикул: <?= $product['sku'] ?></div>
                                        <div>Модель: <?= $product['model'] ?></div>
                                        <div>Производитель: <?= $product['manufacturer'] ?></div>
                                        <div>Марка: <?= $product['jan'] ?></div>
                                        <div>Цена:
                                            <?php if (!$product['special']) : ?>
                                                <?= $product['price'] ?>
                                            <?php else : ?>
                                                <span class="price-old">&nbsp;<?= $product['price'] ?>&nbsp;</span>
                                                <span class="price-new"><?= $product['special'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group">
                                            <?php if ($product['quantity'] <= 0 || $disable_cart_button) : ?>
                                                <button class="btn btn-addtocart" type="button" disabled>
                                                    <span><?= $disable_cart_button_text ?></span>
                                                </button>
                                            <?php else : ?>
                                                <?php if ($product['price']) : ?>
                                                    <button class="btn btn-addtocart" type="button" onclick="cart.add('<?= $product['product_id'] ?>');">
                                                        <i class="fa fa-shopping-cart"></i>
                                                        <span class="hidden-xs hidden-sm hidden-md"><?= $button_cart ?></span>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <button class="btn btn-addtocart" type="button">
                                                <a href="/autoparts/search/<?= $product['ean'] ?>/<?= $product['mpn'] ?>">Аналоги</a>
                                            </button>
                                        </div>
                                        <?php if ($product['rating']) : ?>
                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                <?php if ($product['rating'] < $i) : ?>
                                                    <i class="fa fa-star"></i>
                                                <?php else : ?>
                                                    <i class="fa fa-star active"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="well well-sm">
                    <div class="row">
                        <div class="col-md-6"><div class="pagination-wrapper"><?= $pagination ?></div></div>
                        <div class="col-md-6 text-right-md"><div style="padding: 6px 0;"><?= $results ?></div></div>
                    </div>
                </div>
            <?php endif; ?>

			<?php if (!$description_position) : ?>
			    <?php if ($thumb || $description) : ?>
				    <div class="clearfix"></div>
                <?php endif; ?>
			<?php endif; ?>

            <?php if (!$categories && !$products) : ?>
                <p><?= $text_empty ?></p>
                <div class="buttons">
                    <div class="pull-right">
                        <a href="<?= $continue ?>" class="btn btn-primary"><?= $button_continue ?></a>
                    </div>
                </div>
            <?php endif; ?>

            <?= $content_bottom ?>

        </div>

        <?= $column_right ?>

    </div>
</div>

<script>
	function adddotdotdot($element) {
		$(".subcategory .name-wrapper").dotdotdot();
	}
	$(document).ready(adddotdotdot);
	$(window).resize(adddotdotdot);
</script>

<style>
    .product-layout:hover .btn-addtocart a{
        color: #fff;
    }
    .product-layout:hover .btn-addtocart:hover a, .product-layout:hover .btn-addtocart:active a, .product-layout:hover .btn-addtocart.active a{
        color: #fff;
        text-decoration: none;
    }
</style>

<?= $footer ?>