<?= $header ?>
<?= $column_left ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-pc_autopart_brands" data-toggle="tooltip" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
                <i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $heading_title ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?= $action ?>" method="post" enctype="multipart/form-data" id="form-pc_autopart_brands" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?= $entry_status ?></label>
                        <div class="col-sm-10">
                            <select name="pc_autopart_brands_status" id="input-status" class="form-control">
                                <?php if ($pc_autopart_brands_status) : ?>
                                    <option value="1" selected="selected"><?= $text_status_enabled ?></option>
                                    <option value="0"><?= $text_status_disabled ?></option>
                                <?php else : ?>
                                    <option value="1"><?= $text_status_enabled ?></option>
                                    <option value="0" selected="selected"><?= $text_status_disabled ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>