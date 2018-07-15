<?= $header ?>
<?= $column_left ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?= $action_add ?>" data-toggle="tooltip" title="<?= $text_btn_add ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                <button type="button" data-toggle="tooltip" title="<?= $text_btn_delete; ?>" class="btn btn-danger" onclick="confirm('<?= $text_confirm ?>') ? $('#form-brands').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <?php if ($success) : ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?= $heading_title ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?= $action_delete ?>" method="post" enctype="multipart/form-data" id="form-brands">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td style="width: 1px;" class="text-center">
                                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
                                    </td>
                                    <td class="text-left" style="width: 12%;"><a href="<?= $sort_brand ?>" class="<?= mb_strtolower($order) ?>"><?= $text_column_brand ?></a></td>
                                    <td class="text-left" style="width: 19%;"><?= $text_column_website ?></td>
                                    <td class="text-left" style="width: 12%;"><?= $text_column_logo ?></td>
                                    <td class="text-left"><?= $text_column_description ?></td>
                                    <td class="text-right"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($brands)) : ?>
                                    <tr>
                                        <td class="text-center" colspan="6"><?= $text_no_results ?></td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($brands as $brand) : ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if (in_array($brand['id'], $selected)) : ?>
                                                    <input type="checkbox" name="selected[]" value="<?= $brand['id'] ?>" checked="checked"/>
                                                <?php else : ?>
                                                    <input type="checkbox" name="selected[]" value="<?= $brand['id'] ?>"/>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-left"><?= $brand['brand'] ?></td>
                                            <td class="text-left"><?php if ($brand['website']) : ?><a href="<?= $brand['website'] ?>"><?= $brand['website'] ?></a><?php endif; ?></td>
                                            <td class="text-left"><?php if ($brand['logo']) : ?> <img src="<?= $brand['thumb'] ?>" /><?php endif; ?></td>
                                            <td class="text-left"><?= html_entity_decode($brand['description']) ?></td>
                                            <td class="text-right">
                                                <a href="<?= $brand['edit'] ?>" data-toggle="tooltip" title="<?= $text_btn_edit ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6 text-left"><?= $pagination ?></div>
                    <div class="col-sm-6 text-right"><?= $results ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>