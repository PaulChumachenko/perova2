<?= $header ?>

<div class="container" itemscope itemtype="http://schema.org/Product">
	<ul class="breadcrumb" prefix:v="http://rdf.data-vocabulary.org/#">
		<?php $breadcount = count($breadcrumbs) - 1; ?>
		<?php $i = 0; ?>
        <?php foreach ($breadcrumbs as $key => $breadcrumb) : ?>
		    <?php $i++; ?>
            <?php if ($key != $breadcount) : ?>
                <li <?php if ($i > 1): ?> typeof="v:Breadcrumb"<?php endif; ?>>
                    <a href="<?= $breadcrumb['href'] ?>" <?php if ($i > 1): ?>rel="v:url" property="v:title"<?php endif; ?>><?= $breadcrumb['text'] ?></a>
                </li>
            <?php else : ?>
                <li class="active"><?= $breadcrumb['text'] ?></li>
		    <?php endif; ?>
        <?php endforeach; ?>
    </ul>

	<h3 itemprop="name"><?= $heading_title ?></h3>

    <div class="well well-sm">
		<div class="inline-info">
			<b>Код: </b>
            <span><?= empty($pc_tecdoc['code']) ? null : $pc_tecdoc['code'] ?></span>
		</div>
        <div class="inline-info">
			<b>Производитель: </b>
            <span data-pc-manufacturer="<?= empty($pc_tecdoc['manufacturer']) ? null : $pc_tecdoc['manufacturer'] ?>"><?= empty($pc_tecdoc['manufacturer']) ? null : $pc_tecdoc['manufacturer'] ?></span>
		</div>
        <div class="inline-info">
			<b>Наличие: </b>
            <span><?= empty($pc_tecdoc['available']) ? 0 : ($pc_tecdoc['available'] >= 4 ? '> 4' : $pc_tecdoc['available']) ?> шт.</span>
		</div>
        <div class="inline-info">
            <b>В пути: </b>
            <span><?= empty($pc_tecdoc['en_route']) ? 0 : $pc_tecdoc['en_route'] ?> шт.</span>
        </div>
        <div class="inline-info">
            <b>Применение: </b>
            <span><?= $isbn ?></span>
        </div>
        <div class="inline-info">
            <b>Идентификатор (TecDoc): </b>
            <span><?= empty($pc_tecdoc['article']) ? null : $pc_tecdoc['article'] ?></span>
        </div>
        <div class="inline-info">
            <b>Производитель (TecDoc): </b>
            <span><?= empty($pc_tecdoc['brand']) ? null : $pc_tecdoc['brand'] ?></span>
        </div>
        <?php if ($reward) : ?>
            <div class="inline-info">
                <b><?= $text_reward ?></b>
                <?= $reward ?>
            </div>
		<?php endif; ?>
        <?php if (!empty($pc_tecdoc['alts'])) : ?>
            <div class="inline-info">
                <b>Альтернативные номера: </b>
                <?= implode(', ', $pc_tecdoc['alts']) ?>
            </div>
		<?php endif; ?>
		<?php if ($review_status) : ?>
            <div class="inline-info-right">
                <span class="stars">
                    <?php if ($rating) : ?>
                        <span itemprop = "aggregateRating" itemscope itemtype = "http://schema.org/AggregateRating">
                            <meta itemprop='reviewCount' content='<?= preg_replace("/\D/","",$reviews) ?>' />
                            <meta itemprop='worstRating' content='1' />
                            <meta itemprop='bestRating' content='5' />
                            <meta itemprop='ratingValue' content='<?= $rating ?>' />
                        </span>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <?php if ($rating < $i) : ?>
                            <i class="fa fa-star"></i>
                        <?php else : ?>
                            <i class="fa fa-star active"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>
                <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click');  $('html, body').animate({ scrollTop: $('a[href=\'#tab-review\']').offset().top - 5}, 250); return false;"><?= $reviews ?></a>
            </div>
		<?php endif; ?>
	</div>
    
    <div class="row">
        
        <?= $column_left ?>
        
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-7">
                <div class="row">
                    <?php $sda_class = $short_description_off & $short_attribute_off & $social_likes_off ? 'col-lg-12' : 'col-lg-6'; ?>
                    <div class="<?= $sda_class ?>">

                        <div class="thumbnails">
                            <div class="main-image-wrapper">
                                <?php if ($pc_tecdoc['images']) : ?>
                                    <a href="<?= reset($pc_tecdoc['images']) ?>" class="cbx_imgs pc_category_main_img ">
                                        <div class="prevphoto" style="background-image:url('<?= reset($pc_tecdoc['images']) ?>');"></div>
                                    </a>
                                    <?php foreach($pc_tecdoc['images'] as $src): ?>
                                        <?php if ($src == reset($pc_tecdoc['images'])) continue; ?>
                                        <a href="<?= $src ?>" class="cbx_imgs"></a>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="pc_category_main_img"><div class="pc_noimage"></div></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
						<?php if ($thumb || $images) : ?>
                            <div class="thumbnails">
                                <?php if ($thumb) : ?>
                                    <div class="main-image-wrapper">
                                        <a class="main-image" href="<?= $popup ?>" title="<?= $heading_title ?>" data-number="0">
                                            <img itemprop="image" src="<?= $thumb ?>" title="<?= $heading_title ?>" alt="<?= $heading_title ?>" class="img-responsive center-block" />
                                        </a>
                                    </div>
                                <?php endif; ?>
    
                                <?php if ($images) : ?>
                                    <div class="images-additional">
                                        <?php if ($thumb_small) : ?>
                                            <a class="thumbnail" href="<?= $popup ?>">
                                                <img src="<?= $thumb_small ?>" data-number="0"/>
                                            </a>
                                        <?php endif; ?>
                                        <?php $number = 1; ?>
                                        <?php foreach ($images as $image) : ?>
                                            <a class="thumbnail" href="<?= $image['popup'] ?>">
                                                <img src="<?= $image['thumb'] ?>" data-number="<?= $number ?>"/>
                                            </a>
                                            <?php $number++; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
						<?php endif; ?>
                    </div>
                    
                    <?php if (!$short_description_off || !$short_attribute_off || !$social_likes_off) : ?>
						<div class="col-lg-6 hidden-md hidden-sm hidden-xs">

                            <?php if (!empty($pc_tecdoc['analogs_href'])) : ?>
                                <button class="btn btn-addtocart" type="button">
                                    <a href="<?= $pc_tecdoc['analogs_href'] ?>">Показать Аналоги</a>
                                </button>
                            <?php endif; ?>

                            <?php if (!$short_description_off) : ?>
							    <h5><strong><?= $product_short_description_text ?></strong></h5>
							    <p>
                                    <?= utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, 330) . '... ' ?>
                                    <a href="" class="red-link" onclick="$('a[href=\'#tab-description\']').trigger('click'); $('html, body').animate({ scrollTop: $('a[href=\'#tab-description\']').offset().top - 6}, 250); return false;">
                                        <?= $product_read_more_text ?> &#8594;
                                    </a>
                                </p>
							<?php endif; ?>

							<?php if (!$short_attribute_off) : ?>
							    <?php if ($attribute_groups) : ?>
                                    <?php $i = 0; ?>
                                    <?php foreach ($attribute_groups as $attribute_group) : ?>
                                        <?php if ($i < 8) : ?>
                                            <h5><strong><?= $attribute_group['name'] ?></strong></h5>
                                            <table class="short-attr-table">
                                                <tbody>
                                                    <?php foreach ($attribute_group['attribute'] as $attribute) : ?>
                                                        <?php if ($i < 8) : ?>
                                                            <tr>
                                                                <td class="left"><span><?= $attribute['name'] ?></span></td>
                                                                <td class="right"><span><?= $attribute['text'] ?></span></td>
                                                            </tr>
                                                        <?php endif; ?>
                                                        <?php $i++ ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                        <?php $i++ ?>
                                    <?php endforeach; ?>
							    
                                    <p>...</p>
							        <p><button class="btn btn-sm btn-default" onclick="$('a[href=\'#tab-specification\']').trigger('click'); $('html, body').animate({ scrollTop: $('a[href=\'#tab-specification\']').offset().top - 2}, 250); return false;"><?= $product_all_specifications_text ?></button></p>
							    <?php endif; ?>
                                <br>
                            <?php endif; ?>

							<?php if (!$social_likes_off) : ?>
                                <div class="social-likes" style="margin-bottom:20px;">
                                    <div class="facebook" title="<?= $product_share_text ?> на Facebook">Facebook</div>
                                    <div class="twitter" title="<?= $product_share_text ?> в Twitter">Twitter</div>
                                    <div class="plusone" title="<?= $product_share_text ?> в Google+">Google+</div>
                                </div>
							<?php endif; ?>
						</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-5" id="product">
                <div class="panel panel-default">
                    <div class="panel-body">
						<div class="btn-group pull-right"></div>
                        
						<?php if ($price) : ?>
						    <div class="price" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">
							    <meta itemprop="priceCurrency" content="<?= $currency_code ?>" />
							    <?php if (!$special) : ?>
							        <meta itemprop="price" content="<?= preg_replace("/[^\d.]/","",rtrim($price, " \t.")) ?>" />
                                    <h2>
                                        <span><?= $price ?></span>
                                        <?php if ($tax) : ?>
                                            <span class="tax"><?= $text_tax ?> <?= $tax ?></span>
                                        <?php endif; ?>
                                        <?php if ($points) : ?>
                                            <span class="points"><?= $text_points ?> <strong><?= $points ?></strong></span>
                                        <?php endif; ?>
                                    </h2>
							    <?php else : ?>
							        <meta itemprop="price" content="<?= preg_replace("/[^\d.]/","",rtrim($special, " \t.")) ?>" />
                                    <h2>
                                        <span class="price-old">&nbsp;<?= $price; ?>&nbsp;</span>
                                        <span><?= $special ?></span>
                                        <?php if ($tax) : ?>
                                            <span class="tax"><?= $text_tax ?> <?= $tax ?></span>
                                        <?php endif; ?>
                                        <?php if ($points) : ?>
                                            <span class="points"><?= $text_points ?> <strong><?= $points ?></strong></span>
                                        <?php endif; ?>
                                    </h2>
							    <?php endif; ?>

                                <?php if ($discounts) : ?>
                                    <div class="alert-alt alert-info-alt">
                                        <?php foreach ($discounts as $discount) : ?>
                                            <div><strong><?= $discount['quantity'] ?></strong><?= $text_discount ?><strong><?= $discount['price']; ?></strong></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
						    </div>
						<?php endif; ?>
                        
                        <div class="alert-alt <?= $product_quantity <= 0 ? 'alert-danger-alt' : 'alert-success-alt' ?>">
                            <strong><?= $product_stock ?></strong>
                            <?php if ($config_stock_display & ($product_quantity > 0)) : ?>
                                <br /><?= $product_quantity_text ?>
                            <?php endif; ?>
					    </div>

					    <?php if ($minimum > 1) : ?>
                            <div class="alert-alt alert-warning-alt"><?= $text_minimum ?></div>
                        <?php endif; ?>
                        
                        <div class="options">
                            <?php if ($options) : ?>
                                <?php foreach ($options as $option) : ?>
                            
                                    <?php if ($option['type'] == 'select') : ?>
                                        <div class="form-group">
                                            <label class="control-label" for="input-option<?= $option['product_option_id'] ?>">
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text; ?>"></i>
                                                <?php endif; ?>
                                                <?= $option['name'] ?>
							                </label>
                                            <select name="option[<?= $option['product_option_id'] ?>]" id="input-option<?= $option['product_option_id'] ?>" class="form-control">
                                                <option value=""><?= $text_select ?></option>
                                                <?php foreach ($option['product_option_value'] as $option_value) : ?>
                                                    <option value="<?= $option_value['product_option_value_id'] ?>">
                                                        <?= $option_value['name'] ?>
                                                        <?php if ($option_value['price']) : ?>
                                                            (<?= $option_value['price_prefix'] ?><?= $option_value['price'] ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                            
                                    <?php if ($option['type'] == 'radio') : ?>
                                        <div class="form-group">
                                            <label class="control-label">
								                <?php if ($option['required']) : ?>
									                <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
								                <?php endif; ?>
								                <?= $option['name'] ?>
							                </label>
                                            <div id="input-option<?= $option['product_option_id'] ?>">
                                                <?php foreach ($option['product_option_value'] as $option_value) : ?>
                                                    <div class="radio-checbox-options">
                                                        <input type="radio" name="option[<?= $option['product_option_id'] ?>]" value="<?= $option_value['product_option_value_id'] ?>" id="<?= $option['product_option_id'] ?>_<?= $option_value['product_option_value_id'] ?>" />
                                                        <label for="<?= $option['product_option_id'] ?>_<?= $option_value['product_option_value_id'] ?>">
                                                            <span class="option-name"><?= $option_value['name'] ?></span>
                                                            <?php if ($option_value['price']) : ?>
                                                                <span class="option-price"><?= $option_value['price_prefix'] ?><?= $option_value['price'] ?></span>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                            
                                    <?php if ($option['type'] == 'checkbox') : ?>
                                        <div class="form-group">
                                            <label class="control-label">
								                <?php if ($option['required']) : ?>
									                <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text; ?>"></i>
								                <?php endif; ?>
								                <?= $option['name'] ?>
							                </label>
                                            <div id="input-option<?= $option['product_option_id'] ?>">
                                                <?php foreach ($option['product_option_value'] as $option_value) : ?>
                                                    <div class="radio-checbox-options">
                                                        <input type="checkbox" name="option[<?= $option['product_option_id'] ?>][]" value="<?= $option_value['product_option_value_id'] ?>" id="<?= $option['product_option_id'] ?>_<?= $option_value['product_option_value_id'] ?>" />
									                    <label for="<?= $option['product_option_id'] ?>_<?= $option_value['product_option_value_id'] ?>">
                                                            <span class="option-name"><?= $option_value['name'] ?></span>
                                                            <?php if ($option_value['price']) : ?>
                                                                <span class="option-price"><?= $option_value['price_prefix'] ?><?= $option_value['price'] ?></span>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                            
                                    <?php if ($option['type'] == 'image') : ?>
                                        <div class="form-group">
                                            <label class="control-label">
								                <?php if ($option['required']) : ?>
									                <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
								                <?php endif; ?>
								                <?= $option['name'] ?>
							                </label>
                                            <div id="input-option<?= $option['product_option_id'] ?>">
                                                <?php foreach ($option['product_option_value'] as $option_value) : ?>
                                                    <div class="image-radio">
                                                        <label>
                                                            <input type="radio" name="option[<?= $option['product_option_id'] ?>]" value="<?= $option_value['product_option_value_id'] ?>" />
                                                            <img src="<?= $option_value['image'] ?>" alt="<?= $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : '') ?>" class="img-thumbnail" data-toggle="tooltip" title="<?= $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : '') ?>" />
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                            
                                    <?php if ($option['type'] == 'text') : ?>
                                        <div class="form-group">
                                            <label class="control-label" for="input-option<?= $option['product_option_id'] ?>">
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
                                                <?php endif; ?>
                                                <?= $option['name'] ?>
                                            </label>
                                            <input type="text" name="option[<?= $option['product_option_id'] ?>]" value="<?= $option['value'] ?>" placeholder="<?= $option['name'] ?>" id="input-option<?= $option['product_option_id'] ?>" class="form-control" />
                                        </div>
                                    <?php endif; ?>
                            
                                    <?php if ($option['type'] == 'textarea') : ?>
                                        <div class="form-group">
                                            <label class="control-label" for="input-option<?= $option['product_option_id'] ?>">
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
                                                <?php endif; ?>
                                                <?= $option['name'] ?>
                                            </label>
                                            <textarea name="option[<?= $option['product_option_id'] ?>]" rows="5" placeholder="<?= $option['name'] ?>" id="input-option<?= $option['product_option_id'] ?>" class="form-control"><?= $option['value'] ?></textarea>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($option['type'] == 'file') : ?>
                                        <div class="form-group">
                                            <label class="control-label">
                                                <?= $option['name'] ?>
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="left" title="Tooltip on left"></i>
                                                <?php endif; ?>
                                            </label>
                                            <button type="button" id="button-upload<?= $option['product_option_id'] ?>" data-loading-text="<= $text_loading ?>" class="btn btn-default btn-block"><i class="fa fa-upload"></i> <?= $button_upload ?></button>
                                            <input type="hidden" name="option[<?= $option['product_option_id'] ?>]" value="" id="input-option<?= $option['product_option_id'] ?>" />
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($option['type'] == 'date') : ?>
                                        <div class="form-group">
                                            <label class="control-label" for="input-option<?= $option['product_option_id'] ?>">
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
                                                <?php endif; ?>
                                                <?= $option['name'] ?>
                                            </label>
                                            <div class="input-group date">
                                                <input type="text" name="option[<?= $option['product_option_id'] ?>]" value="<?= $option['value'] ?>" data-date-format="YYYY-MM-DD" id="input-option<?= $option['product_option_id'] ?>" class="form-control" />
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($option['type'] == 'datetime') : ?>
                                        <div class="form-group">
                                            <label class="control-label" for="input-option<?= $option['product_option_id'] ?>">
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
                                                <?php endif ?>
                                                <?= $option['name'] ?>
                                            </label>
                                            <div class="input-group datetime">
                                              <input type="text" name="option[<?= $option['product_option_id'] ?>]" value="<?= $option['value'] ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?= $option['product_option_id'] ?>" class="form-control" />
                                              <span class="input-group-btn">
                                                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                              </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($option['type'] == 'time') : ?>
                                        <div class="form-group">
                                            <label class="control-label" for="input-option<?= $option['product_option_id'] ?>">
                                                <?php if ($option['required']) : ?>
                                                    <i class="fa fa-exclamation-circle required" data-toggle="tooltip" data-placement="left" title="<?= $product_required_text ?>"></i>
                                                <?php endif; ?>
                                                <?= $option['name'] ?>
                                            </label>
                                            <div class="input-group time">
                                                <input type="text" name="option[<?= $option['product_option_id'] ?>]" value="<?= $option['value'] ?>" data-date-format="HH:mm" id="input-option<?= $option['product_option_id'] ?>" class="form-control" />
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if ($recurrings) : ?>
                                <hr>
                                <h3><?= $text_payment_recurring ?></h3>
                                <div class="form-group required">
                                    <select name="recurring_id" class="form-control">
                                        <option value=""><?= $text_select ?></option>
                                        <?php foreach ($recurrings as $recurring) : ?>
                                            <option value="<?= $recurring['recurring_id'] ?>"><?= $recurring['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="help-block" id="recurring-description"></div>
                                </div>
                            <?php endif; ?>
                        </div>

					    <?php if ($product_quantity > 0) : ?>
                            <div class="addcart">
                                <div class="row">
                                    <div class="col-lg-5 col-md-4 col-sm-12">
                                        <div class="input-group quantity" data-toggle="tooltip"  title="<?= $entry_qty ?>">
                                            <span class="input-group-addon quantity-plus-minus">
                                                <button type="button" id="plus" class="btn">+</button>
                                                <button type="button" id="minus" class="btn">-</button>
                                            </span>
                                            <input type="text" name="quantity" value="<?= $minimum ?>" size="2" id="input-quantity" class="form-control" />
                                        </div>
                                        <input type="hidden" name="product_id" value="<?= $product_id ?>" />
                                    </div>
                                    <div class="col-lg-7  col-md-8 col-sm-12">
                                        <?php if (($product_quantity <= 0) && $disable_cart_button) : ?>
                                            <button type="button" id="button-cart" data-loading-text="<?= $text_loading ?>" class="btn btn-block btn-default " disabled><?= $disable_cart_button_text ?></button>
                                        <?php else : ?>
                                            <button type="button" id="button-cart" data-loading-text="<?= $text_loading ?>" class="btn btn-block btn-danger "><?= $button_cart ?></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
					</div>
                </div>
            </div>

            <div class="col-sm-12">
                <?php if (!$related_product_position) : ?>
					<?php if ($products) : ?>
					    <div class="panel panel-default box-product related-products">
						    <div class="panel-heading"><i class="glyphicon glyphicon-link"></i>&nbsp;&nbsp;<?= $text_related ?></div>
                            <div class="panel-body" id="related-products">
                                <?php foreach ($products as $product) : ?>
                                    <div class="product-item">
                                        <div class="image">
                                            <a href="<?= $product['href'] ?>"><img src="<?= $product['thumb'] ?>" alt="<?= $product['name'] ?>" title="<?= $product['name'] ?>" class="img-responsive" /></a>
                                            <?php if ($product['special']) : ?>
                                                <?php $new_price = preg_replace("/[^0-9]/", '', $product['special']); ?>
                                                <?php $old_price = preg_replace("/[^0-9]/", '', $product['price']); ?>
                                                <?php $total_discount = round(100 - ($new_price / $old_price) * 100); ?>
                                                <span class="sticker st-sale">-<?= $total_discount ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="caption">
                                            <h4><a href="<?= $product['href'] ?>"><?= $product['name'] ?></a></h4>
                                            <?php if ($product['price']) : ?>
                                                <div class="price">
                                                    <?php if (!$product['special']) : ?>
                                                        <?= $product['price'] ?>
                                                    <?php else : ?>
                                                        <span class="price-old">&nbsp;<?= $product['price'] ?>&nbsp;</span>
                                                        <span class="price-new"><?= $product['special'] ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($product['tax']) : ?>
                                                        <br /><span class="price-tax"><?= $text_tax ?> <?= $product['tax'] ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
					    </div>
					<?php endif; ?>
                <?php endif; ?>

                <ul class="nav nav-tabs product-tabs">
                    <li class="active"><a href="#tab-description" data-toggle="tab"><i class="fa fa-file-text-o"></i><span class="hidden-xs">&nbsp;&nbsp;<?= $tab_description ?></span></a></li>
                    <?php if ($attribute_groups) : ?>
                        <li><a href="#tab-specification" data-toggle="tab"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;&nbsp;<?= $tab_attribute ?></span></a></li>
                    <?php endif; ?>
                    <?php if ($review_status) : ?>
                        <li><a href="#tab-review" data-toggle="tab"><i class="fa fa-comment-o"></i><span class="hidden-xs">&nbsp;&nbsp;<?= $tab_review ?></span></a></li>
                    <?php endif; ?>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active red-links" id="tab-description" itemprop="description">
                        <?= $description ?>
                    </div>

                    <?php if ($attribute_groups) : ?>
                        <div class="tab-pane" id="tab-specification">
                            <table class="table table-bordered">
                                <?php foreach ($attribute_groups as $attribute_group) : ?>
                                    <thead>
                                        <tr>
                                            <td colspan="2"><strong><?= $attribute_group['name'] ?></strong></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attribute_group['attribute'] as $attribute) : ?>
                                            <tr>
                                                <td><?= $attribute['name'] ?></td>
                                                <td><?= $attribute['text'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($review_status) : ?>
                        <div class="tab-pane" id="tab-review">
                            <?php if ($review_guest) : ?>
								<a class="btn btn-default" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?= $text_write ?></a>
								<br><br>
								<div id="collapseOne" class="panel-collapse collapse">
									<div class="well riview-helper">
										<form class="form-horizontal">
											<div class="form-group required">
												<label class="col-sm-2 control-label" for="input-name"><?= $entry_name ?></label>
												<div class="col-sm-10">
													<input type="text" name="name" value="" id="input-name" class="form-control" />
												</div>
											</div>
											<div class="form-group required">
												<label class="col-sm-2 control-label" for="input-review"><?= $entry_review ?></label>
												<div class="col-sm-10">
													<textarea name="text" rows="5" id="input-review" class="form-control"></textarea>
												</div>
											</div>
											<div class="form-group required">
												<label class="col-sm-2 control-label"><?= $entry_rating ?></label>
												<div class="col-sm-10">
													<div class="prod-rat">
														<input id="rat1" type="radio" name="rating" value="1" /><label class="rat-star" for="rat1"><i class="fa fa-star"></i></label>
														<input id="rat2" type="radio" name="rating" value="2" /><label class="rat-star" for="rat2"><i class="fa fa-star"></i></label>
														<input id="rat3" type="radio" name="rating" value="3" /><label class="rat-star" for="rat3"><i class="fa fa-star"></i></label>
														<input id="rat4" type="radio" name="rating" value="4" /><label class="rat-star" for="rat4"><i class="fa fa-star"></i></label>
														<input id="rat5" type="radio" name="rating" value="5" /><label class="rat-star" for="rat5"><i class="fa fa-star"></i></label>
													</div>
													<script>
														$('.rat-star').hover(function () {
															$(this).prevAll('.rat-star').addClass('active');
															$(this).addClass('active');
														},function () {
															$(this).prevAll('.rat-star').removeClass('active');
															$(this).removeClass('active');
														});

														$('.rat-star').click(function(){
															$('.rat-star').each(function(){
																$(this).removeClass('checked');
																$(this).prevAll('.rat-star').removeClass('checked');
															});

															$(this).addClass('checked');
															$(this).prevAll('.rat-star').addClass('checked');
														});

													</script>
												</div>
											</div>

											<?= $captcha ?>

											<div class="form-group" style="margin-bottom: 0;">
												<div class="col-sm-10 col-sm-offset-2">
													<button type="button" id="button-review" data-loading-text="<?= $text_loading ?>" class="btn btn-primary"><?= $button_continue ?></button>
												</div>
											</div>
										</form>
									</div>
								</div>
                            <?php else : ?>
                                <div class="alert alert-warning"><i class="fa fa-warning"></i>&nbsp;&nbsp;<?= $text_login ?></div>
                            <?php endif; ?>
                            <div id="review"></div>
						</div>
                    <?php endif; ?>
                </div>

                <?php if ($related_product_position) : ?>
					<?php if ($products) : ?>
                        <div class="panel panel-default box-product related-products">
                            <div class="panel-heading"><i class="glyphicon glyphicon-link"></i>&nbsp;&nbsp;<?= $text_related ?></div>
                            <div class="panel-body" id="related-products">
                                <?php foreach ($products as $product) : ?>
                                    <div class="product-item">
                                        <div class="image">
                                            <a href="<?= $product['href'] ?>"><img src="<?= $product['thumb'] ?>" alt="<?= $product['name'] ?>" title="<?= $product['name'] ?>" class="img-responsive" /></a>
                                            <?php if ($product['special']) : ?>
                                                <?php $new_price = preg_replace("/[^0-9]/", '', $product['special']); ?>
                                                <?php $old_price = preg_replace("/[^0-9]/", '', $product['price']); ?>
                                                <?php $total_discount = round(100 - ($new_price / $old_price) * 100); ?>
                                                <span class="sticker st-sale">-<?= $total_discount ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="caption">
                                            <h4><a href="<?= $product['href'] ?>"><?= $product['name'] ?></a></h4>
                                            <?php if ($product['price']) : ?>
                                                <div class="price">
                                                    <?php if (!$product['special']) : ?>
                                                        <?= $product['price'] ?>
                                                    <?php else : ?>
                                                        <span class="price-old">&nbsp;<?= $product['price'] ?>&nbsp;</span>
                                                        <span class="price-new"><?= $product['special'] ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($product['tax']) : ?>
                                                        <br /><span class="price-tax"><?= $text_tax ?> <?= $product['tax'] ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
					<?php endif; ?>
                <?php endif; ?>

            </div>
        </div>

        <?php if ($tags) : ?>
            <p style="margin-bottom: 20px;" class="red-links">
                <?= $text_tags ?>
                <?php for ($i = 0; $i < count($tags); $i++) : ?>
                    <?php if ($i < (count($tags) - 1)) : ?>
                        <a href="<?= $tags[$i]['href'] ?>"><?= $tags[$i]['tag'] ?></a>,
                    <?php else : ?>
                        <a href="<?= $tags[$i]['href'] ?>"><?= $tags[$i]['tag'] ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </p>
        <?php endif; ?>

        <div class="col-sm-12">
            <?= $content_bottom ?>
        </div>
    </div>

    <?= $column_right ?>

</div>

<script type="text/javascript">
$('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
	$.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#recurring-description').html('');
		},
		success: function(json) {
			$('.text-danger').remove();

			if (json['success']) {
				$('#recurring-description').html(json['success']);
			}
		}
	});
});

$('#button-cart').on('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
		},
		success: function(json) {
			$('.text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

			if (json['success']) {
				html  = '<div id="modal-cart" class="modal fade">';
					html += '  <div class="modal-dialog">';
					html += '    <div class="modal-content">';
					html += '      <div class="modal-body alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>';
					html += '    </div>';
					html += '  </div>';
					html += '</div>';

					$('body').append(html);

					$('#modal-cart').modal('show');

					setTimeout(function () {
						$('#cart-total').html(json['total']);
					}, 100);

				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}
		}, error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
	});
});

$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});

$('button[id^=\'button-upload\']').on('click', function() {
	var node = this;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('#review').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();

    $('#review').fadeOut('slow');

    $('#review').load(this.href);

    $('#review').fadeIn('slow');
});

$('#review').load('index.php?route=product/product/review&product_id=<?= $product_id ?>');

$('#button-review').on('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?= $product_id ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
			$('#button-review').button('loading');
		},
		complete: function() {
			$('#button-review').button('reset');
			$('#captcha').attr('src', 'index.php?route=tool/captcha#'+new Date().getTime());
			$('input[name=\'captcha\']').val('');
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();

			if (json['error']) {
				$('.riview-helper').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>');
			}

			if (json['success']) {
				$('.riview-helper').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>').remove();

				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').prop('checked', false);
				$('input[name=\'captcha\']').val('');
			}
		}
	});
});

$(document).ready(function() {
	$('.thumbnails .images-additional').magnificPopup({
		type:'image',
		delegate: 'a',
		gallery: {
			enabled:true
		}
	});

    $('.thumbnails .main-image').magnificPopup({
        type:'image'
    });

    $(".cbx_imgs").colorbox({ current:'', innerWidth:900, innerHeight:600, onComplete:function(){$('.cboxPhoto').unbind().click($.colorbox.next);} });
});

$('.images-additional img').click(function(){
	var oldsrc = $(this).attr('src'),
			newsrc = oldsrc.replace('<?= $img_small ?>','<?= $img_big ?>'),
			newhref = $(this).parent().attr('href'),
			number = $(this).attr('data-number');

	$('.main-image img').attr('src', newsrc);
	$('.main-image').attr('href', newhref);
	$('.main-image').attr('data-number', number);
	return false;
});


$('.thumbnails .main-image img').click(function(){
	if ($('.thumbnails .images-additional').length > 0) {
		var startnumber = $(this).parent().attr('data-number');
		$('.thumbnails .images-additional').magnificPopup('open', startnumber);
		return false
	} else {
		$(this).magnificPopup('open');
		return false
	}
});



		$('#related-products').owlCarousel({
			responsiveBaseWidth: '#related-products',
			itemsCustom: [[0, 1], [448, 2], [668, 3], [848, 4], [1000, 5]],
			theme: 'product-carousel',
			navigation: true,
			slideSpeed: 200,
			paginationSpeed: 300,
			autoPlay: false,
			stopOnHover: true,
			touchDrag: false,
			mouseDrag: false,
			navigationText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
			pagination: false,
		});

    $('.quantity-plus-minus #minus').click(function () {
        var $input = $('.quantity input[type="text"]');
        var count = parseInt($input.val()) - 1;
        count = count < <?= $minimum ?> ? <?= $minimum ?> : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $('.quantity-plus-minus #plus').click(function () {
        var $input = $('.quantity input[type="text"]');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
        });
</script>
<style>
    .content-wrapper .btn-addtocart:hover a, .content-wrapper .btn-addtocart:active a{
        color: #fff;
        text-decoration: none;
    }
    .pc_category_main_img{
        width: 300px;
        text-align:center;
        font-size:8px; color:#878787;
        margin:0 15px 20px 0;
        background-color:#fff;
        display: inline-block;
    }
    .prevphoto, .pc_noimage{
        width: auto;
        height: 300px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: 50% 50%;
        background-color: #fff;
    }
    .prevphoto:hover{
        border:1px solid #3A97C9;
        color:#CC2121;
        cursor:move;
    }
    .pc_noimage{
        background-image: url('/image/catalog/pc_part_no_image.png');
    }
    div.popover .popover-header {
        padding:8px 14px;
        background-color:#f7f7f7;
        border-bottom:1px solid #ebebeb;
        -webkit-border-radius:5px 5px 0 0;
        -moz-border-radius:5px 5px 0 0;
        border-radius:5px 5px 0 0;
    }
    div.popover .popover-title {
        margin:0;
        padding:0;
        background-color:transparent;
        border:none;
    }
    h3.popover-title {
        font-weight: bold;
    }
    div.popover {
        max-width: 450px !important;
    }
    .brand-container .pc-external-link {
        color: #0059b2;
        text-decoration: none;
        margin-bottom: 10px;
    }
    .brand-container .pc-external-link a {
        color: #0059b2;
    }
    .brand-container .pc-external-link a:hover {
        text-decoration: underline;
    }
    .brand-container .pc-description {
        margin-bottom: 10px;
    }
    .brand-container .pc-description {
        text-align: justify;
    }
</style>

<?= $footer ?>