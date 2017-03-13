<?php
  use yii\helpers\Json;
  use yii\helpers\Url;

  $loggedUserId = Yii::$app->user->getId();
  $options =  Json::decode($component->config);
  $filters = \app\controllers\FilterController::getFiltersOfUser($loggedUserId);
  $contentTypes = array('table' => 'Table', 'lineChart' => 'Line chart', 'barChart' => 'Bar chart');

  printf("<div class='grid-item card %s' id='component_%s'>", $options['width'], $component->id);
?>

<!--Main content of component-->
<div class="card-content">
    <div class="card-header">
        <span class="card-title activator grey-text text-darken-4"><span class="nameTitle"><?php  echo $options['name']; ?></span><i class="material-icons right">more_vert</i></span>
    </div>
    <div class="card-body">

        <!--If no filter was added to component-->
        <?php if ($component->filter_id == null): ?>
        <div class="section center-align" id="componentContentBody<?= $component->id ?>">
            <a class="waves-effect waves-light btn-large" <?php printf("href='#modal%s'", $component->id) ?>><i class="material-icons right">add_circle_outline</i>Add content</a>
        </div>
        <?php endif; ?>

        <!-- Modal Structure -->
        <div class="modal" id="modal<?= $component->id ?>">
            <div class="modal-content">
                <h4>Content settings</h4>
                <form action="#" id="contentSettingsForm<?= $component->id ?>">
                    <div class="row">
                        <div class=" input-field col s12">
                            <input type="hidden" name="componentId" value="<?= $component->id ?>" />

                            <select id="componentFilterId<?= $component->id ?>" name="filterId">
                                <?php foreach ($filters as $filter):  ?>
                                    <option value="<?= $filter->id ?>"<?= $component->filter_id == $filter->id ? " selected='selected'" : "" ?>><?= $filter->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="componentFilterId<?= $component->id ?>">Filter Select</label>
                            <div class="help-block left-align">
                                <a href="<?= Url::to(["filter/create"]) ?>" >Create new filter</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="componenContentTypeId<?= $component->id ?>" name="contentTypeId">
                            <?php foreach ($contentTypes as $key => $type): ?>
                                <option value="<?= $key ?>"<?= $options["contentType"] ?? "" == $key ? " selected='selected'" : "" ?>><?= $type ?></option>
                            <?php endforeach; ?>
                            </select>
                            <label for="componenContentTypeId<?php print($component->id); ?>">Content type visualisation</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="left">
                    <button id="removeComponentContentBtn" data-id="<?= $component->id ?>" class="modal-action modal-close waves-effect waves-red btn-flat">Delete</button>
                </div>

                <div class="right">
                    <button id="saveComponentContentBtn" data-id="<?= $component->id ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Save</button>
                    <button class=" modal-close waves-effect waves-green btn-flat">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Component options-->
<div class="card-reveal">
    <div class="card-header light-blue accent-4">
        <span class="card-title white-text"><span class="nameTitle"><?php  echo $options['name']; ?></span> - options<i class="material-icons right">close</i></span>
    </div>
    <div class="card-body">
        <form class="row componentForm"  data-id="<?php  echo $component->id; ?>">
            <div class="input-field col s12">
                <label class="active" for="name">Name</label>
                <input class="nameInput" data-id="component_<?php  echo $component->id; ?>" onfocus="this.select();" onmouseup="return false;" id="name<?php  echo $component->id; ?>" type="text" value="<?php  echo $options['name']; ?>">
            </div>

            <div class="input-field col s12">
                <label class="active">Select width</label>
                <select id="width<?php  echo $component->id; ?>" class="widthSelect" data-id="component_<?php  echo $component->id; ?>">
                    <option <?= $options['width'] == '' ? ' selected="selected"' : '' ?> value="">25%</option>
                    <option <?= $options['width'] == 'width2' ? ' selected="selected"' : '' ?> value="width2">50%</option>
                    <option <?= $options['width'] == 'width3' ? ' selected="selected"' : '' ?> value="width3">75%</option>
                    <option <?= $options['width'] == 'width4' ? ' selected="selected"' : '' ?> value="width4">100%</option>
                </select>
            </div>

            <div class="input-field col s12 center-align">
                <a href="#modal<?= $component->id ?>" class="btn waves-effect waves-light blue">
                    Edit Content
                    <i class="material-icons right">edit</i>
                </a>
            </div>

            <div class="input-field col s12 center-align">
                <button type="button" class="deleteComponentBtn btn waves-effect waves-light red" data-id="<?php  echo $component->id; ?>">
                    Delete
                    <i class="material-icons right">delete</i>
                </button>
            </div>
        </form>
    </div>
</div>