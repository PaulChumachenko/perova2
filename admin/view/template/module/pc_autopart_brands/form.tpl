<?= $header ?>
<?= $column_left ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-brand" data-toggle="tooltip" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?= $cancel ?>" data-toggle="tooltip" title="<?= $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?= $heading_title ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <li><a href="<?= $breadcrumb['href'] ?>"><?= $breadcrumb['text'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) : ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?= $error_warning ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_action_brand ?></h3>
            </div>
            <div class="panel-body">
                <div style="margin-top: 25px;">
                    <form action="<?= $action ?>" method="post" enctype="multipart/form-data" id="form-brand" class="form-horizontal">
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="brand"><?= $entry_brand ?></label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" style = "padding: 0 15px 0 15px;">
                                            <select name="pc_autopart_brands[brand]" id="brand" class="form-control" value="<?= $model['brand'] ?>">
                                                <?php if(empty($model['brand'])) : ?>
                                                    <option disabled selected value> -- выберите производителя -- </option>
                                                <?php endif; ?>
                                                <?php foreach($brands_list as $option) : ?>
                                                    <option value="<?= $option['name'] ?>" <?= $option['name'] == $model['brand'] ? 'selected' : '' ?> >
                                                        <?= $option['name'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if ($error_brand) : ?><div class="text-danger"><?= $error_brand ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="website"><?= $entry_website ?></label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" style = "padding: 0 15px 0 15px;">
                                            <input type="text" class="form-control" id="website" name="pc_autopart_brands[website]" value="<?= $model['website'] ?>"  />
                                            <?php if ($error_website) : ?><div class="text-danger"><?= $error_website ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="website"><?= $entry_logo ?></label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" style = "padding: 0 15px 0 15px;">
                                            <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail">
                                                <img src="<?= $thumb ?>" data-placeholder="<?= $placeholder; ?>" />
                                            </a>
                                            <input type="hidden" name="pc_autopart_brands[logo]" value="<?= $model['logo'] ?>" id="input-image" />
                                            <?php if ($error_logo) : ?><div class="text-danger"><?= $error_logo ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="description"><?= $entry_description ?></label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" style = "padding: 0 15px 0 15px;">
                                        <textarea data-summernote id="description" cols="125" rows="2" name="pc_autopart_brands[description]">
                                            <?= $model['description'] ?>
                                        </textarea>
                                        <?php if ($error_description) : ?><div class="text-danger"><?= $error_description ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<?php if (version_compare(VERSION, "2.3", ">=")): ?>
    <script type="text/javascript" src="view/javascript/summernote/summernote.min.js"></script>
    <link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<?php endif; ?>
<script type="text/javascript">
    $('[data-summernote]').summernote({height: 300});
</script>